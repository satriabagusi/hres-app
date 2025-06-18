<?php

namespace App\Livewire\Dashboard\Admin\UserAccount;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class ListAccount extends Component
{
    public $totalPaginate = 10;

    public ?string $search = null;

    public $email, $password, $role, $name, $company_name;
    public $showPassword = false;
    public $generatedPassword = null;
    public $user_role = ['administrator', 'manager', 'hse', 'medical', 'security'];


    public function addAccount()
    {
        // dd($this->role);

        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in($this->user_role)],
            'company_name' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'role.required' => 'Role tidak boleh kosong.',
            'role.in' => 'Role tidak valid.',
            'company_name.required' => 'Perusahaan tidak boleh kosong.',
        ]);

        DB::beginTransaction();

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => $this->role,
                'company_name' => $this->company_name,
                'status' => 'approved',
            ]);

            $this->reset(['name', 'email', 'role', 'company_name']);

            DB::commit();
            $this->dispatch('swal', title: 'Berhasil', text: 'Akun berhasil ditambahkan.', icon: 'success');
            $this->dispatch('success');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat menambahkan akun.' . $th->getMessage(), icon: 'error');
        }
    }

    #[On('deactivateAccount')]
    public function deactivateAccount($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            $this->dispatch('swal', title: 'Berhasil', text: 'Akun berhasil di hapus/di non-aktifkan.', icon: 'success');
        } catch (\Throwable $th) {
            Log::error($th);
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat menghapus akun.' . $th->getMessage(), icon: 'error');
        }
    }

    #[On('activateAccount')]
    public function activateAccount($id)
    {
        try {
            $user = User::withTrashed()->find($id); // gunakan find() tanpa 'OrFail'

            if (!$user) {
                $this->dispatch('swal', title: 'Data Tidak Ditemukan', text: 'Akun tidak ditemukan di database.', icon: 'error');
                return;
            }

            if (!$user->trashed()) {
                $this->dispatch('swal', title: 'Info', text: 'Akun ini sudah aktif.', icon: 'info');
                return;
            }

            $user->restore();

            $this->dispatch('swal', title: 'Berhasil', text: 'Akun berhasil diaktifkan.', icon: 'success');
        } catch (\Throwable $th) {
            Log::error($th);
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat mengaktifkan akun. ' . $th->getMessage(), icon: 'error');
        }
    }

    #[On('resetPassword')]
    public function resetPassword($id)
    {
        $this->generatedPassword = "@hres_app_123";

        try {
            $user = User::findOrFail($id)
                        ->update([
                            'password' => bcrypt($this->generatedPassword)
                        ]);

            // $this->dispatch('swal', title: 'Berhasil', text: 'Password berhasil direset.', icon: 'success');
            $this->dispatch('password-changed', newPassword: $this->generatedPassword);
        } catch (\Throwable $th) {
            Log::error($th);
            $this->dispatch('swal', title: 'Gagal', text: 'Terjadi kesalahan saat mereset password. ' . $th->getMessage(), icon: 'error');
        }

    }


    #[Layout(
        'layouts.dashboard',
        [
            'subTitle' => 'List Akun User',
            'employee_account_active' => 'active',
            'list_employee_active' => 'active'
        ]
    )]
    #[Title('Dashboard')]
    public function render()
    {
        $users = User::where('id', '!=', Auth::id())
            // ->where('role', '!=', 'contractor')
            ->withTrashed()
            ->paginate($this->totalPaginate);

        return view('livewire.dashboard.admin.user-account.list-account', [
            'users' => $users
        ]);
    }
}
