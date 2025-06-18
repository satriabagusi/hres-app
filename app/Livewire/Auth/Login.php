<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{

    public $email;
    public $password;
    public $showPassword = false;

    public function toggleShowPassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login()
    {

        $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        // check status user is approved or pending
        $user = User::where('email', $this->email)->first();
        if ($user && $user->status !== 'approved') {
            $this->dispatch('swal', title: 'Error', text: 'Akun Anda belum disetujui. Silakan tunggu konfirmasi dari admin.', icon: 'error');
            return;
        }


        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('success', 'Login berhasil.');
            return redirect()->route('home');
        } else {
            $this->dispatch('swal', title: 'Error', text: 'Email atau password salah.', icon: 'error');
        }
    }

    #[Layout('layouts.auth')]
    #[Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
