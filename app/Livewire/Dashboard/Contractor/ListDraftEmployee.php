<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Exports\DataPekerjaImport;
use App\Models\ContractorWorker;
use App\Models\MedicalReview;
use App\Models\ProjectContractor;
use App\Models\SecurityReview;
use App\Models\WorkerBlacklist;
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
    public bool $projectIsClosed = false;
    public $ktp_document = null;
    public $skck_document = null;
    public $photo_document = null;
    public $mcu_document = null;
    public $form_b_document = null;
    public $age_justification_document = null;

    public $ktpUrl = null;
    public $skckUrl = null;
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

        $project = ProjectContractor::find($this->projectContractId);
        if (!$project || $project->is_closed) {
            $this->dispatch('swal', title: 'Error', text: 'Proyek sudah ditutup. Tidak bisa upload pekerja baru.', icon: 'error');
            return;
        }

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
                $isBlacklisted = WorkerBlacklist::isNikBlacklisted($employee['nik']);
                if ($isBlacklisted) {
                    throw new \Exception("NIK {$employee['nik']} ({$employee['nama_lengkap']}) sedang dalam blacklist aktif dan tidak dapat didaftarkan.");
                }

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
        $this->skckUrl = $employee->skck_document ? asset('uploads/employee_documents/' . $employee->skck_document) : null;
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
            skckUrl: $this->skckUrl,
            photoUrl: $this->photoUrl,
            formbUrl: $this->formBUrl,
            ageJustificationUrl: $employee->age_justification_document,
            employeeAge: Carbon::parse($employee->birth_date)->age,
            domicile: $employee->domicile,
        );
    }

    public function confirmSubmitEmployee($id)
    {
        $project = ProjectContractor::find($this->projectContractId);
        if (!$project || $project->is_closed) {
            $this->dispatch('swal', title: 'Error', text: 'Proyek sudah ditutup. Tidak bisa mengajukan pekerja.', icon: 'error');
            return;
        }

        $this->dispatch('confirmSubmitEmployeeModal', employeeId: (int) $id);
    }

    public function uploadDocument()
    {
        $project = ProjectContractor::find($this->projectContractId);
        if (!$project || $project->is_closed) {
            $this->dispatch('swal', title: 'Error', text: 'Proyek sudah ditutup. Tidak bisa upload dokumen.', icon: 'error');
            return;
        }

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
            !$this->skck_document &&
            !$this->photo_document &&
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
            'skck_document' => 'nullable|file|mimes:pdf|max:2048',
            'photo_document' => 'nullable|image|mimes:jpg,jpeg,png|dimensions:ratio=3/4|max:2048',
            'age_justification_document' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'ktp_document.file' => 'KTP harus berupa file.',
            'ktp_document.mimes' => 'KTP harus berupa file PDF.',
            'ktp_document.max' => 'Ukuran KTP terlalu besar. Maksimal 2MB.',
            'skck_document.file' => 'SKCK harus berupa file.',
            'skck_document.mimes' => 'SKCK harus berupa file PDF.',
            'skck_document.max' => 'Ukuran SKCK terlalu besar. Maksimal 2MB.',
            'photo_document.image' => 'Foto harus berupa file gambar yang valid.',
            'photo_document.mimes' => 'Foto harus berupa file gambar (jpg, jpeg, png).',
            'photo_document.dimensions' => 'Rasio foto wajib 3:4 agar sesuai format ID Card.',
            'photo_document.max' => 'Ukuran foto terlalu besar. Maksimal 2MB.',
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
                $storeKtp = File::move($this->ktp_document->getPathname(), $destinationPath . '/' . $ktpFileName);
                if (!$storeKtp) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file KTP.', icon: 'error');
                    return;
                }
                $employee->ktp_document = $ktpFileName;
            }

            if ($this->photo_document) {
                if (!str_starts_with($this->photo_document->getClientOriginalName(), 'cropped_')) {
                    $this->dispatch('swal', title: 'Error', text: 'Foto wajib di-crop terlebih dahulu sebelum upload.', icon: 'error');
                    return;
                }

                if ($employee->photo && File::exists($destinationPath . '/' . $employee->photo)) {
                    File::delete($destinationPath . '/' . $employee->photo);
                }

                $photoFileName = 'cropped_' . time() . "_" . uniqid() . '.' . $this->photo_document->getClientOriginalExtension();
                $storePhoto = File::move($this->photo_document->getPathname(), $destinationPath . '/' . $photoFileName);
                if (!$storePhoto) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file foto.', icon: 'error');
                    return;
                }
                $employee->photo = $photoFileName;
            }

            if ($this->skck_document) {
                if ($employee->skck_document && File::exists($destinationPath . '/' . $employee->skck_document)) {
                    File::delete($destinationPath . '/' . $employee->skck_document);
                }

                $skckFileName = time() . "_" . uniqid() . '.' . $this->skck_document->getClientOriginalExtension();
                $storeSkck = File::move($this->skck_document->getPathname(), $destinationPath . '/' . $skckFileName);
                if (!$storeSkck) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file SKCK.', icon: 'error');
                    return;
                }
                $employee->skck_document = $skckFileName;
            }

            if ($this->form_b_document) {
                if ($employee->form_b_document && File::exists($destinationPath . '/' . $employee->form_b_document)) {
                    File::delete($destinationPath . '/' . $employee->form_b_document);
                }

                $formBFileName = time() . "_" . uniqid() . '.' . $this->form_b_document->getClientOriginalExtension();
                $storeFormB = File::move($this->form_b_document->getPathname(), $destinationPath . '/' . $formBFileName);
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
                $storeAgeJustification = File::move($this->age_justification_document->getPathname(), $destinationPath . '/' . $ageJustificationFileName);
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
        $project = ProjectContractor::find($this->projectContractId);
        if (!$project || $project->is_closed) {
            $this->dispatch('swal', title: 'Error', text: 'Proyek sudah ditutup. Tidak bisa mengajukan pekerja.', icon: 'error');
            return;
        }

        DB::beginTransaction();

        try {
            $data = ContractorWorker::where('project_contractor_id', $this->projectContractId)
                ->where('status', 'draft')
                ->whereNotNull('ktp_document')
                ->whereNotNull('skck_document')
                ->whereNotNull('photo')
                ->where('photo', 'like', 'cropped_%')
                ->get();

            if ($data->isEmpty()) {
                $this->dispatch('swal', title: 'Error', text: 'Tidak ada pekerja yang dapat diajukan. Pastikan semua dokumen telah diunggah.', icon: 'error');
                return;
            }

            $submittedCount = 0;

            foreach ($data as $employee) {
                $isBlacklisted = WorkerBlacklist::isNikBlacklisted($employee->nik);
                if ($isBlacklisted) {
                    $employee->status = 'rejected';
                    $employee->save();
                    continue;
                }

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
        if (!$project_contractor || $project_contractor->is_closed) {
            $this->dispatch('swal', title: 'Error', text: 'Proyek sudah ditutup. Tidak bisa mengajukan pekerja.', icon: 'error');
            return;
        }
        // dd($project_contractor);

        DB::beginTransaction();

        try {
            $employee = ContractorWorker::find($id);

            if (!$employee) {
                $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan.', icon: 'error');
                return;
            }

            if (WorkerBlacklist::isNikBlacklisted($employee->nik)) {
                $employee->status = 'rejected';
                $employee->save();
                $this->dispatch('swal', title: 'Error', text: 'Pekerja masuk blacklist aktif dan tidak dapat diajukan.', icon: 'error');
                DB::commit();
                return;
            }

            if (!$employee->ktp_document || !$employee->skck_document || !$employee->photo) {
                $this->dispatch('swal', title: 'Error', text: 'KTP, SKCK, dan Foto wajib dilengkapi sebelum pengajuan.', icon: 'error');
                DB::rollBack();
                return;
            }

            if (!str_starts_with((string) $employee->photo, 'cropped_')) {
                $this->dispatch('swal', title: 'Error', text: 'Foto wajib hasil crop sebelum pekerja dapat diajukan.', icon: 'error');
                DB::rollBack();
                return;
            }

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

        if (!$employee) {
            $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan atau sudah terhapus.', icon: 'error');
            return;
        }

        $companyName = optional(optional($employee->project_contractor)->contractor)->company_name ?? '-';
        $basePath = public_path('uploads/employee_documents/');

        $deleteIfExists = function (?string $fileName, string $label) use ($basePath, $employee, $companyName) {
            if (!$fileName) {
                return;
            }

            $filePath = $basePath . $fileName;
            if (File::exists($filePath)) {
                File::delete($filePath);
                Log::info('Deleting Employee | ' . $label . ' found for employee: ' . $employee->full_name . '(' . $companyName . ')');
            } else {
                Log::info('Deleting Employee | No ' . $label . ' found for employee: ' . $employee->full_name . '(' . $companyName . ')');
            }
        };

        // Check worker documents and delete physical files safely.
        $deleteIfExists($employee->ktp_document, 'KTP document');
        $deleteIfExists($employee->photo, 'Photo');
        $deleteIfExists($employee->form_b_document, 'Form B document');
        $deleteIfExists($employee->skck_document, 'SKCK document');
        $deleteIfExists($employee->age_justification_document, 'Age justification document');

        // Delete Medical Review and Security Review if exist
        if($employee->medical_review){
            $employee->medical_review->delete();
            Log::info('Deleting Employee | Medical review found for employee: '.$employee->full_name."(".$companyName.")");
        }

        if($employee->security_review){
            $employee->security_review->delete();
            Log::info('Deleting Employee | Security review found for employee: '.$employee->full_name."(".$companyName.")");
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

        $employees = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->whereHas('project_contractor', function ($query) {
            $query->where('contractor_id', Auth::id());
        })
            ->where('status', 'draft')
            ->when($this->projectContractId, function ($query) {
                $query->where('project_contractor_id', $this->projectContractId);
            })
            ->whereNotIn('nik', WorkerBlacklist::query()->active()->select('nik'))
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate($this->totalPaginate)
            ->withQueryString(); // Penting agar query param tetap saat pagination

        if ($this->projectContractId) {
            $project = ProjectContractor::where('id', $this->projectContractId)
                ->where('contractor_id', Auth::id())
                ->first();
            $this->projectIsClosed = (bool) ($project?->is_closed);
        } else {
            $this->projectIsClosed = false;
        }

        $activeBlacklistedNiks = WorkerBlacklist::query()
            ->active()
            ->whereIn('nik', $employees->getCollection()->pluck('nik')->unique()->toArray())
            ->pluck('nik')
            ->flip();

        $employees->getCollection()->transform(function ($employee) use ($activeBlacklistedNiks) {
            $employee->is_blacklisted_active = isset($activeBlacklistedNiks[$employee->nik]);
            return $employee;
        });


        return view('livewire.dashboard.contractor.list-draft-employee', [
            'employees' => $employees,
        ]);
    }
}
