<div class="mb-0">
    <div class="card card-md">
        <div class="card-body">
            <h2 class="h2 text-center mb-4">Masuk ke Akun Anda</h2>
            <form wire:submit.prevent="login" novalidate>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                        autocomplete="off" wire:model='email' />
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-2">
                    <label class="form-label">
                        Password
                        {{-- <span class="form-label-description">
                            <a href="./forgot-password.html">I forgot password</a>
                        </span> --}}
                    </label>
                    <div class="input-group input-group-flat">
                        <input type="{{ $showPassword ? 'text' : 'password' }}" class="form-control @error('password') is-invalid @enderror"
                            placeholder="password" autocomplete="off" wire:model='password' />
                        <span class="input-group-text">
                            <span wire:click="toggleShowPassword" class="link-secondary" style="cursor: pointer;">
                                <i class="{{ $showPassword ? 'ti ti-eye-off' : 'ti ti-eye' }}"></i>
                            </span>
                        </span>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-2">
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading
                                wire:target="login" role="status" aria-hidden="true">
                            </span>
                            Masuk

                        </button>
                        <span class="d-block text-center mt-3">
                            Belum punya akun? <a href="{{ route('register') }}" tabindex="-1">Buat Akun</a> <small
                                class="text-muted">(khusus kontraktor)</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
