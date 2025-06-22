<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Models\ProjectContractor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ListProjectContract extends Component
{

    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public ?string $search = '';

    public $project_name, $contract_number, $start_date, $end_date;

    public $contract_document = null;

    public function addProjectContract()
    {
        if (!$this->contract_document instanceof TemporaryUploadedFile) {
            $this->dispatch('swal', title: 'Error', text: 'Silakan upload minimal satu dokumen.', icon: 'error');
            return;
        }
    
        $this->validate([
            'project_name' => 'required|string|max:255',
            'contract_number' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_document' => 'file|mimes:pdf|max:2048', // max 2MB
        ], [
            'project_name.required' => 'Nama proyek harus diisi.',
            'contract_number.required' => 'Nomor kontrak/memo harus diisi.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'end_date.required' => 'Tanggal selesai harus setelah tanggal mulai.',
            'contract_document.file' => 'Dokumen kontrak harus berupa file.',
            'contract_document.mimes' => 'Dokumen kontrak harus berupa file PDF.',
            'contract_document.max' => 'Ukuran dokumen kontrak maksimal 2MB.',
        ]);
    
        DB::beginTransaction();
    
        try {
            $folderPath = 'contractor_documents/' . Auth::user()->company_name . '/project_contracts';
            $documentFileName = time() . "_" . $this->contract_number . "_" . uniqid() . ".pdf";
    
            // Simpan file ke public/ folder via disk custom_public
            $this->contract_document->storeAs($folderPath, $documentFileName, 'custom_public');
    
            // Simpan data kontrak ke DB
            $projectContract = ProjectContractor::create([
                'contractor_id' => Auth::id(),
                'project_name' => $this->project_name,
                'memo_number' => $this->contract_number,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'memo_document' => $folderPath . '/' . $documentFileName,
            ]);
    
            DB::commit();
    
            $this->dispatch('swal', title: 'Berhasil', text: 'Kontrak proyek berhasil dibuat.', icon: 'success');
            $this->dispatch('successAddProjectContract');
            $this->reset([
                'project_name',
                'contract_number',
                'start_date',
                'end_date',
                'contract_document'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat membuat kontrak proyek: ' . $e->getMessage(), icon: 'error');
        }
    }


    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'List Kontrak Proyek',
            'contract_active' => 'active',
            // 'employee_data_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {

        if (Auth::user()->role === 'administrator') {
            $project = ProjectContractor::with(['contractor'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        if (Auth::user()->role === 'contractor') {
            $project = ProjectContractor::with(['contractor'])
                ->where('contractor_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('livewire.dashboard.contractor.list-project-contract', [
            'projects' => $project,
        ]);
    }
}
