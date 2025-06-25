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

        // Siapkan query dasar pekerja sesuai role
        $baseQuery = ContractorWorker::query();

        if (Auth::user()->role === 'contractor') {
            $baseQuery->whereHas('project_contractor', function ($q) {
                $q->where('contractor_id', Auth::id());
            });
        }

        // Hitung draft: status draft ATAU dokumen wajib belum lengkap
        $total_worker_draft = (clone $baseQuery)
            ->where(function ($q) {
                $q->where('status', 'draft')
                    ->orWhereNull('photo')
                    ->orWhereNull('ktp_document')
                    ->orWhereNull('form_b_document');
            })
            ->count();

        // Hitung submitted
        $total_worker_submitted = (clone $baseQuery)
            ->where('status', 'submitted')
            ->count();

        // Hitung medical approved / on review
        $total_medical_fit_to_work = (clone $baseQuery)
            ->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'fit'))
            ->count();

        $total_medical_on_review = (clone $baseQuery)
            ->whereHas('medical_review', fn($q) => $q->where('status', 'on_review'))
            ->count();

        $total_medical_follow_up = (clone $baseQuery)
            ->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'follow_up'))
            ->count();

        $total_medical_unfit = (clone $baseQuery)
            ->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'unfit'))
            ->count();

        // Hitung security approved / on review
        $total_id_badge_printed = (clone $baseQuery)
            ->where('status', 'approved')
            ->count();

        $total_before_induction = (clone $baseQuery)
            ->where('status', 'submitted')
            ->where('induction_card_number', '=',  null)
            ->count();

        $total_after_induction = (clone $baseQuery)
            ->where('status', 'submitted')
            ->whereNotNull('induction_card_number')
            ->count();

        $list_company_chart = User::where('role', 'contractor')
            ->where('status', 'approved')
            ->withCount([
                // Semua pekerja fit
                'workers as fit_to_work' => function ($q) {
                    $q->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'fit'));
                },

                // Medical on review
                'workers as medical_on_review' => function ($q) {
                    $q->whereHas('medical_review', fn($q) => $q->where('status', 'on_review'));
                },

                // Medical follow up
                'workers as medical_follow_up' => function ($q) {
                    $q->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'follow_up'));
                },

                // Medical unfit
                'workers as medical_unfit' => function ($q) {
                    $q->whereHas('medical_review', fn($q) => $q->where('status_mcu', 'unfit'));
                },

                // ID card printed (status approved)
                'workers as id_card_printed' => function ($q) {
                    $q->where('status', 'approved');
                },

                // Belum induction
                'workers as belum_induction' => function ($q) {
                    $q->where('status', 'submitted')
                        ->whereNull('induction_card_number');
                },
            ])
            ->get();

        $workerQuery = ContractorWorker::whereHas('project_contractor', function ($q) {
            $q->where('contractor_id', Auth::id());
        });

        // Hitung jumlah berdasarkan status
        $total_draft = (clone $workerQuery)->where('status', 'draft')->count();
        $total_submitted = (clone $workerQuery)->where('status', 'submitted')->count();
        $total_approved = (clone $workerQuery)->where('status', 'approved')->count();
        $total_rejected = (clone $workerQuery)->where('status', 'rejected')->count(); // jika ada
        $total_status = [
            $total_draft,
            $total_submitted,
            $total_approved,
            $total_rejected
        ];


        return view('livewire.dashboard.home', [
            'total_contractor_pending' => $total_contractor_pending,
            'total_worker_draft' => $total_worker_draft,
            'total_worker_submitted' => $total_worker_submitted,
            'total_medical_fit_to_work' => $total_medical_fit_to_work,
            'total_medical_on_review' => $total_medical_on_review,
            'total_id_badge_printed' => $total_id_badge_printed,
            'total_before_induction' => $total_before_induction,
            'total_after_induction' => $total_after_induction,
            'total_medical_follow_up' => $total_medical_follow_up,
            'total_medical_unfit' => $total_medical_unfit,
            'list_company_chart' => $list_company_chart,

            'total_status' => $total_status
        ]);
    }
}
