<?php

namespace App\Livewire\Dashboard;

use App\Models\ContractorWorker;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{

    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'Dashboard',
            'home_active' => 'active',
            // 'patient_add_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {
        $total_contractor_pending = User::where('role', 'contractor')
            ->where('status', 'pending')
            ->count();

        if (Auth::user()->role === 'contractor') {
            $total_worker_draft = ContractorWorker::whereHas('project_contractor', function ($query) {
                $query->where('contractor_id', Auth::id());
            })
                ->where(function ($query) {
                    $query->where('status', 'draft')
                        ->orWhereNull('photo')
                        ->orWhereNull('ktp_document')
                        ->orWhereNull('form_b_document');
                })
                ->count();

            $total_worker_submitted = ContractorWorker::whereHas('project_contractor', function ($query) {
                $query->where('contractor_id', Auth::id());
            })
                ->where('status', 'submitted')
                ->count();
        }

        if (Auth::user()->role === 'administrator' || Auth::user()->role === 'medical' || Auth::user()->role === 'security') {
            $total_worker_draft = ContractorWorker::where('status', 'draft')
                ->orWhere('photo', null)
                ->orWhere('ktp_document', null)
                ->orWhere('form_b_document', null)
                ->count();

            $total_worker_submitted = ContractorWorker::where('status', 'submitted')
                ->count();
        }

        return view('livewire.dashboard.home', [
            'total_contractor_pending' => $total_contractor_pending,
            'total_worker_draft' => $total_worker_draft ?? 0,
            'total_worker_submitted' => $total_worker_submitted ?? 0,
        ]);
    }
}
