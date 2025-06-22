<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Register extends Component
{

    use WithFileUploads;

    #[Validate('required|string')]
    public string $companyName = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $showPassword = false;

    // #[Validate('file|mimes:pdf|max:3072')]
    // public $formB = null;


    public function toggleShowPassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function save()
    {

        // if (!$this->formB instanceof TemporaryUploadedFile || !$this->formB->exists()) {
        //     $this->dispatch('swal', title: 'Error', text: 'File dokumen kontraktor tidak valid atau belum diupload.', icon: 'error');
        //     return;
        // }

        $this->validate([
            'companyName' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // 'formB' => 'file|mimes:pdf|max:3072', // 3MB max
        ], [
            'companyName.required' => 'Nama Perusahaan tidak boleh kosong.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Kata sandi tidak boleh kosong.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            // 'formB.file' => 'File dokumen kontraktor harus berupa file.',
            // 'formB.mimes' => 'File dokumen kontraktor harus berupa file PDF.',
            // 'formB.max' => 'File dokumen kontraktor maksimal 3MB.',
        ]);

        // $path = Storage::move('livewire-tmp', $this->formB->getRealPath(), ); // $this->formB->store()

        DB::beginTransaction();

        try {
            // $newFileName = 'formB_' . time() . "_" . str_replace(' ', '_', $this->companyName) . '.' . $this->formB->getClientOriginalExtension();
            // $destinationPath = public_path('uploads/contractor_documents');

            // if (!File::exists($destinationPath)) {
            //     File::makeDirectory($destinationPath, 0755, true);
            // }

            // $store = File::move($this->formB->getRealPath(), $destinationPath . '/' . $newFileName);

            // if (!$store) {
            //     $this->dispatch('swal', title: 'Error', text: 'Gagal menyimpan file dokumen kontraktor.', icon: 'error');
            //     return;
            // }
            User::create([
                'name' => $this->companyName, // Assuming name is the same as company name
                'company_name' => $this->companyName,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => 'contractor',
                // 'document_contractor' => $newFileName,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Silakan tunggu verifikasi dari admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal', title: 'Error', text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.' . $e->getMessage() , icon: 'error');
            return;
        }
    }


    #[Title('Register')]
    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.register');
    }
}
