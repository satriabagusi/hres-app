<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Exports\DataPekerjaImport;
use App\Models\ContractorWorker;
use App\Models\MedicalReview;
use App\Models\ProjectContractor;
use App\Models\SecurityReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class ListDraftEmployee extends Component
{
    use WithFileUploads, WithPagination;
    public $paginationTheme = 'bootstrap';

    #[Url(as: 'project_contract_id')]
    public ?int $projectContractId = null;

    public $employee_xls = null;
    public array $parsedEmployees = [];

    #[Url(as: 'per_page')]
    public $totalPaginate = 10;
    public ?string $search = null;

    public $selectedEmployeeId = null;
    public $ktp_document = null;
    public $photo_document = null;
    public $mcu_document = null;
    public $form_b_document = null;
    public $age_justification_document = null;

    public $ktpUrl = null;
    public $photoUrl = null;
    public $mcuUrl = null;
    public $formBUrl = null;
    public $ageJustificationUrl = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTotalPaginate()
    {
        $this->resetPage();
    }

    public function uploadEmployee()
    {

        if (!$this->employee_xls instanceof TemporaryUploadedFile || !$this->employee_xls->exists()) {
            $this->dispatch('swal', title: 'Error', text: 'Silakan pilih file Excel terlebih dahulu.', icon: 'error');
            return;
        }

        $this->validate([
            'employee_xls' => 'file|mimes:xlsx,xls|max:2048', // Max 2MB
        ], [
            'employee_xls.file' => 'File harus berupa file.',
            'employee_xls.mimes' => 'File harus berupa file Excel (xlsx, xls).',
            'employee_xls.max' => 'Ukuran file terlalu besar. Maksimal 2MB.',
        ]);



        DB::beginTransaction();

        try {
            $import = new DataPekerjaImport();
            Excel::import($import, $this->employee_xls);

            $this->parsedEmployees = $import->getData();

            sleep(1.5); // Simulasi proses upload

            foreach ($this->parsedEmployees as $employee) {
                ContractorWorker::create([
                    'project_contractor_id' => $this->projectContractId,
                    'full_name' => $employee['nama_lengkap'],
                    'nik' => $employee['nik'],
                    'birth_place' => $employee['tempat_lahir'],
                    'birth_date' => $employee['tanggal_lahir'],
                    'position' => $employee['jabatan'],
                    'jenis_kelamin' => $employee['jenis_kelamin'],
                    'status' => 'draft',
                    'domicile' => $employee['domisili'],
                ]);
            }
            $this->employee_xls = null; // Reset file input
            $this->parsedEmployees = []; // Reset parsed data

            $this->dispatch('uploadSuccess'); // Trigger success event
            $this->dispatch('swal', title: 'Sukses', text: 'Data pekerja berhasil diunggah.', icon: 'success');

            DB::commit();
        } catch (\Exception $e) {
            $this->dispatch('swal', title: 'Error', text: $e->getMessage(), icon: 'error');
            DB::rollBack();
        }
    }

    public function viewDocument($id)
    {
        $employee = ContractorWorker::find($id);
        $this->selectedEmployeeId = $id;

        if (!$employee) {
            $this->dispatch('swal', title: 'Error', text: 'Pekerja tidak ditemukan.', icon: 'error');
            return;
        }

        $this->ktpUrl = $employee->ktp_document ? asset('uploads/employee_documents/' . $employee->ktp_document) : null;
        $this->photoUrl = $employee->photo ? asset('uploads/employee_documents/' . $employee->photo) : null;
        $this->formBUrl = $employee->form_b_document ? asset('uploads/employee_documents/' . $employee->form_b_document) : null;

        if ($employee->age_justification_document && Carbon::parse($employee->birth_date)->age >= 56) {
            $this->ageJustificationUrl = $employee->age_justification_document ? asset('uploads/employee_documents/' . $employee->age_justification_document) : null;
        }

        $this->dispatch(
            'viewDocumentModal',
            full_name: $employee->full_name,
            nik: $employee->nik,
            birth_place: $employee->birth_place,
            birth_date: $employee->birth_date,
            position: $employee->position,
            jenis_kelamin: $employee->jenis_kelamin,
            ktpUrl: $this->ktpUrl,
            photoUrl: $this->photoUrl,
            formbUrl: $this->form_b_document,
            ageJustificationUrl: $employee->age_justification_document,
            employeeAge: Carbon::parse($employee->birth_date)->age,
            domicile: $employee->domicile,
        );
    }

    public function uploadDocument()
    {
        if ($this->selectedEmployeeId === null) {
            $this->dispatch('swal', title: 'Error', text: 'Silakan pilih pekerja terlebih dahulu.', icon: 'error');
            return;
        }

        $employee = ContractorWorker::find($this->selectedEmployeeId);

        if (!$employee) {
            $this->dispatch('swal', title: 'Error', text: 'Pekerja tidak ditemukan.', icon: 'error');
            return;
        }

        // Hitung usia pekerja
        $age = \Carbon\Carbon::parse($employee->birth_date)->age;
        $ageDocRequired = $age > 56;

        if (
            !$this->ktp_document &&
            !$this->photo_document &&
            !$this->form_b_document &&
            (!$this->age_justification_document && !$ageDocRequired)
        ) {
            $this->dispatch('swal', title: 'Error', text: 'Silakan upload minimal satu dokumen.', icon: 'error');
            return;
        }

        if ($ageDocRequired && !$this->age_justification_document) {
            $this->dispatch('swal', title: 'Error', text: 'Pekerja di atas 56 tahun wajib upload dokumen justifikasi usia.', icon: 'error');
            return;
        }

        $this->validate([
            'ktp_document' => 'nullable|file|mimes:pdf|max:2048',
            'photo_document' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'form_b_document' => 'nullable|file|mimes:pdf|max:2048',
            'age_justification_document' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'ktp_document.file' => 'KTP harus berupa file.',
            'ktp_document.mimes' => 'KTP harus berupa file PDF.',
            'ktp_document.max' => 'Ukuran KTP terlalu besar. Maksimal 2MB.',
            'photo_document.file' => 'Foto harus berupa file gambar.',
            'photo_document.mimes' => 'Foto harus berupa file gambar (jpg, jpeg, png).',
            'photo_document.max' => 'Ukuran foto terlalu besar. Maksimal 2MB.',
            'form_b_document.file' => 'MCU harus berupa file.',
            'form_b_document.mimes' => 'MCU harus berupa file PDF.',
            'form_b_document.max' => 'Ukuran MCU terlalu besar. Maksimal 2MB.',
            'age_justification_document.file' => 'Justifikasi usia harus berupa file.',
            'age_justification_document.mimes' => 'Justifikasi usia harus berupa file PDF.',
            'age_justification_document.max' => 'Ukuran justifikasi usia terlalu besar. Maksimal 2MB.',
        ]);

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($this->selectedEmployeeId);

            if (!$employee) {
                $this->dispatch('swal', title: 'Error', text: 'Pekerja tidak ditemukan.', icon: 'error');
                return;
            }

            $destinationPath = public_path('uploads/employee_documents');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            if ($this->ktp_document) {
                // Hapus file lama
                if ($employee->ktp_document && File::exists($destinationPath . '/' . $employee->ktp_document)) {
                    File::delete($destinationPath . '/' . $employee->ktp_document);
                }

                $ktpFileName = time() . "_" . uniqid() . '.' . $this->ktp_document->getClientOriginalExtension();
                $storeKtp = File::move($this->ktp_document->getRealPath(), $destinationPath . '/' . $ktpFileName);
                if (!$storeKtp) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file KTP.', icon: 'error');
                    return;
                }
                $employee->ktp_document = $ktpFileName;
            }

            if ($this->photo_document) {
                if ($employee->photo && File::exists($destinationPath . '/' . $employee->photo)) {
                    File::delete($destinationPath . '/' . $employee->photo);
                }

                $photoFileName = time() . "_" . uniqid() . '.' . $this->photo_document->getClientOriginalExtension();
                $storePhoto = File::move($this->photo_document->getRealPath(), $destinationPath . '/' . $photoFileName);
                if (!$storePhoto) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file foto.', icon: 'error');
                    return;
                }
                $employee->photo = $photoFileName;
            }

            if ($this->form_b_document) {
                if ($employee->form_b_document && File::exists($destinationPath . '/' . $employee->form_b_document)) {
                    File::delete($destinationPath . '/' . $employee->form_b_document);
                }

                $formBFileName = time() . "_" . uniqid() . '.' . $this->form_b_document->getClientOriginalExtension();
                $storeFormB = File::move($this->form_b_document->getRealPath(), $destinationPath . '/' . $formBFileName);
                if (!$storeFormB) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file Form B.', icon: 'error');
                    return;
                }
                $employee->form_b_document = $formBFileName;
            }

            if ($this->age_justification_document) {
                if ($employee->age_justification_document && File::exists($destinationPath . '/' . $employee->age_justification_document)) {
                    File::delete($destinationPath . '/' . $employee->age_justification_document);
                }

                $ageJustificationFileName = time() . "_" . uniqid() . '.' . $this->age_justification_document->getClientOriginalExtension();
                $storeAgeJustification = File::move($this->age_justification_document->getRealPath(), $destinationPath . '/' . $ageJustificationFileName);
                if (!$storeAgeJustification) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file justifikasi usia.', icon: 'error');
                    return;
                }
                $employee->age_justification_document = $ageJustificationFileName;
            }

            $employee->save();

            DB::commit();

            $this->dispatch('swal', title: 'Sukses', text: 'Dokumen berhasil diunggah.', icon: 'success');
            $this->dispatch('uploadSucceed');
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', title: 'Error', text: $e->getMessage(), icon: 'error');
        }
    }

    #[On('submitAllEmployee')]
    public function submitAllEmployee()
    {
        DB::beginTransaction();

        try {
            $data = ContractorWorker::where('project_contractor_id', $this->projectContractId)
                ->where('status', 'draft')
                ->whereNotNull('ktp_document')
                ->whereNotNull('photo')
                ->whereNotNull('form_b_document')
                ->get();

            if ($data->isEmpty()) {
                $this->dispatch('swal', title: 'Error', text: 'Tidak ada pekerja yang dapat diajukan. Pastikan semua dokumen telah diunggah.', icon: 'error');
                return;
            }

            $submittedCount = 0;

            foreach ($data as $employee) {
                $age = Carbon::parse($employee->birth_date)->age;

                // Skip jika umur >= 56 dan tidak ada justifikasi
                if ($age >= 56 && !$employee->age_justification_document) {
                    continue;
                }

                // Skip jika umur < 18 (opsional keamanan)
                if ($age < 18) {
                    continue;
                }

                // Proses submit
                MedicalReview::updateOrCreate(
                    ['worker_id' => $employee->id],
                    [
                        'reviewed_by' => Auth::id(),
                        'user_id' => Auth::id(),
                        'status' => 'on_review',
                        'reviewed_at' => now(),
                    ]
                );

                SecurityReview::updateOrCreate(
                    ['worker_id' => $employee->id],
                    [
                        'reviewed_by' => Auth::id(),
                        'user_id' => Auth::id(),
                        'status' => 'on_review',
                        'reviewed_at' => now(),
                    ]
                );

                $employee->status = 'submitted';
                $employee->save();
                $submittedCount++;
            }

            DB::commit();

            if ($submittedCount === 0) {
                $this->dispatch('swal', title: 'Info', text: 'Tidak ada pekerja yang memenuhi syarat untuk diajukan.', icon: 'info');
            } else {
                $this->dispatch('swal', title: 'Sukses', text: "$submittedCount pekerja berhasil diajukan.", icon: 'success');
            }

            $this->resetPage();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error submitting all employees: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Terjadi kesalahan saat mengajukan pekerja.', icon: 'error');
        }
    }

    #[On('submitEmployee')]
    public function submitEmployee($id)
    {

        $project_contractor = ProjectContractor::find($this->projectContractId);
        // dd($project_contractor);

        DB::beginTransaction();

        try {
            $employee = ContractorWorker::find($id);
            $employee->status = 'submitted';
            $employee->save();

            MedicalReview::updateOrCreate(
                ['worker_id' => $employee->id],
                [
                    'reviewed_by' => Auth::id(),
                    'user_id' => Auth::id(),
                    'status' => 'on_review',
                    'reviewed_at' => now(),
                    'expiry_date' => $project_contractor->end_date
                ]
            );

            SecurityReview::updateOrCreate(
                ['worker_id' => $employee->id],
                [
                    'reviewed_by' => Auth::id(),
                    'user_id' => Auth::id(),
                    'status' => 'on_review',
                    'reviewed_at' => now(),
                ]
            );

            DB::commit();

            $this->dispatch('swal', title: 'Sukses', text: 'Pekerja berhasil diajukan.', icon: 'success');
            $this->resetPage();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Error submitting employee: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Terjadi kesalahan saat mengajukan pekerja.', icon: 'error');
        }
    }

    public function deleteDraft($id)
    {
        $employee = ContractorWorker::find($id);

        $this->dispatch('confirmDeleteModal', data: $employee);
        return;
    }

    #[On('deleteEmployeeDraft')]
    public function deleteEmployeeDraft($id){
        $employee = ContractorWorker::find($id);

        // check if has document on it and delete the document
        if($employee->ktp_document){
            if(File::hash('uploads/employee_documents/'.$employee->ktp_document)){
                File::delete('uploads/employee_documents/'.$employee->ktp_document);
                Log::info('Deleting Employee | Ktp document found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }else{
                Log::info('Deleting Employee | No ktp document found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }
        }

        if($employee->photo){
            if(File::hash('uploads/employee_documents/'.$employee->photo)){
                File::delete('uploads/employee_documents/'.$employee->photo);
                Log::info('Deleting Employee | Photo found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }else{
                Log::info('Deleting Employee | No photo found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }
        }

        if($employee->form_b_document){
            if(File::hash('uploads/employee_documents/'.$employee->form_b_document)){
                File::delete('uploads/employee_documents/'.$employee->form_b_document);
                Log::info('Deleting Employee | Form b document found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }else{
                Log::info('Deleting Employee | No form b document found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
            }
        }

        // Delete Medical Review and Security Review if exist
        if($employee->medical_review){
            $employee->medical_review->delete();
            Log::info('Deleting Employee | Medical review found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
        }

        if($employee->security_review){
            $employee->security_review->delete();
            Log::info('Deleting Employee | Security review found for employee: '.$employee->full_name."(".$employee->user->company_name.")");
        }

        $employee->delete();
        $this->dispatch('swal', title: 'Berhasil', text: 'Pekerja berhasil dihapus.', icon: 'success');
        $this->resetPage();
    }


    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'Upload Pekerja',
            'employee_active' => 'active',
            // 'patient_add_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {

        $employees = ContractorWorker::whereHas('project_contractor', function ($query) {
            $query->where('contractor_id', Auth::id());
        })
            ->where('status', 'draft')
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate($this->totalPaginate)
            ->withQueryString(); // Penting agar query param tetap saat pagination


        return view('livewire.dashboard.contractor.list-draft-employee', [
            'employees' => $employees,
        ]);
    }
}
