<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Models\ContractorWorker;
use App\Models\WorkerBlacklist;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListEmployee extends Component
{

    use WithPagination;
    public $paginationTheme = 'bootstrap';

    #[Url(as: 'project_contract_id')]
    public ?int $projectContractId = null;
    public ?string $search = null;
    public $totalPaginate = 10;
    public ?string $statusSelected = null;
    public bool $projectIsClosed = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTotalPaginate()
    {
        $this->resetPage();
    }

    public function updatingStatusSelected()
    {
        $this->resetPage();
    }

    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'List Pekerja',
            'employee_active' => 'active',
            // 'patient_add_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {

        $query = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor'])
            ->whereHas('project_contractor', function ($query) {
                $query->where('contractor_id', Auth::id());
            })
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->whereNotIn('nik', WorkerBlacklist::query()->active()->select('nik'));

        if ($this->projectContractId && Auth::user()->role === 'contractor') {
            $query->where('project_contractor_id', $this->projectContractId);

            $project = \App\Models\ProjectContractor::where('id', $this->projectContractId)
                ->where('contractor_id', Auth::id())
                ->first();
            $this->projectIsClosed = (bool) ($project?->is_closed);
        } else {
            $this->projectIsClosed = false;
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('position', 'like', '%' . $this->search . '%');
            });
        }

        if (!is_null($this->statusSelected)) {
            $query->where('status', $this->statusSelected);
        }

        $employees = $query
            ->orderBy('created_at', 'desc')
            ->paginate($this->totalPaginate);

        return view('livewire.dashboard.contractor.list-employee', [
            'employees' => $employees,
        ]);
    }
}
