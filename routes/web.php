<?php

use App\Exports\TemplatePekerjaExport;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard\Admin\ListCompany;
use App\Livewire\Dashboard\Admin\ListEmployee as AdminListEmployee;
use App\Livewire\Dashboard\Admin\UserAccount\ListAccount;
use App\Livewire\Dashboard\Contractor\ListDraftEmployee;
use App\Livewire\Dashboard\Contractor\ListEmployee;
use App\Livewire\Dashboard\Contractor\ListProjectContract;
use App\Livewire\Dashboard\Home;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', Login::class)->name('login');

    Route::get('/register', Register::class)->name('register');
});


Route::group(['middleware' => 'auth'], function () {

    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    Route::get('/', Home::class)->name('home');

    Route::get('/list-company', ListCompany::class)->name('admin.list-company');
    Route::get('/list-employee', AdminListEmployee::class)->name('admin.list-employee');
    Route::get('/user-account', ListAccount::class)->name('admin.user-account');


    // Role inside user table
    Route::get('/contractor/list-employee', ListEmployee::class)->name('contractor.list-employee');
    Route::get('/contractor/list-draft-employee', ListDraftEmployee::class)->name('contractor.list-draft-employee');
    Route::get('/contractor/list-project-contract', ListProjectContract::class)->name('contractor.list-project-contract');

    Route::get('/contractor/download-template-pekerja', function() {
        $companyName = Auth::user()->company_name ?? 'PT. Contoh Perusahaan';
        return Excel::download(new TemplatePekerjaExport(ucwords(str_replace('-', ' ', $companyName))), 'template_pekerja.xlsx');
    })->name('contractor.download-template-pekerja');
});
