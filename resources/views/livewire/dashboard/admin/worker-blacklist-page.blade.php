<div>
    <div class="col-12">
        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full align-items-center">
                        <div class="col">
                            <h3 class="card-title mb-0">Blacklist Pekerja</h3>
                            <p class="text-secondary m-0">Data blacklist aktif berbasis NIK lintas perusahaan dan project.</p>
                        </div>
                        <div class="col-md-auto col-sm-12 d-flex gap-2 align-items-center">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#manualBlacklistModal">
                                <i class="ti ti-plus"></i>
                                Input Blacklist Manual
                            </button>
                            <div class="input-group input-group-flat w-auto">
                                <span class="input-group-text">
                                    <i class="ti ti-search"></i>
                                </span>
                                <input type="text" class="form-control" autocomplete="off"
                                    placeholder="Cari NIK / nama / alasan" wire:model.live="search" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter table-selectable">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Tenggat</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody">
                            @forelse ($blacklists as $item)
                                @php
                                    $isActive = $item->is_blacklisted && (
                                        $item->blacklist_type === 'permanent' ||
                                        ($item->blacklist_type === 'temporary' && $item->blacklisted_until && \Carbon\Carbon::parse($item->blacklisted_until)->isToday() || \Carbon\Carbon::parse($item->blacklisted_until)->isFuture())
                                    );
                                @endphp
                                <tr class="{{ $item->is_blacklisted ? 'bg-dark text-white' : '' }}">
                                    <td>{{ $loop->iteration + ($blacklists->currentPage() - 1) * $blacklists->perPage() }}</td>
                                    <td>{{ $item->nik }}</td>
                                    <td>{{ $item->full_name }}</td>
                                    <td>
                                        @if ($item->blacklist_type === 'permanent')
                                            <span class="badge bg-dark text-dark-fg">Permanent</span>
                                        @else
                                            <span class="badge bg-orange text-orange-fg">Temporary</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->blacklisted_until ? \Carbon\Carbon::parse($item->blacklisted_until)->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        @if ($item->is_blacklisted)
                                            <span class="badge bg-red text-red-fg">Active</span>
                                        @else
                                            <span class="badge bg-success text-success-fg">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->reason ?: '-' }}</td>
                                    <td>
                                        @if ($item->is_blacklisted)
                                            <button type="button" class="btn btn-sm btn-outline-teal btn-unblacklist"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->full_name }}"
                                                data-nik="{{ $item->nik }}">
                                                <i class="ti ti-check"></i>
                                                Unblacklist
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data blacklist.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center">
                    <div class="pagination m-0 ms-auto">
                        {{ $blacklists->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="manualBlacklistModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-0">Input Blacklist Manual</h5>
                        <div class="text-secondary">Tambahkan pekerja ke blacklist aktif agar upload perusahaan berikutnya langsung ditolak.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="createBlacklistWorker">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">NIK</label>
                                <input type="text" class="form-control @error('blacklist_nik') is-invalid @enderror"
                                    placeholder="Masukkan NIK pekerja" wire:model.defer="blacklist_nik">
                                @error('blacklist_nik')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Nama Pekerja</label>
                                <input type="text" class="form-control @error('blacklist_full_name') is-invalid @enderror"
                                    placeholder="Masukkan nama pekerja" wire:model.defer="blacklist_full_name">
                                @error('blacklist_full_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jenis Blacklist</label>
                                <div wire:ignore>
                                    <select id="manual-blacklist-type-select" class="form-select">
                                        <option value="temporary">Temporary</option>
                                        <option value="permanent">Permanent</option>
                                    </select>
                                </div>
                                @error('blacklist_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4" id="manual-blacklist-until-wrapper">
                                <label class="form-label">Tenggat Blacklist</label>
                                <div class="input-icon" wire:ignore>
                                    <span class="input-icon-addon">
                                        <i class="ti ti-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control @error('blacklist_until') is-invalid @enderror"
                                        id="manual-blacklist-until-picker" placeholder="Pilih tanggal tenggat" readonly>
                                </div>
                                @error('blacklist_until')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alasan Blacklist</label>
                                <textarea rows="3" class="form-control @error('blacklist_reason') is-invalid @enderror"
                                    placeholder="Tuliskan alasan blacklist" wire:model.defer="blacklist_reason"></textarea>
                                @error('blacklist_reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createBlacklistWorker">Simpan Blacklist</span>
                            <span wire:loading wire:target="createBlacklistWorker">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            const typeSelectElement = document.getElementById('manual-blacklist-type-select');
            const untilWrapper = document.getElementById('manual-blacklist-until-wrapper');
            const untilInput = document.getElementById('manual-blacklist-until-picker');
            const modalElement = document.getElementById('manualBlacklistModal');
            let typeTomSelect = null;
            let picker = null;

            const toggleUntilField = (type) => {
                if (!untilWrapper) {
                    return;
                }

                untilWrapper.style.display = type === 'permanent' ? 'none' : '';

                if (type === 'permanent' && untilInput) {
                    untilInput.value = '';
                }
            };

            const initManualBlacklistInputs = () => {
                if (typeSelectElement && !typeTomSelect) {
                    typeTomSelect = new TomSelect(typeSelectElement, {
                        create: false,
                        allowEmptyOption: false,
                        onChange: function(value) {
                            @this.set('blacklist_type', value);
                            toggleUntilField(value);
                        }
                    });

                    typeTomSelect.setValue(@js($blacklist_type), true);
                    toggleUntilField(@js($blacklist_type));
                }

                if (untilInput && !picker) {
                    picker = new Litepicker({
                        element: untilInput,
                        format: 'DD-MM-YYYY',
                        buttonText: {
                            previousMonth: '<i class="ti ti-chevron-left"></i>',
                            nextMonth: '<i class="ti ti-chevron-right"></i>',
                        },
                        singleMode: true,
                        autoApply: true,
                        setup(instance) {
                            instance.on('selected', (date) => {
                                @this.set('blacklist_until', date.format('YYYY-MM-DD'));
                            });
                        }
                    });

                    untilInput._litepicker = picker;
                }
            };

            if (modalElement) {
                modalElement.addEventListener('shown.bs.modal', function() {
                    initManualBlacklistInputs();
                    toggleUntilField(@js($blacklist_type));
                });
            }

            document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-unblacklist');

                if (!button) {
                    return;
                }

                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const nik = button.getAttribute('data-nik');

                Swal.fire({
                    title: 'Unblacklist Pekerja',
                    html: `<p>Yakin unblacklist <b>${name}</b> (${nik})?</p>`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Unblacklist',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0ca678'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('unblacklistWorker', { id });
                    }
                });
            });

            Livewire.on('blacklistCreated', () => {
                if (typeTomSelect) {
                    typeTomSelect.setValue('temporary', true);
                }

                if (untilInput) {
                    untilInput.value = '';
                }

                toggleUntilField('temporary');

                if (modalElement && window.bootstrap) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });
    </script>
@endpush
