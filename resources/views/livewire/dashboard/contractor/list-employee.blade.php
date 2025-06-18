<div>
    @if($projectContractId)
    <div class="col-12 mb-3">
        <a class="btn btn-cyan" href="{{ route('contractor.list-draft-employee', ['project_contract_id' => $projectContractId]) }}"><i class="ti ti-upload"></i> &nbsp;
            Upload/Draft Pekerja</a>
        <p class="text-muted mt-2">Jika data pekerja tidak muncul disini silahkan check pada Draft Data Pekerja (ada
            kemungkinan data belum di submit atau di reject terkait dokumen)</p>
    </div>
    <hr>
    @endif
    <div class="col-12">
        <div class="card">
            <div class="card-table">
                <div class="card-header">
                    <div class="row w-full">
                        <div class="col">
                            <h3 class="card-title mb-0">List Pekerja</h3>
                            <p class="text-secondary m-0">Data Pekerja Kontraktor.</p>
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
                            </div>
                        </div>
                    </div>
                </div>
                <div id="advanced-table">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-selectable">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>Nama Pekerja</th>
                                    <th>USIA</th>
                                    <th>NIK</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                    <th>Verifikasi Medical</th>
                                    <th>Verifikasi Security</th>
                                    <th>Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody class="table-tbody">
                                @foreach ($employees as $item)
                                    <tr>
                                        <td width="50px" class="text-center">
                                            {{-- Show number based on current page --}}
                                            {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                        </td>
                                        <td>
                                            <span class="text-body">{{ $item->full_name }}</span>
                                        </td>
                                        <td class="">{{ \Carbon\Carbon::parse($item->birth_date)->age }}</td>
                                        <td class="">{{ $item->nik }}</td>
                                        <td class="">{{ $item->position }}</td>
                                        <td class="sort-status">
                                            @if ($item->status == 'approved')
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @elseif($item->status == 'draft')
                                                <span class="badge bg-orange text-orange-fg">Draft</span>
                                            @elseif($item->status == 'submitted')
                                                <span class="badge bg-blue text-blue-fg">Diajukan</span>
                                            @elseif($item->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="sort-status">
                                            @if ($item->medical_review->status == 'on_review')
                                                <span class="badge bg-yellow text-yellow-fg ">Sedang Di Review</span>
                                            @elseif($item->medical_review->status == 'approved')
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @elseif($item->medical_review->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="sort-status">
                                            @if ($item->security_review->status == 'on_review')
                                                <span class="badge bg-yellow text-yellow-fg">Sedang Di Review</span>
                                            @elseif($item->security_review->status == 'approved')
                                                <span class="badge bg-lime text-lime-fg">Disetujui</span>
                                            @elseif($item->security_review->status == 'rejected')
                                                <span class="badge bg-red text-red-fg">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="">{{ $item->birth_place }}</td>
                                        <td class="">
                                            {{ \Carbon\Carbon::parse($item->birth_date)->format('d-m-Y') }}
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex align-items-center">
                        <div class="pagination m-0 ms-auto">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {


        });
    </script>
@endpush
