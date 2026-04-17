<div>
    <div class="col-12">
        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full">
                        <div class="col">
                            <h3 class="card-title mb-0">List Akun User</h3>
                            <p class="text-secondary m-0">Data Akun User.</p>
                        </div>
                        <div class="col-md-auto col-sm-12">
                            <div class="ms-auto d-flex flex-wrap btn-list">
                                <div class="input-group input-group-flat w-auto">
                                    <span class="input-group-text">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input id="advanced-table-search" type="text" class="form-control"
                                        autocomplete="off" placeholder="Cari ... " wire:model.live='search' />
                                </div>
                                <div class="dropdown">
                                    <a class="btn btn-outline-azure dropdown-toggle d-flex align-items-center"
                                        data-bs-toggle="dropdown" wire:loading.delay wire:loading.attr="disabled"
                                        wire:target="totalPaginate, page">
                                        <!-- Spinner dalam button saat loading -->
                                        <div wire:loading.delay wire:target="totalPaginate"
                                            class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span id="page-count" class="me-1">Tampilkan {{ $totalPaginate }}</span>
                                        <span>data</span>
                                    </a>
                                    <div class="dropdown-menu">
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('totalPaginate', 10)">10
                                            data</button>
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('totalPaginate', 20)">20
                                            data</button>
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('totalPaginate', 50)">50
                                            data</button>
                                        <button type="button" class="dropdown-item"
                                            wire:click="$set('totalPaginate', 100)">100
                                            data</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
                        <div class="position-relative">
                            <div wire:loading.delay
                                wire:target="totalPaginate, search, page, approveDocument, rejectDocument"
                                class="position-absolute w-100 h-100 bg-white bg-opacity-75 table-loading-overlay"
                                style="top: 0; left: 0; z-index: 10;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        <table class="table table-vcenter table-selectable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama </th>
                                    <th>Email</th>
                                    <th>User Role</th>
                                    <th>Status</th>
                                    <th>Nama Perusahaan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody">
                                @foreach ($users as $item)
                                    <tr>
                                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                        </td>
                                        <td>
                                            <span class="text-body">{{ $item->name }}</span>
                                        </td>
                                        <td class="">
                                            {{ $item->email }}
                                        </td>
                                        <td class="text-uppercase">
                                            {{ $item->role }}
                                        </td>
                                        <td>

                                            @if ($item->role === 'contractor')
                                                @if ($item->status == 'approved')
                                                    <span class="badge bg-lime text-lime-fg">Approved</span>
                                                @elseif($item->status == 'pending')
                                                    <span class="badge bg-orange text-orange-fg">Pending</span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="badge bg-red text-red-fg">Rejected</span>
                                                @endif
                                            @elseif(
                                                $item->role === 'hse' ||
                                                    $item->role === 'administrator' ||
                                                    $item->role === 'manager' ||
                                                    $item->role === 'security' ||
                                                    $item->role === 'medical')
                                                @if ($item->deleted_at == null)
                                                    <span class="badge bg-lime text-lime-fg">Aktif</span>
                                                @else
                                                    <span class="badge bg-red text-red-fg">Tidak Aktif</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="">
                                            {{ $item->company_name }}
                                        </td>
                                        <td class="sort-category py-0">
                                            @if ($item->role === 'contractor' && $item->status === 'rejected')
                                                <button class="btn btn-sm btn-outline-teal unreject-acc-btn"
                                                    data-id="{{ $item->id }}" type="button">Un-Rejected</button>
                                            @endif
                                            @if ($item->deleted_at == null)
                                                <button class="btn btn-sm btn-outline-red deactivate-acc-btn"
                                                    data-id="{{ $item->id }}" type="button">NonAktif</button>
                                            @else
                                                <button class="btn btn-sm btn-outline-green activate-acc-btn"
                                                    data-id="{{ $item->id }}" type="button">Aktifkan</button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-indigo reset-pass-btn"
                                                data-id="{{ $item->id }}" type="button">Reset Password</button>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div>
                            <button class="btn btn-sm btn-outline-blue" data-bs-toggle="modal"
                                data-bs-target="#modalAddAccount">
                                Tambah Akun
                            </button>
                        </div>

                        <div class="pagination m-0 ms-auto">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAddAccount" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun</h5>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="addAccount">
                        <div class="row">

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text"
                                        class="form-control @error('name')
                                        is-invalid
                                    @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email"
                                        class="form-control @error('email')
                                        is-invalid
                                    @enderror"
                                        wire:model="email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password"
                                        class="form-control @error('password')
                                        is-invalid
                                    @enderror"
                                        wire:model="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Nama Perusahaan</label>
                                    <input type="text"
                                        class="form-control @error('company_name')
                                        is-invalid
                                    @enderror"
                                        wire:model="company_name">
                                    @error('company_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3" wire:ignore>
                                    <label class="form-label">Advanced select</label>
                                    <select class="form-select" id="select-user-role">
                                        {{-- foreach non-indexed array $user_role = ['administrator', 'manager', 'hse', 'medical', 'security' ] --}}
                                        @foreach ($user_role as $item)
                                            <option value="{{ $item }}" class="text-uppercase">
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('role')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </form>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" wire:click='addAccount'>Tambah</button>
                    <button class="btn btn-outline-secondary float-end" data-bs-dismiss="modal"> Batal </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {

            var bootstrap = tabler.bootstrap;

            const modalAddAccount = new bootstrap.Modal('#modalAddAccount', {
                keyboard: false,
                backdrop: 'static',
            });

            const modalAddAccountElement = document.getElementById('modalAddAccount');
            const selectEl = document.getElementById('select-user-role');

            modalAddAccountElement.addEventListener('shown.bs.modal', function() {
                // Check if the select element has already been initialized TomSelect
                if (!selectEl.tomselect) {
                    // TomSelect
                    new TomSelect(selectEl, {
                        copyClassesToDropdown: false,
                        // dropdownParent: "body",
                        controlInput: "<input>",
                        render: {
                            item: function(data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data
                                        .customProperties + "</span>" + escape(data.text) +
                                        "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function(data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data
                                        .customProperties + "</span>" + escape(data.text) +
                                        "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                        onChange: function(value) {
                            @this.set('role', value);
                        }
                    })
                }
            })

            Livewire.on('success', (e) => {
                modalAddAccount.hide();
            })
            document.addEventListener('click', (e) => {
                    const deactivateBtn = e.target.closest('.deactivate-acc-btn');
                    if (deactivateBtn) {
                        const idBtn = Number(deactivateBtn.getAttribute('data-id'));

                        Swal.fire({
                            title: "Apakah anda yakin?",
                            html: "Akun ini tidak akan bisa login jika di non-aktifkan",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, non-aktifkan!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Livewire.dispatch('deactivateAccount', {
                                    id: idBtn
                                });
                            }
                        });
                        return;
                    }

                    const activateBtn = e.target.closest('.activate-acc-btn');
                    if (activateBtn) {
                        const idBtn = Number(activateBtn.getAttribute('data-id'));

                        Swal.fire({
                            title: "Apakah anda yakin?",
                            html: "Akun ini bisa digunakan login jika di aktifkan kembali",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, aktifkan!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Livewire.dispatch('activateAccount', {
                                    id: idBtn
                                });
                            }
                        });
                        return;
                    }

                    const unrejectBtn = e.target.closest('.unreject-acc-btn');
                    if (unrejectBtn) {
                        const idBtn = Number(unrejectBtn.getAttribute('data-id'));

                        Swal.fire({
                            title: "Apakah anda yakin?",
                            html: "Status akun contractor ini akan diubah dari rejected ke pending",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#0ca678',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, un-rejected!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Livewire.dispatch('unRejectAccount', {
                                    id: idBtn
                                });
                            }
                        });
                        return;
                    }

                    const resetBtn = e.target.closest('.reset-pass-btn');
                    if (resetBtn) {
                        const idBtn = Number(resetBtn.getAttribute('data-id'));

                        Swal.fire({
                            title: "Apakah anda yakin?",
                            html: "Password akun ini akan di reset",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, reset password!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Livewire.dispatch('resetPassword', {
                                    id: idBtn
                                });
                            }
                        });
                    }
            });

            Livewire.on('password-changed', (e) => {
                // Do not auto close SweetAlert add Copy Button
                Swal.fire({
                    title: "Password berhasil di reset",
                    icon: 'success',
                    html: "Password baru: <code>" + e.newPassword + "</code>",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCancelButton: true,
                    showConfirmButton: true,
                    confirmButtonText: 'Copy',
                    cancelButtonText: 'Tutup',
                }).then((result) => {
                    if (result.isConfirmed) {
                        navigator.clipboard.writeText(e.newPassword);
                    }
                })
            })


        });
    </script>
@endpush
