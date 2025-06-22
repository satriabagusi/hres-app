<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\ContractorWorker;
use App\Models\MedicalReview;
use App\Models\SecurityReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\LaravelPdf\Facades\Pdf;

class ListEmployee extends Component
{

    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $mcu_document = null;
    public $hazard_status = null;
    public $notes = null;
    public $selected_employee;
    public $selected_employee_id;
    public $fit_status = null;
    public $expiry_date;

    public $no_badge_induction;
    public $no_badge_security;

    public function modalUploadMCU($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::find($id);

        $this->dispatch('showModalUploadMCU');
    }

    public function uploadMCUFile()
    {
        $this->validate([
            'mcu_document' => 'file|mimes:pdf|max:2048',
            'hazard_status' =>  ['required', Rule::in(['low_risk', 'medium_risk', 'high_risk'])],
            'fit_status' => ['required', Rule::in(['fit', 'unfit', 'fit_with_note', 'follow_up'])],
            'notes' => [
                'nullable',
                'string',
                Rule::requiredIf(function () {
                    return in_array($this->fit_status, ['unfit', 'follow_up']);
                })
            ],
            'expiry_date' => 'required|date|after:today',
        ], [
            'mcu_document.mimes' => 'File harus berformat PDF',
            'mcu_document.max' => 'Ukuran file terlalu besar. Maksimal 2MB.',
            'mcu_document.file' => 'Harus berupa file',
            'hazard_status.required' => 'Status bahaya tidak boleh kosong.',
            'hazard_status.in' => 'Status bahaya tidak valid.',
            'fit_status.required' => 'Status fit tidak boleh kosong.',
            'fit_status.in' => 'Status fit tidak valid.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.required_if' => 'Catatan tidak boleh kosong.',
            'expiry_date.required' => 'Tanggal Berlaku MCU tidak boleh kosong.',
            'expiry_date.after' => 'Tanggal Berlaku MCU harus setelah hari ini.',
        ]);

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($this->selected_employee_id);
            $medical_review_employee = MedicalReview::where('worker_id', $this->selected_employee_id)->first();

            if (!$this->mcu_document instanceof TemporaryUploadedFile) {
                $this->dispatch('swal', title: 'Error', text: 'Silakan pilih file terlebih dahulu.', icon: 'error');
                return;
            }


            $destinationPath = public_path('uploads/employee_documents');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            if ($this->mcu_document) {
                $mcuDocsName = time() . "_" . uniqid() . '.' . $this->mcu_document->getClientOriginalExtension();
                $storeMcuDocs = File::move($this->mcu_document->getRealPath(), $destinationPath . '/' . $mcuDocsName);
                if (!$storeMcuDocs) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file foto.', icon: 'error');
                    return;
                }

                File::delete(public_path('uploads/employee_documents/' . $medical_review_employee->mcu_document));
                $medical_review_employee->mcu_document = $mcuDocsName;
                $medical_review_employee->reviewed_by = Auth::user()->id;
                $medical_review_employee->risk_notes = $this->hazard_status;
                $medical_review_employee->status_mcu = $this->fit_status;
                $medical_review_employee->notes = $this->notes;
                $medical_review_employee->expiry_date = $this->expiry_date;
                $medical_review_employee->status = 'approved';
            }

            $medical_review_employee->save();

            $this->dispatch('uploadMCUSuccess');
            $this->dispatch('swal', title: 'Success', text: 'Berhasil mengunggah dokumen MCU.', icon: 'success');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Error', text: 'Gagal mengunggah dokumen MCU.', icon: 'error');
        }
    }

    #[On('rejectMcu')]
    public function rejectMcu($id, $alasan, $keterangan)
    {

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($id);
            $medical_review_employee = MedicalReview::where('worker_id', $id)->first();

            $medical_review_employee->status = 'rejected';
            $medical_review_employee->status_mcu = $alasan;
            $medical_review_employee->notes = $keterangan;
            $medical_review_employee->save();

            $this->dispatch('swal', title: 'Success', text: 'Berhasil menolak dokumen MCU.', icon: 'success');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Error', text: 'Gagal menolak dokumen MCU.', icon: 'error');
        }
    }

    public function detailData($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalDetail', data: $this->selected_employee);
    }

    public function modalVerificationHSE($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalVerificationHSE', data: $this->selected_employee, type: 'hse');
    }

    #[On('submitVerificationHSE')]
    public function submitVerificationHSE($id, $no_induction)
    {

        // find no_induction can't be redundant
        $redundant = ContractorWorker::where('induction_card_number', $no_induction)->first();

        if ($redundant) {
            $this->dispatch('swal', title: 'Error', text: 'Nomor Induction sudah terdaftar.', icon: 'error');
            return;
        }

        // check if no_induction is empty, space inputted or null | remove space first
        if (trim($no_induction) == '' || $no_induction == null) {
            $this->dispatch('swal', title: 'Error', text: 'Nomor Induction tidak boleh kosong.', icon: 'error');
        }


        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($id);
            $employee->induction_card_number = $no_induction;

            $employee->save();

            $this->dispatch('swal', title: 'Success', text: 'Berhasil Verifikasi HSE Induction.', icon: 'success');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Error', text: 'Gagal Verifikasi HSE Induction.', icon: 'error');
        }
    }

    public function modalVerificationSecurity($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalVerificationSecurity', data: $this->selected_employee, type: 'security');
    }

    #[On('submitVerificationSecurity')]
    public function submitVerificationSecurity($id, $no_id_security)
    {

        // dd($id, $no_id_security);

        // find no_id_security can't be redundant
        $redundant = ContractorWorker::where('security_card_number', $no_id_security)->first();

        if ($redundant) {
            $this->dispatch('swal', title: 'Error', text: 'Nomor ID Security sudah terdaftar.', icon: 'error');
            return;
        }

        // check if no_id_security is empty, space inputted or null | remove space first
        if (trim($no_id_security) == '' || $no_id_security == null) {
            $this->dispatch('swal', title: 'Error', text: 'Nomor ID Security tidak boleh kosong.', icon: 'error');
        }

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($id);
            $employee->security_card_number = $no_id_security;

            $security_reviews = SecurityReview::where('worker_id', $id)
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => Auth::user()->id,
                    'notes' => 'ok',
                ]);

            $employee->save();

            $this->dispatch('swal', title: 'Success', text: 'Berhasil Verifikasi Security.', icon: 'success');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Error', text: 'Gagal Verifikasi Security.', icon: 'error');
        }
    }

    public function alasanRejectMcu($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalAlasanRejectMcu', data: $this->selected_employee);
    }

    #[On('deleteEmployee')]
    public function deleteEmployee($id)
    {
        if (Auth::user()->role === 'administrator') {
            DB::beginTransaction();
            try {
                $employee = ContractorWorker::find($id);

                // Hapus dokumen pekerja jika ada

                if ($employee->medical_review) {
                    if ($employee->medical_review->mcu_document) {
                        File::delete(public_path('uploads/employee_documents/' . $employee->medical_review->mcu_document));
                    }
                } else {
                    Log::info('Deleting Employee | No medical review found for employee: ' . $employee->full_name);
                }

                if ($employee->ktp_document) {
                    File::delete(public_path('uploads/employee_documents/' . $employee->ktp_document));
                } else {
                    Log::info('Deleting Employee | No ktp document found for employee: ' . $employee->full_name);
                }

                if ($employee->photo) {
                    File::delete(public_path('uploads/employee_documents/' . $employee->photo));
                } else {
                    Log::info('Deleting Employee | No photo found for employee: ' . $employee->full_name);
                }

                if ($employee->form_b_document) {
                    File::delete(public_path('uploads/employee_documents/' . $employee->form_b_document));
                } else {
                    Log::info('Deleting Employee | No form b document found for employee: ' . $employee->full_name);
                }

                $employee->delete();

                $this->dispatch('swal', title: 'Success', text: 'Berhasil menghapus data.', icon: 'success');
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error($th);
                $this->dispatch('swal', title: 'Error', text: 'Gagal menghapus data.', icon: 'error');
            }
        }
    }

    #[On('printIdBadge')]
    public function printIdBadge($id)
    {
        // Ambil data pekerja dengan relasi
        $employee = ContractorWorker::with([
            'medical_review',
            'security_review',
            'user',
            'project_contractor'
        ])->findOrFail($id); // Tidak perlu cek !$employee karena findOrFail sudah otomatis 404

        // Validasi medical & security review
        if (
            $employee->medical_review->status !== 'approved' ||
            $employee->security_review->status !== 'approved'
        ) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Data pekerja belum diverifikasi.',
                'icon' => 'error'
            ]);
            return;
        }

        // Validasi status draft
        if ($employee->status === 'draft') {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Data pekerja belum diajukan.',
                'icon' => 'error'
            ]);
            return;
        }

        // Set status ke approved jika belum
        if ($employee->status !== 'approved') {
            $employee->status = 'approved';
            $employee->save();
        }

        // Generate URL untuk print (area tidak dipakai di sini karena PDF ambil dari route)
        $url = route('print-view.employee-id-badge', ['id' => $id]);

        // dd($url);

        // Kirim URL ke frontend
        $this->dispatch('printBadge', url: $url);
    }


    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'List Pekerja',
            'contractor_data' => 'active',
            'employee_data_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {
        $employees = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            // get data pekerja dengan status submitted, approved, dan rejected
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        return view('livewire.dashboard.admin.list-employee', [
            'employees' => $employees
        ]);
    }
}
