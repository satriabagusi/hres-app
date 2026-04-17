<?php

namespace App\Livewire\Dashboard\Admin;

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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelPdf\Facades\Pdf;

class ListEmployee extends Component
{

    use WithFileUploads, WithPagination;
    protected $paginationTheme = 'bootstrap';

    public ?string $search = null;
    public string $filter = 'All';
    public array $listFilter = [];
    public $filterSelected = null;
    public $statusSelected = null;
    public int $totalPaginate = 10;
    public $expandedRowId = null;

    public $mcu_document = null;
    public $hazard_status = null;
    public $notes = null;
    public $selected_employee;
    public $selected_employee_id;
    public $fit_status = null;
    public $edit_employee_name = null;
    public $edit_employee_nik = null;
    public $edit_employee_position = null;
    public $transfer_employee_name = null;
    public $transfer_employee_company = null;
    public $transfer_employee_project = null;
    public ?int $transfer_project_contractor_id = null;
    public ?string $transfer_position = null;

    public $no_badge_induction;
    public $no_badge_security;
    public ?string $security_badge_number = null;

    public function modalUploadMCU($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['project_contractor.contractor'])->find($id);
        $this->hazard_status = 'low_risk';
        $this->fit_status = 'fit';
        $this->notes = null;

        $this->dispatch('showModalUploadMCU', data: $this->selected_employee);
    }

    public function toggleDetail($id): void
    {
        $this->expandedRowId = $this->expandedRowId === $id ? null : $id;
    }

    public function confirmPrintIdBadge($id): void
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalPrintIdBadge', data: $this->selected_employee);
    }

    public function deleteConfirmation($id): void
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalDeleteConfirmation', data: $this->selected_employee);
    }

    public function uploadMCUFile()
    {
        try {
            $this->validate([
                'mcu_document' => 'nullable|file|mimes:pdf|max:2048',
                'hazard_status' =>  ['required', Rule::in(['low_risk', 'medium_risk', 'high_risk'])],
                'fit_status' => ['required', Rule::in(['fit', 'unfit', 'follow_up'])],
                'notes' => [
                    'nullable',
                    'string',
                    Rule::requiredIf(function () {
                        return in_array($this->fit_status, ['unfit', 'follow_up']);
                    })
                ],
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
            ]);
        } catch (ValidationException $exception) {
            $firstError = $exception->validator->errors()->first() ?: 'Validasi gagal. Periksa kembali input verifikasi.';
            $this->dispatch('swal', title: 'Validasi Gagal', text: $firstError, icon: 'error');
            throw $exception;
        }

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::find($this->selected_employee_id);
            $medical_review_employee = MedicalReview::where('worker_id', $this->selected_employee_id)->first();

            if (!$employee || !$medical_review_employee) {
                $this->dispatch('swal', title: 'Error', text: 'Data pekerja atau review medical tidak ditemukan.', icon: 'error');
                return;
            }

            if ($this->mcu_document instanceof TemporaryUploadedFile) {
                $destinationPath = public_path('uploads/employee_documents');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $mcuDocsName = time() . "_" . uniqid() . '.' . $this->mcu_document->getClientOriginalExtension();
                $storeMcuDocs = File::move($this->mcu_document->getRealPath(), $destinationPath . '/' . $mcuDocsName);
                if (!$storeMcuDocs) {
                    $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file foto.', icon: 'error');
                    return;
                }

                File::delete(public_path('uploads/employee_documents/' . $medical_review_employee->mcu_document));
                $medical_review_employee->mcu_document = $mcuDocsName;
            }

            $medical_review_employee->reviewed_by = Auth::user()->id;
            $medical_review_employee->risk_notes = $this->hazard_status;
            $medical_review_employee->status_mcu = $this->fit_status;
            $medical_review_employee->notes = $this->notes;
            $medical_review_employee->status = in_array($this->fit_status, ['unfit', 'follow_up'], true) ? 'rejected' : 'approved';

            $medical_review_employee->save();

            $this->dispatch('uploadMCUSuccess');
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

    public function editEmployee(): void
    {
        if (!$this->selected_employee_id) {
            return;
        }

        $employee = ContractorWorker::with(['project_contractor.contractor'])
            ->find($this->selected_employee_id);

        if (!$employee) {
            return;
        }

        $this->edit_employee_name = $employee->full_name;
        $this->edit_employee_nik = $employee->nik;
        $this->edit_employee_position = $employee->position;
        $this->selected_employee = $employee;

        $this->dispatch('showModalEditEmployee', data: $employee);
    }

    public function updateEmployeeDetail(): void
    {
        if (!$this->selected_employee_id) {
            return;
        }

        $this->validate([
            'edit_employee_name' => 'required|string|max:255',
            'edit_employee_nik' => 'required|string|max:32',
            'edit_employee_position' => 'required|string|max:255',
        ]);

        $employee = ContractorWorker::find($this->selected_employee_id);

        if (!$employee) {
            return;
        }

        $employee->full_name = $this->edit_employee_name;
        $employee->nik = $this->edit_employee_nik;
        $employee->position = $this->edit_employee_position;
        $employee->save();

        $this->selected_employee = $employee->fresh(['medical_review', 'security_review', 'project_contractor.contractor']);

        $this->dispatch('hideModalEditEmployee');
        $this->dispatch('swal', title: 'Berhasil', text: 'Data pekerja berhasil diperbarui.', icon: 'success');
    }

    public function modalTransferEmployee($id): void
    {
        $employee = ContractorWorker::with(['project_contractor.contractor'])->find($id);

        if (!$employee) {
            $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan.', icon: 'error');
            return;
        }

        $this->selected_employee_id = $employee->id;
        $this->selected_employee = $employee;
        $this->transfer_employee_name = $employee->full_name;
        $this->transfer_employee_company = $employee->project_contractor?->contractor?->company_name;
        $this->transfer_employee_project = $employee->project_contractor?->project_name;
        $this->transfer_project_contractor_id = $employee->project_contractor_id;
        $this->transfer_position = $employee->position;

        $this->dispatch('showModalTransferEmployee', data: $employee, selectedProjectId: $this->transfer_project_contractor_id);
    }

    public function submitTransferEmployee(): void
    {
        if (Auth::user()->role !== 'administrator') {
            $this->dispatch('swal', title: 'Error', text: 'Hanya administrator yang dapat memindahkan pekerja.', icon: 'error');
            return;
        }

        $this->validate([
            'transfer_project_contractor_id' => [
                'required',
                'integer',
                Rule::exists('project_contractors', 'id')->where(function ($query) {
                    $query->where('is_closed', false);
                }),
            ],
            'transfer_position' => ['required', 'string', 'max:255'],
        ], [
            'transfer_project_contractor_id.required' => 'Project tujuan wajib dipilih.',
            'transfer_project_contractor_id.exists' => 'Project tujuan tidak valid atau sudah ditutup.',
            'transfer_position.required' => 'Jabatan baru wajib diisi.',
        ]);

        DB::beginTransaction();

        try {
            $employee = ContractorWorker::with(['project_contractor.contractor'])->lockForUpdate()->find($this->selected_employee_id);

            if (!$employee) {
                DB::rollBack();
                $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan.', icon: 'error');
                return;
            }

            $targetProject = ProjectContractor::with('contractor')
                ->where('is_closed', false)
                ->find($this->transfer_project_contractor_id);

            if (!$targetProject) {
                DB::rollBack();
                $this->dispatch('swal', title: 'Error', text: 'Project tujuan tidak valid atau sudah ditutup.', icon: 'error');
                return;
            }

            if ((int) $employee->project_contractor_id === (int) $targetProject->id && trim((string) $employee->position) === trim((string) $this->transfer_position)) {
                DB::rollBack();
                $this->dispatch('swal', title: 'Info', text: 'Pekerja sudah berada pada project dan jabatan yang sama.', icon: 'info');
                return;
            }

            $employee->project_contractor_id = $targetProject->id;
            $employee->position = $this->transfer_position;
            $employee->save();

            $this->selected_employee = $employee->fresh(['medical_review', 'security_review', 'project_contractor.contractor']);

            DB::commit();

            $this->reset(['transfer_employee_name', 'transfer_employee_company', 'transfer_employee_project', 'transfer_project_contractor_id', 'transfer_position']);
            $this->dispatch('hideModalTransferEmployee');
            $this->dispatch('swal', title: 'Berhasil', text: 'Pekerja berhasil dipindahkan ke project tujuan.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error transfer employee: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Gagal memindahkan pekerja.', icon: 'error');
        }
    }

    public function exportExcel()
    {
        $rows = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->whereNotIn('nik', WorkerBlacklist::query()->active()->select('nik'))
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($employee) {
                $isBlacklisted = WorkerBlacklist::query()
                    ->active()
                    ->where('nik', $employee->nik)
                    ->exists();

                return [
                    'nama_pekerja' => $employee->full_name,
                    'nik' => $employee->nik,
                    'jabatan' => $employee->position,
                    'perusahaan' => $employee->project_contractor?->contractor?->company_name,
                    'status' => $isBlacklisted ? 'Blacklisted' : $employee->status,
                    'medical' => $employee->medical_review?->status,
                    'security' => $employee->security_review?->status,
                    'hse' => $employee->induction_card_number ? 'approved' : 'on_review',
                ];
            });

        return Excel::download(
            new class($rows) implements FromCollection, WithHeadings {
                public function __construct(private $rows)
                {
                }

                public function collection()
                {
                    return collect($this->rows);
                }

                public function headings(): array
                {
                    return ['Nama Pekerja', 'NIK', 'Jabatan', 'Perusahaan', 'Status', 'Medical', 'Security', 'HSE'];
                }
            },
            'worker-admin-' . date('Ymd_His') . '.xlsx'
        );
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

        if (!$this->selected_employee) {
            $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan.', icon: 'error');
            return;
        }

        $isAlreadyVerified =
            optional($this->selected_employee->security_review)->status === 'approved' ||
            !empty($this->selected_employee->security_card_number);

        if ($isAlreadyVerified) {
            $this->dispatch('swal', title: 'Info', text: 'Pekerja ini sudah diverifikasi Security. Nomor ID Badge tidak dapat diubah.', icon: 'info');
            return;
        }

        $this->security_badge_number = $this->generateSecurityBadgeNumber();

        $this->dispatch('showModalVerificationSecurity', data: $this->selected_employee, type: 'security', securityBadgeNumber: $this->security_badge_number);
    }

    #[On('submitVerificationSecurity')]
    public function submitVerificationSecurity($id, $area, $area_color = null, $securityBadgeNumber = null)
    {
        if (trim($area) == '' || $area == null) {
            $this->dispatch('swal', title: 'Error', text: 'Area harus dipilih.', icon: 'error');
            return;
        }

        if (trim((string) $area_color) === '') {
            $this->dispatch('swal', title: 'Error', text: 'Zona/warna area harus dipilih.', icon: 'error');
            return;
        }

        DB::beginTransaction();
        try {
            $employee = ContractorWorker::where('id', $id)->lockForUpdate()->first();
            $securityReview = SecurityReview::where('worker_id', $id)->lockForUpdate()->first();

            if (!$employee) {
                DB::rollBack();
                $this->dispatch('swal', title: 'Error', text: 'Data pekerja tidak ditemukan.', icon: 'error');
                return;
            }

            $isAlreadyVerified =
                optional($securityReview)->status === 'approved' ||
                !empty($employee->security_card_number);

            if ($isAlreadyVerified) {
                DB::rollBack();
                $this->dispatch('swal', title: 'Info', text: 'Verifikasi Security sudah pernah dilakukan untuk pekerja ini.', icon: 'info');
                return;
            }

            $badgeNumber = $securityBadgeNumber ?: $this->security_badge_number ?: $this->generateSecurityBadgeNumber();

            if (ContractorWorker::where('security_card_number', $badgeNumber)->where('id', '!=', $id)->exists()) {
                $badgeNumber = $this->generateSecurityBadgeNumber();
            }

            $employee->security_card_number = $badgeNumber;

            if ($securityReview) {
                $securityReview->status = 'approved';
                $securityReview->reviewed_by = Auth::user()->id;
                $securityReview->notes = 'ok';
                $securityReview->area = $area;
                $securityReview->area_color = $area_color;
                $securityReview->save();
            } else {
                SecurityReview::create([
                    'worker_id' => $id,
                    'user_id' => Auth::id(),
                    'reviewed_by' => Auth::id(),
                    'status' => 'approved',
                    'notes' => 'ok',
                    'area' => $area,
                    'area_color' => $area_color,
                    'reviewed_at' => now(),
                ]);
            }

            $employee->save();
            $this->security_badge_number = null;

            $this->dispatch('swal', title: 'Success', text: 'Berhasil Verifikasi Security.', icon: 'success');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Error', text: 'Gagal Verifikasi Security.', icon: 'error');
        }
    }

    private function generateSecurityBadgeNumber(): string
    {
        do {
            $latest = ContractorWorker::query()
                ->where('security_card_number', 'like', 'EM-KPB-%')
                ->orderByDesc('security_card_number')
                ->value('security_card_number');

            $nextNumber = 1;
            if ($latest && preg_match('/EM-KPB-(\d{5})$/', $latest, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            }

            $generated = 'EM-KPB-' . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
        } while (ContractorWorker::query()->where('security_card_number', $generated)->exists());

        return $generated;
    }

    public function alasanRejectMcu($id)
    {
        $this->selected_employee_id = $id;
        $this->selected_employee = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->find($id);

        $this->dispatch('showModalAlasanRejectMcu', data: $this->selected_employee);
    }

    public function modalRejectMcu($id): void
    {
        $this->dispatch('showModalRejectMcu', data: ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])->find($id));
    }

    public function showModalAlasanRejectMcu($id): void
    {
        $this->dispatch('loadingAlasanRejectMcu', data: $id);
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

    #[On('submitBlacklistWorker')]
    public function submitBlacklistWorker($id, $type, $until = null, $reason = null)
    {
        try {
            if (Auth::user()->role !== 'administrator') {
                $this->dispatch('swal', title: 'Error', text: 'Hanya administrator yang dapat melakukan blacklist.', icon: 'error');
                return;
            }

            $employee = ContractorWorker::findOrFail($id);

            $activeBlacklist = WorkerBlacklist::query()
                ->active()
                ->where('nik', $employee->nik)
                ->first();

            if ($activeBlacklist) {
                $this->dispatch('swal', title: 'Info', text: 'Pekerja ini sudah masuk blacklist aktif.', icon: 'info');
                return;
            }

            if (!in_array($type, ['temporary', 'permanent'], true)) {
                $this->dispatch('swal', title: 'Error', text: 'Tipe blacklist tidak valid.', icon: 'error');
                return;
            }

            $untilDate = null;
            if ($type === 'temporary') {
                if (empty($until)) {
                    $this->dispatch('swal', title: 'Error', text: 'Tanggal akhir blacklist wajib diisi untuk blacklist sementara.', icon: 'error');
                    return;
                }

                $untilDate = Carbon::parse($until)->toDateString();
                if ($untilDate < now()->toDateString()) {
                    $this->dispatch('swal', title: 'Error', text: 'Tanggal akhir blacklist tidak boleh lebih kecil dari hari ini.', icon: 'error');
                    return;
                }
            }

            DB::beginTransaction();

            WorkerBlacklist::updateOrCreate(
                ['nik' => $employee->nik],
                [
                    'full_name' => $employee->full_name,
                    'is_blacklisted' => true,
                    'blacklist_type' => $type,
                    'blacklisted_until' => $type === 'permanent' ? null : $untilDate,
                    'reason' => $reason,
                    'blacklisted_by' => Auth::id(),
                ]
            );

            $employee->status = 'rejected';
            $employee->save();

            if ($employee->medical_review) {
                $employee->medical_review->status = 'rejected';
                $employee->medical_review->notes = trim(($employee->medical_review->notes ?? '') . ' | Worker masuk blacklist aktif.');
                $employee->medical_review->save();
            }

            if ($employee->security_review) {
                $employee->security_review->status = 'rejected';
                $employee->security_review->notes = trim(($employee->security_review->notes ?? '') . ' | Worker masuk blacklist aktif.');
                $employee->security_review->save();
            }

            DB::commit();
            $this->dispatch('swal', title: 'Berhasil', text: 'Pekerja berhasil dimasukkan ke blacklist.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error submit blacklist worker: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Gagal blacklist pekerja.', icon: 'error');
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
        $url = route('print-view.employee-id-badge', ['id' => base64_encode($id)]);

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
        $transferProjects = ProjectContractor::with('contractor')
            ->where('is_closed', false)
            ->orderBy('project_name')
            ->get();

        if ($this->filter === 'Perusahaan') {
            $this->listFilter = \App\Models\User::query()
                ->where('role', 'contractor')
                ->orderBy('company_name')
                ->pluck('company_name', 'id')
                ->toArray();
        } elseif ($this->filter === 'Project') {
            $this->listFilter = ProjectContractor::query()
                ->orderBy('project_name')
                ->pluck('project_name', 'id')
                ->toArray();
        } else {
            $this->listFilter = [];
            $this->filterSelected = null;
        }

        $employees = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor.contractor'])
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->whereNotIn('nik', WorkerBlacklist::query()->active()->select('nik'))
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('position', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter === 'Perusahaan' && $this->filterSelected, function ($query) {
                $query->whereHas('project_contractor', function ($q) {
                    $q->where('contractor_id', (int) $this->filterSelected);
                });
            })
            ->when($this->filter === 'Project' && $this->filterSelected, function ($query) {
                $query->where('project_contractor_id', (int) $this->filterSelected);
            })
            ->when($this->statusSelected, function ($query) {
                $query->where('status', $this->statusSelected);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->totalPaginate);

        return view('livewire.dashboard.admin.list-employee', [
            'employees' => $employees,
            'transferProjects' => $transferProjects,
        ]);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->filterSelected = null;
        $this->statusSelected = null;
        $this->resetPage();
    }

    public function updatedFilterSelected(): void
    {
        $this->statusSelected = null;
        $this->resetPage();
    }

    public function updatedStatusSelected(): void
    {
        $this->resetPage();
    }

    public function updatedTotalPaginate(): void
    {
        $this->resetPage();
    }
}
