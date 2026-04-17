<?php

namespace App\Livewire\Dashboard\Admin;

use App\Models\ContractorWorker;
use App\Models\MedicalReview;
use App\Models\SecurityReview;
use App\Models\WorkerBlacklist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class WorkerBlacklistPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public ?string $search = null;
    public ?string $blacklist_nik = null;
    public ?string $blacklist_full_name = null;
    public string $blacklist_type = 'temporary';
    public ?string $blacklist_until = null;
    public ?string $blacklist_reason = null;

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'administrator') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }
    }

    public function updatedBlacklistType(): void
    {
        if ($this->blacklist_type === 'permanent') {
            $this->blacklist_until = null;
        }
    }

    public function createBlacklistWorker(): void
    {
        if (Auth::user()->role !== 'administrator') {
            $this->dispatch('swal', title: 'Error', text: 'Hanya administrator yang dapat membuat blacklist.', icon: 'error');
            return;
        }

        $this->validate([
            'blacklist_nik' => ['required', 'string', 'max:32'],
            'blacklist_full_name' => ['required', 'string', 'max:255'],
            'blacklist_type' => ['required', 'in:temporary,permanent'],
            'blacklist_until' => ['nullable', 'date', 'required_if:blacklist_type,temporary'],
            'blacklist_reason' => ['required', 'string', 'max:1000'],
        ], [
            'blacklist_nik.required' => 'NIK wajib diisi.',
            'blacklist_full_name.required' => 'Nama pekerja wajib diisi.',
            'blacklist_type.required' => 'Jenis blacklist wajib dipilih.',
            'blacklist_type.in' => 'Jenis blacklist tidak valid.',
            'blacklist_until.required_if' => 'Tanggal tenggat wajib diisi untuk blacklist sementara.',
            'blacklist_until.date' => 'Tanggal tenggat tidak valid.',
            'blacklist_reason.required' => 'Alasan blacklist wajib diisi.',
        ]);

        if ($this->blacklist_type === 'temporary' && $this->blacklist_until) {
            $untilDate = Carbon::parse($this->blacklist_until)->toDateString();
            if ($untilDate < now()->toDateString()) {
                $this->dispatch('swal', title: 'Error', text: 'Tanggal tenggat tidak boleh lebih kecil dari hari ini.', icon: 'error');
                return;
            }
        }

        DB::beginTransaction();

        try {
            $worker = ContractorWorker::where('nik', $this->blacklist_nik)->first();

            WorkerBlacklist::updateOrCreate(
                ['nik' => $this->blacklist_nik],
                [
                    'full_name' => $this->blacklist_full_name,
                    'is_blacklisted' => true,
                    'blacklist_type' => $this->blacklist_type,
                    'blacklisted_until' => $this->blacklist_type === 'permanent' ? null : Carbon::parse($this->blacklist_until)->toDateString(),
                    'reason' => $this->blacklist_reason,
                    'blacklisted_by' => Auth::id(),
                ]
            );

            if ($worker) {
                $worker->status = 'rejected';
                $worker->save();

                if ($worker->medical_review) {
                    $worker->medical_review->status = 'rejected';
                    $worker->medical_review->notes = trim(($worker->medical_review->notes ?? '') . ' | Worker masuk blacklist aktif.');
                    $worker->medical_review->reviewed_by = Auth::id();
                    $worker->medical_review->reviewed_at = now();
                    $worker->medical_review->save();
                }

                if ($worker->security_review) {
                    $worker->security_review->status = 'rejected';
                    $worker->security_review->notes = trim(($worker->security_review->notes ?? '') . ' | Worker masuk blacklist aktif.');
                    $worker->security_review->reviewed_by = Auth::id();
                    $worker->security_review->reviewed_at = now();
                    $worker->security_review->save();
                }
            }

            DB::commit();

            $this->reset(['blacklist_nik', 'blacklist_full_name', 'blacklist_until', 'blacklist_reason']);
            $this->blacklist_type = 'temporary';

            $this->dispatch('blacklistCreated');
            $this->dispatch('swal', title: 'Berhasil', text: 'Pekerja berhasil dimasukkan ke blacklist.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error create blacklist worker: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Gagal membuat blacklist pekerja.', icon: 'error');
        }
    }

    #[On('unblacklistWorker')]
    public function unblacklistWorker($id)
    {
        if (Auth::user()->role !== 'administrator') {
            $this->dispatch('swal', title: 'Error', text: 'Hanya administrator yang dapat melakukan unblacklist.', icon: 'error');
            return;
        }

        DB::beginTransaction();

        try {
            $record = WorkerBlacklist::findOrFail($id);
            $worker = ContractorWorker::where('nik', $record->nik)->first();

            $record->is_blacklisted = false;
            $record->blacklisted_until = null;
            $record->save();

            if ($worker) {
                $worker->status = 'approved';
                $worker->security_card_number = null;
                $worker->induction_card_number = null;
                $worker->save();

                $medicalReview = MedicalReview::where('worker_id', $worker->id)->first();
                if ($medicalReview) {
                    $medicalReview->status = 'on_review';
                    $medicalReview->reviewed_by = Auth::id();
                    $medicalReview->reviewed_at = now();
                    $medicalReview->save();
                }

                $securityReview = SecurityReview::where('worker_id', $worker->id)->first();
                if ($securityReview) {
                    $securityReview->status = 'on_review';
                    $securityReview->reviewed_by = Auth::id();
                    $securityReview->reviewed_at = now();
                    $securityReview->save();
                }
            }

            DB::commit();
            $this->dispatch('swal', title: 'Berhasil', text: 'Data blacklist berhasil dinonaktifkan.', icon: 'success');
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Error unblacklist worker: ' . $th->getMessage());
            $this->dispatch('swal', title: 'Error', text: 'Gagal melakukan unblacklist.', icon: 'error');
        }
    }

    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'Blacklist Pekerja',
            'contractor_data' => 'active',
            'blacklist_data_active' => 'active',
        ]
    )]
    #[Title('Blacklist Pekerja')]
    public function render()
    {
        $blacklists = WorkerBlacklist::query()
            ->active()
            ->when($this->search, function ($query) {
                $query->where(function ($sub) {
                    $sub->where('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('reason', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('livewire.dashboard.admin.worker-blacklist-page', [
            'blacklists' => $blacklists,
        ]);
    }
}
