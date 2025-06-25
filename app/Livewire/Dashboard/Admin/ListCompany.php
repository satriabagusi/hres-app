<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ListCompany extends Component
{
    use WithPagination;
    // template bootstrap
    protected $paginationTheme = 'bootstrap';

    public $pdfFileName = null;
    public $selectedCompanyName = null;
    public $selectedCompanyId = null;

    public ?string $search = '';
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDocument($id)
    {
        $company = User::findOrFail($id);
        if (!$company->document_contractor) {
            session()->flash('error', 'Dokumen tidak ditemukan.');
            return;
        }
        $this->selectedCompanyName = $company->company_name;
        $this->selectedCompanyId = $id;
        $this->pdfFileName = 'uploads/contractor_documents/' . $company->document_contractor;
    }

    public function approveModal($id)
    {
        $company = User::findOrFail($id);
        $this->dispatch('approvalDocumentModal',
            title: 'Konfirmasi Persetujuan',
            text: "Setujui perusahaan <b>{$company->company_name} </b>?",
            confirmText: 'Setujui',
            cancelText: 'Batal',
            id: $id
        );
    }

    #[On('approveDocument')]
    public function approveDocument($id)
    {
        // dd($id);

        DB::beginTransaction();
        try {
            $company = User::findOrFail($id);
            // if (!$company->document_contractor) {
            //     $this->dispatch('swal', title: 'Gagal', text: 'Dokumen tidak ditemukan.', icon: 'error');
            //     return;
            // }

            $company->status = 'approved';
            $company->save();

            $this->dispatch('swal', title: 'Berhasil', text: 'Perusahaan telah disetujui.', icon: 'success');
            $this->resetPage();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat menyetujui perusahaan.' . $th->getMessage(), icon: 'error');
            // Log the error
            Log::error('Error approving document: ' . $th->getMessage());
        }
    }

    public function rejectModal($id)
    {
        $company = User::findOrFail($id);
        $this->dispatch('rejectDocumentModal',
            title: 'Konfirmasi Penolakan',
            text: "Tolak perusahaan <b>{$company->company_name} </b>?",
            confirmText: 'Tolak',
            cancelText: 'Batal',
            id: $id
        );
    }

    #[On('rejectDocument')]
    public function rejectDocument($id)
    {
        DB::beginTransaction();
        try {
            $company = User::findOrFail($id);

            $company->status = 'rejected';
            $company->save();

            $this->dispatch('swal', title: 'Berhasil', text: 'Dokumen perusahaan telah ditolak.', icon: 'success');
            $this->resetPage();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat menolak dokumen perusahaan.' . $th->getMessage(), icon: 'error');
            // Log the error
            Log::error('Error rejecting document: ' . $th->getMessage());
        }
    }

    public function mount(){
        // Ensure the user is authenticated and has the correct role
        if (!Auth::check() || Auth::user()->role !== 'administrator') {
            return redirect()->route('home');
        }
    }


    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'List Perusahaan',
            'contractor_data' => 'active',
            'company_data_active' => 'active'
        ]
    )]
    #[Title('List Perusahaan')]
    public function render()
    {

        if ($this->search) {
            $companies = User::where('role', 'contractor')
                ->where(function ($query) {
                    $query->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);
        }
        else {
            $companies = User::where('role', 'contractor')
                ->paginate(10);
        }

        return view('livewire.dashboard.admin.list-company', [
            'companies' => $companies,
        ]);
    }
}
