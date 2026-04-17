<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Models\ContractorWorker;
use App\Models\ProjectContractor;
use App\Models\WorkerBlacklist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public $totalPaginate = 10;
    public ?string $search = '';
    public ?string $projectStatusSelected = null;

    public $project_name, $contract_number, $start_date, $end_date;

    public $contract_document = null;
    public $selectedProject = null;
    public $selectedProjectId = null;

    protected function normalizeIdPayload($id): ?int
    {
        if (is_array($id)) {
            $id = $id['projectId'] ?? $id['id'] ?? null;
        }

        if ($id === null || $id === '') {
            return null;
        }

        return (int) $id;
    }

    public function detailProjectContract($id)
    {
        $project = ProjectContractor::with(['contractor'])
            ->withCount(['workers'])
            ->findOrFail($id);

        $project->submitted_and_approved_workers = ContractorWorker::where('project_contractor_id', $project->id)
            ->whereIn('status', ['submitted', 'approved'])
            ->count();

        $this->selectedProject = $project;
        $this->dispatch('showModalDetailProjectContract', selectedId: $project->id);
    }

    public function updatingTotalPaginate()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingProjectStatusSelected()
    {
        $this->resetPage();
    }

    public function mount()
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['administrator', 'contractor'], true)) {
            abort(403, 'Akses ditolak.');
        }
    }

    #[On('closeProject')]
    public function closeProject($id)
    {
        try {
            $project = ProjectContractor::with('workers')->findOrFail($id);

            if (Auth::user()->role !== 'administrator' && $project->contractor_id !== Auth::id()) {
                $this->dispatch('swal', title: 'Error', text: 'Anda tidak memiliki akses untuk menutup proyek ini.', icon: 'error');
                return;
            }

            if ($project->is_closed) {
                $this->dispatch('swal', title: 'Info', text: 'Proyek ini sudah ditutup sebelumnya.', icon: 'info');
                return;
            }

            DB::beginTransaction();

            $workers = ContractorWorker::where('project_contractor_id', $project->id)->get();

            foreach ($workers as $worker) {
                if (WorkerBlacklist::isNikBlacklisted($worker->nik)) {
                    WorkerBlacklist::updateOrCreate(
                        ['nik' => $worker->nik],
                        [
                            'full_name' => $worker->full_name,
                            'is_blacklisted' => true,
                            'blacklist_type' => 'permanent',
                            'blacklisted_until' => null,
                            'reason' => 'Sinkronisasi otomatis saat hard close project.',
                            'blacklisted_by' => Auth::id(),
                        ]
                    );
                }
            }

            ContractorWorker::where('project_contractor_id', $project->id)->delete();

            $project->is_closed = true;
            $project->closed_at = now();
            $project->closed_by = Auth::id();
            $project->save();

            DB::commit();
            $this->dispatch('swal', title: 'Berhasil', text: 'Proyek berhasil ditutup dan semua pekerja terkait telah dihapus.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error close project: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Gagal menutup proyek.', icon: 'error');
        }
    }

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

    public function editProjectContract($id)
    {
        $projectId = $this->normalizeIdPayload($id);
        if (!$projectId) {
            $this->dispatch('swal', title: 'Gagal', text: 'ID project tidak valid.', icon: 'error');
            return;
        }

        $project = ProjectContractor::with('contractor')->findOrFail($projectId);

        if (Auth::user()->role !== 'administrator' && $project->contractor_id !== Auth::id()) {
            $this->dispatch('swal', title: 'Error', text: 'Anda tidak memiliki akses untuk mengubah proyek ini.', icon: 'error');
            return;
        }

        $this->selectedProjectId = $project->id;
        $this->project_name = $project->project_name;
        $this->contract_number = $project->memo_number;
        $this->start_date = $project->start_date;
        $this->end_date = $project->end_date;

        $this->dispatch('showModalEditProjectContract', selectedId: $project->id);
    }

    public function updateProjectContract()
    {
        if (!$this->selectedProjectId) {
            $this->dispatch('swal', title: 'Gagal', text: 'Project belum dipilih.', icon: 'error');
            return;
        }

        $this->validate([
            'project_name' => 'required|string|max:255',
            'contract_number' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'contract_document' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $project = ProjectContractor::with('contractor')->findOrFail($this->selectedProjectId);
        $this->dispatch('confirmationUpdateProjectContract', data: $project, selectedId: $project->id);
    }

    #[On('confirmUpdateProjectContract')]
    public function confirmUpdateProjectContract($projectId)
    {
        $projectId = $this->normalizeIdPayload($projectId);
        if (!$projectId) {
            $this->dispatch('swal', title: 'Gagal', text: 'ID project tidak valid.', icon: 'error');
            return;
        }

        $project = ProjectContractor::with('contractor')->findOrFail($projectId);

        if (Auth::user()->role !== 'administrator' && $project->contractor_id !== Auth::id()) {
            $this->dispatch('swal', title: 'Error', text: 'Anda tidak memiliki akses untuk mengubah proyek ini.', icon: 'error');
            return;
        }

        DB::beginTransaction();

        try {
            $project->project_name = $this->project_name;
            $project->memo_number = $this->contract_number;
            $project->start_date = $this->start_date;
            $project->end_date = $this->end_date;

            if ($this->contract_document instanceof TemporaryUploadedFile) {
                $folderPath = 'contractor_documents/' . ($project->contractor->company_name ?? Auth::user()->company_name) . '/project_contracts';
                $documentFileName = time() . "_" . $this->contract_number . "_" . uniqid() . ".pdf";
                $this->contract_document->storeAs($folderPath, $documentFileName, 'custom_public');
                $project->memo_document = $folderPath . '/' . $documentFileName;
            }

            $project->save();
            DB::commit();

            $this->dispatch('successUpdateProjectContract');
            $this->dispatch('swal', title: 'Berhasil', text: 'Data project berhasil diperbarui.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error update project: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Gagal', text: 'Gagal memperbarui project.', icon: 'error');
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
                ->when($this->search, function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('project_name', 'like', '%' . $this->search . '%')
                            ->orWhere('memo_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->projectStatusSelected === 'closed', function ($query) {
                    $query->where('is_closed', true);
                })
                ->when($this->projectStatusSelected === 'active', function ($query) {
                    $query->where('is_closed', false);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->totalPaginate);
        }

        if (Auth::user()->role === 'contractor') {
            $project = ProjectContractor::with(['contractor'])
                ->where('contractor_id', Auth::id())
                ->when($this->search, function ($query) {
                    $query->where(function ($sub) {
                        $sub->where('project_name', 'like', '%' . $this->search . '%')
                            ->orWhere('memo_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->projectStatusSelected === 'closed', function ($query) {
                    $query->where('is_closed', true);
                })
                ->when($this->projectStatusSelected === 'active', function ($query) {
                    $query->where('is_closed', false);
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->totalPaginate);
        }

        if (Auth::user()->role === 'administrator') {
            return view('livewire.dashboard.admin.list-project', [
                'projects' => $project,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ]);
        }

        return view('livewire.dashboard.contractor.list-project-contract', [
            'projects' => $project,
        ]);
    }
}
