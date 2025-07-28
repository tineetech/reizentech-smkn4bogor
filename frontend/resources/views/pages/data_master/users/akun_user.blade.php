@extends('layouts.app')

@section('title', $title)

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')

<div class="card">
    <div class="card-table">
        <div class="card-header">
            <div class="row w-full">
                <div class="col">
                    <h3 class="card-title mb-0">Tabel {{ $title }}</h3>
                </div>
                <div class="col-md-auto col-sm-12">
                    <div class="ms-auto d-flex flex-wrap btn-list">
                        <a href="javascript:void(0);" class="btn btn-warning btn-icon btn-refresh">
                            <i class="icon ti ti-refresh"></i>
                        </a>
                        <a class="btn btn-primary btn-insert" href="javascript:void(0);">
                            <i class="ti ti-plus"></i>
                            Tambah
                        </a>
                    </div>
                </div>
            </div>
        </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-stripe" id="table-data">
                    <thead>
                        <tr>
                            <th class="w-1"></th>
                            <th>
                                Username
                            </th>
                            <th>
                                Email
                            </th>
                            <th>
                                No Telpon
                            </th>
                            <th>
                                Roles
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody"></tbody>
                </table>
            </div>
    </div>
</div>

<div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasTop" aria-labelledby="offcanvasTopLabel"
    aria-modal="true" role="dialog" style="height: 100vh;">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title" id="offcanvasTopLabel">Form Data</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div class="flex-grow-1">
            <div id="loadingSpinnerForm" class="text-center my-5" style="display:none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memuat data, harap tunggu...</p>
            </div>

            <form id="formMainMenu" action="" method="post">
                <div class="row row-cards">
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control " name="username" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control " name="email" >
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">No Telpon</label>
                            <input type="text" class="form-control " name="phone_number" >
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div id="additionalSelectContainer"></div>

                    <div class="col-sm-12">
                        <div class="mb-3" id="newSelectForm">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="active">Aktif</option>
                                <option value="non-activate">Tidak Aktif</option>
                                <option value="banned">Ban Akun</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div id="additionalTableContainer"></div>

                </div>

                <div class="mt-3  text-end">
                    <button class="btn" type="button" data-bs-dismiss="offcanvas">
                        Kembali
                    </button>
                    <button type="submit" class="btn  btn-primary float-right">Simpan</button>
                </div>
            </form>


        </div>
    </div>
</div>

<!-- Modal Tambah Role -->
<div class="modal fade" id="modalAddRole" tabindex="-1" aria-labelledby="modalAddRoleLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formAddRole" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row">
                <div class="col-12 mb-3">
                    <label class="form-label">App</label>
                    <select class="form-select" name="app_id" required>
                        <!-- diisi dari backend -->
                    </select>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Level User</label>
                    <select class="form-select" name="user_level_id" required>
                        <!-- diisi dari backend -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apakah anda yakin?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="confirmDelete" action="">
                <div class="modal-body">
                    Data yang dihapus tidak dapat dikembalikan!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@push('scripts')

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const colCount = $('#table-data thead th').length;
        let colCountRole = 0;

        let dataTable = $('#table-data').DataTable({
            order: [],
            fixedHeader: true,
            columnDefs: [
                { className: 'text-center', targets: 0 },
                { orderable: false, targets: colCount-1}
            ],
            pagingType: 'simple_numbers',
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": false,
            "autoWidth": true,
            "responsive": true,
            "scrollX": true,
            ajax: "{{ $urlMenu['link_json'] }}",
        });

        let dataTableRole;

        function loadForm(aksi=false) {
            $('#additionalSelectContainer').html('');
            $('#additionalTableContainer').html('');
            let password = '';
            let tableRole = '';
            if(aksi == 'edit') {
                password = `
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Reset Password (Opsional)</label>
                            <input type="password" class="form-control " name="password" >
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                `;
                tableRole = `<div class="d-flex justify-content-between align-items-center mb-2">
                        <h5>Daftar Role</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddRole">
                            Tambah Role
                        </button>
                    </div>

                    <table class="table table-vcenter table-stripe" id="table-data-role">
                        <thead>
                            <tr>
                                <th class="w-1"></th>
                                <th>
                                    Nama Aplikasi
                                </th>
                                <th>
                                    Biodata Ref
                                </th>
                                <th>
                                    Level Pengguna
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody"></tbody>
                    </table>`;
            } else {
                password = `
                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control " name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                `;
            }
            $('#additionalSelectContainer').append(password);
            $('#additionalTableContainer').append(tableRole);
            colCountRole = $('#table-data-role thead th').length;
        }

        $('.btn-insert').on('click', function() {
            try {
                $('#formMainMenu')[0].reset();
                $('#formMainMenu .is-invalid').removeClass('is-invalid');
                $('#formMainMenu .invalid-feedback').text('');

                $('#offcanvasTop').offcanvas('show');
                $('#loadingSpinnerForm').show();
                $('#formMainMenu').hide();
                loadForm('tambah');
                $('#loadingSpinnerForm').hide();
                $('#formMainMenu').show();

                $('#formMainMenu').attr('action', "{{ $urlMenu['link'] . '/tambah' }}");
                $('#offcanvasTopLabel').text("{{ 'Form Tambah ' . $title }}");
                $('#formMainMenu').attr('method', "POST");
            } catch (error) {
                showAlert('danger', 'Gagal memuat data options: ' + error);
                $('#loadingSpinnerForm').hide();
                $('#offcanvasTop').offcanvas('hide');
            }

        });

        $('.btn-refresh').on('click', function () {
            const btn = $(this);

            // Cek apakah tombol sedang cooldown
            if (btn.hasClass('on-cooldown')) return;

            // Reset global search
            dataTable.search('');

            // Reset sorting (urutan)
            dataTable.order([]); // menghapus semua sorting

            // Reset semua pencarian kolom (jika ada input per kolom)
            dataTable.columns().every(function () {
                this.search('');
            });

           let colCount = dataTable.columns().count();
            let loadingRow = `<tr><td colspan="${colCount}" class="text-center">Loading...</td></tr>`;
            $('#table-data tbody').html(loadingRow);

            dataTable.ajax.reload(null, true);

            // Tambahkan class cooldown & efek visual
            btn.addClass('on-cooldown').css({
                pointerEvents: 'none',
                opacity: 0.5
            });

            // Hapus cooldown setelah 3 detik
            setTimeout(() => {
                btn.removeClass('on-cooldown').css({
                    pointerEvents: '',
                    opacity: ''
                });
            }, 3000);
        });


        $('#table-data').on('click', '.btn-edit', function() {
            let urlShow = $(this).data('url-show');
            let urlUpdate = $(this).data('url-update');
            let urlRole = $(this).data('url-role');

            try {
                $('#offcanvasTop').offcanvas('show');
                $('#loadingSpinnerForm').show();
                $('#formMainMenu').hide();
                loadForm('edit');

                $.ajax({
                    url: urlShow,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response, textStatus, xhr) {
                        $('#formMainMenu .is-invalid').removeClass('is-invalid');
                        $('#formMainMenu .invalid-feedback').text('');
                        let data = response.data;
                        $('#offcanvasTopLabel').text("{{ 'Form Edit ' . $title }}");
                        $('#formMainMenu [name="username"]').val(data.username);
                        $('#formMainMenu [name="email"]').val(data.email);
                        $('#formMainMenu [name="phone_number"]').val(data.phone_number);
                        $('#formMainMenu [name="status"]').val(data.status).trigger('change');
                        $('#formMainMenu').attr('method', "PUT");
                        $('#formMainMenu').attr('action', urlUpdate);
                        $('#loadingSpinnerForm').hide();
                        $('#formMainMenu').show();

                        dataTableRole = $('#table-data-role').DataTable({
                            order: [],
                            fixedHeader: true,
                            columnDefs: [
                                { className: 'text-center', targets: 0 },
                                { orderable: false, targets: colCountRole-1}
                            ],
                            pagingType: 'simple_numbers',
                            "paging": false,
                            "lengthChange": true,
                            "searching": true,
                            "ordering": true,
                            "info": false,
                            "autoWidth": true,
                            "responsive": true,
                            "scrollX": true,
                            ajax: urlRole,
                        });
                    },
                    error: function(xhr) {
                        const message = parseXhrMessage(xhr);
                        const status = xhr.status;

                        if (status === 400 || status === 422) {
                            showAlert('warning', message);
                        } else if (status === 401 || status === 403) {
                            showAlert('warning', message);
                        } else if (status === 404) {
                            showAlert('danger', 'Endpoint tidak ditemukan.');
                        } else {
                            showAlert('danger', message);
                        }
                    }
                });
            } catch (error) {
                showAlert('danger', 'Gagal memuat data options: ' + error);
                $('#loadingSpinnerForm').hide();
                $('#offcanvasTop').offcanvas('hide');
            }

        });

        $('#table-data').on('click', '.btn-delete', function() {
            let urlDelete = $(this).data('url-delete');
            $('#confirmDelete').attr('action', urlDelete);
            $('#confirmDelete').attr('method', "DELETE");
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                success: function(response) {
                    showAlert('success', response.message);
                },
                error: function(xhr) {
                    const message = parseXhrMessage(xhr);
                    const status = xhr.status;

                    if (status === 400 || status === 422) {
                        showAlert('warning', message);
                    } else if (status === 401 || status === 403) {
                        showAlert('warning', message);
                    } else if (status === 404) {
                        showAlert('danger', 'Endpoint tidak ditemukan.');
                    } else {
                        showAlert('danger', message);
                    }
                },
                complete: function () {
                    $('#deleteModal').modal('hide');
                    dataTable.ajax.reload(null, false);
                }
            });
        });

        $('#formMainMenu').on('submit', function(e) {
            e.preventDefault();

            let $submitButton = $('#formMainMenu button[type="submit"]');
            $submitButton.prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function() {
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                },
                success: function(response) {
                    $submitButton.prop('disabled', false);
                    $('#offcanvasTop').offcanvas('hide');
                    dataTable.ajax.reload(null, false);
                    showAlert('success', response.message);
                },
                error: function(xhr, textStatus, errorThrown) {
                    let errors = xhr.responseJSON.messages;
                    $submitButton.prop('disabled', false);
                    if (errors && typeof errors === 'object' && errors !== null && !Array.isArray(errors)) {
                        Object.entries(errors).forEach(([key, val]) => {
                            $('[name="' + key + '"]').addClass('is-invalid');
                            $('[name="' + key + '"]').next('.invalid-feedback')
                                .text(val[0]);
                        });
                    } else {
                        const message = parseXhrMessage(xhr);
                        const status = xhr.status;

                        if (status === 400 || status === 422) {
                            showAlert('warning', message);
                        } else if (status === 401 || status === 403) {
                            showAlert('warning', message);
                        } else if (status === 404) {
                            showAlert('danger', 'Endpoint tidak ditemukan.');
                        } else {
                            showAlert('danger', message);
                        }
                        $('#offcanvasTop').offcanvas('hide');
                    }
                },
            });
        });

    })
</script>

@endpush
