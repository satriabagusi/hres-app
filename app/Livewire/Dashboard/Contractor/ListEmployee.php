<?php

namespace App\Livewire\Dashboard\Contractor;

use App\Models\ContractorWorker;
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

        if ($this->projectContractId && Auth::user()->role === 'contractor') {
            $employees = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor'])
                ->whereHas('project_contractor', function ($query) {
                    $query->where('contractor_id', Auth::id())
                        ->where('id', $this->projectContractId); // filter berdasarkan user login

                })
                ->where('project_contractor_id', $this->projectContractId)
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        if ($this->projectContractId == null && Auth::user()->role === 'contractor') {
            $employees = ContractorWorker::with(['medical_review', 'security_review', 'project_contractor'])
                ->whereHas('project_contractor', function ($query) {
                    $query->where('contractor_id', Auth::id()); // filter berdasarkan user login

                })
                ->where('project_contractor_id', $this->projectContractId)
                ->whereIn('status', ['submitted', 'approved', 'rejected'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }


        return view('livewire.dashboard.contractor.list-employee', [
            'employees' => $employees,
        ]);
    }
}
