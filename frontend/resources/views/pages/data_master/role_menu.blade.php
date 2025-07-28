@extends('layouts.app')

@section('title', $title)

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <label class="col-md-3 col-form-label text-center">Pilih Nama Aplikasi</label>
            <div class="col-md-9 mt-1">
                <select class="form-select" id="select-app" onChange="app(this.value)">
                    <option value="">Pilih Nama Aplikasi</option>
                    @if ($options['appData'])
                        @foreach ($options['appData'] as $item)
                            <option value="{{ $item->id }}" @selected($options['appDataValue'] == $item->id)>
                                {{ $item->app_name }}
                            </option>
                        @endforeach
                    @endif
                </select>

            </div>
        </div>
        @if($options['userLevelData'])
            <div class="row">
                <label class="col-md-3 col-form-label text-center">Pilih Level User</label>
                <div class="col-md-9 mt-1">
                    <select class="form-select" id="select-app" onChange="user_level('{{$options['appDataValue']}}',this.value)">
                        <option value="">Pilih Level User</option>
                        @if ($options['userLevelData'])
                            @foreach ($options['userLevelData'] as $item)
                                <option value="{{ $item->id }}" @selected($options['userLevelDataValue'] == $item->id)>
                                    {{ $item->user_level_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>

                </div>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-table">
        <div class="card-header">
            <div class="row w-full">
                <div class="col">
                    <h3 class="card-title mb-0">Tabel {{ $title }}</h3>
                </div>
                <div class="col-md-auto col-sm-12">
                    <div class="ms-auto d-flex flex-wrap btn-list">
                        @if($options['appData'] && $options['userLevelData'] && $options['userLevelDataValue'])
                        <a href="javascript:void(0);" class="btn btn-warning btn-icon btn-refresh">
                            <i class="icon ti ti-refresh"></i>
                        </a>
                        <a class="btn btn-primary btn-insert" href="javascript:void(0);">
                            <i class="ti ti-plus"></i>
                            Tambah
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
            <div class="table-responsive">
                <div id="tableLoader" class="text-center my-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat data, harap tunggu...</p>
                </div>
                <table class="table table-vcenter table-stripe nowrap" id="table-data">
                    <thead>
                        <tr>
                            <th class="w-1">No</th>
                            <th>
                                Kode Menu
                            </th>
                            <th>
                                Nama Menu
                            </th>
                            <th>
                                Tipe Menu
                            </th>
                            <th>
                                Kode Parent Menu
                            </th>
                            <th>
                                Icon Menu
                            </th>
                            <th>
                                Url Menu
                            </th>
                            <th>
                                Lihat
                            </th>
                            <th>
                                Tambah
                            </th>
                            <th>
                                Ubah
                            </th>
                            <th>
                                Hapus
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

                    <div id="additionalSelectContainer"></div>

                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Ikon Menu</label>
                            <input type="text" class="form-control " name="menu_icon" >
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">Url Menu</label>
                            <input type="text" class="form-control " name="menu_url" >
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label class="form-label">No Urut Menu</label>
                            <input type="text" class="form-control " name="role_order" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3" >
                            <label class="form-label">Lihat</label>
                            <select class="form-select" name="role_view" required>
                                <option value="">Pilih Lihat</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3" >
                            <label class="form-label">Tambah</label>
                            <select class="form-select" name="role_create" required>
                                <option value="">Pilih Status Tambah</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3" >
                            <label class="form-label">Ubah</label>
                            <select class="form-select" name="role_update" required>
                                <option value="">Pilih Status Ubah</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3" >
                            <label class="form-label">Hapus</label>
                            <select class="form-select" name="role_delete" required>
                                <option value="">Pilih Status Hapus</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3" id="newSelectForm">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Pilih Status Menu</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
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
                    <input type="hidden" name="idUser" id="deleteIdUser">
                    <input type="hidden" name="idAlat" id="deleteIdAlat">
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
<script src="{{ asset('libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const colCount = $('#table-data thead th').length;

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

        function loadForm() {
            $('#additionalSelectContainer').html('');
            let menu = `
                <div class="col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Menu</label>
                        <select class="form-select" name="menu_id" id="menu_id" required>
                            <option value="">Pilih Menu</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            `;
            $('#additionalSelectContainer').append(menu);
            let parent_menu_code = `
                <div class="col-sm-12">
                    <div class="mb-3">
                        <label class="form-label">Parent Menu</label>
                        <select class="form-select" name="parent_menu_code" id="parent_menu_code">
                            <option value="">Pilih Parent Menu</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            `;
            $('#additionalSelectContainer').append(parent_menu_code);
        }

        function loadOptions() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ $urlMenu['link_options'] }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let menuOptions = '<option value="">Pilih Menu</option>';
                        response.data.menu.forEach(function(menu) {
                            menuOptions +=
                                `<option value="${menu.id}">${menu.menu_name}</option>`;
                        });
                        $('[name="menu_id"]').html(menuOptions);

                        let parentMenuOptions = '<option value="">Pilih Parent Menu</option>';
                        response.data.parent_menu.forEach(function(parent_menu) {
                            parentMenuOptions +=
                                `<option value="${parent_menu.menu_code}">${parent_menu.menu_name}</option>`;
                        });
                        $('[name="parent_menu_code"]').html(parentMenuOptions);

                        resolve(); // Beritahu bahwa proses selesai
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
                        reject(xhr.responseText); // Beritahu bahwa ada error
                    }
                });
            });
        }

        $('.btn-insert').on('click', async function() {
            try {
                $('#formMainMenu')[0].reset();
                $('#formMainMenu .is-invalid').removeClass('is-invalid');
                $('#formMainMenu .invalid-feedback').text('');

                $('#offcanvasTop').offcanvas('show');
                $('#loadingSpinnerForm').show();
                $('#formMainMenu').hide();
                loadForm();
                await loadOptions();
                $('#loadingSpinnerForm').hide();
                $('#formMainMenu').show();

                $('#formMainMenu').attr('action', "{{ $urlMenu['link_tambah'] }}");
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


        $('#table-data').on('click', '.btn-edit', async function() {
            let urlShow = $(this).data('url-show');
            let urlUpdate = $(this).data('url-update');

            try {
                $('#offcanvasTop').offcanvas('show');
                $('#loadingSpinnerForm').show();
                $('#formMainMenu').hide();
                loadForm();
                await loadOptions();


                $.ajax({
                    url: urlShow,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response, textStatus, xhr) {
                        $('#formMainMenu .is-invalid').removeClass('is-invalid');
                        $('#formMainMenu .invalid-feedback').text('');

                        let data = response.data;
                        $('#offcanvasTopLabel').text("{{ 'Form Edit ' . $title }}");
                        $('#formMainMenu [name="menu_id"]').val(data.menu_id).trigger('change');
                        $('#formMainMenu [name="parent_menu_code"]').val(data.parent_menu_code).trigger('change');
                        $('#formMainMenu [name="menu_icon"]').val(data.menu_icon);
                        $('#formMainMenu [name="menu_url"]').val(data.menu_url);
                        $('#formMainMenu [name="role_order"]').val(data.role_order);
                        let role_view = (data.role_view == true || data.role_view == "true" || data.role_view == 1 || data.role_view == "1") ? '1' : '0';
                        $('#formMainMenu [name="role_view"]').val(role_view).trigger('change');
                        let role_create = (data.role_create == true || data.role_create == "true" || data.role_create == 1 || data.role_create == "1") ? '1' : '0';
                        $('#formMainMenu [name="role_create"]').val(role_create).trigger('change');
                        let role_update = (data.role_update == true || data.role_update == "true" || data.role_update == 1 || data.role_update == "1") ? '1' : '0';
                        $('#formMainMenu [name="role_update"]').val(role_update).trigger('change');
                        let role_delete = (data.role_delete == true || data.role_delete == "true" || data.role_delete == 1 || data.role_delete == "1") ? '1' : '0';
                        $('#formMainMenu [name="role_delete"]').val(role_delete).trigger('change');
                        let status = (data.status == true || data.status == "true" || data.status == 1 || data.status == "1") ? '1' : '0';
                        $('#formMainMenu [name="status"]').val(status).trigger('change');
                        $('#formMainMenu').attr('method', "PUT");
                        $('#formMainMenu').attr('action', urlUpdate);
                        $('#loadingSpinnerForm').hide();
                        $('#formMainMenu').show();
                    },
                    error: function(xhr) {
                        console.log('test')
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
                        $('#loadingSpinnerForm').hide();
                        $('#offcanvasTop').offcanvas('hide');
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

<script>

    function app(app_id) {
        if (app_id) {
            window.location = "{{ $urlMenu['link'] }}" + "?app_id=" + app_id;
        } else {
            window.location = "{{ $urlMenu['link'] }}";
        }

    }


    function user_level(app_id, user_level_id) {
        if (app_id && user_level_id) {
            window.location = "{{ $urlMenu['link'] }}" + "?app_id=" + app_id + "&user_level_id=" + user_level_id;
        } else {
            window.location = "{{ $urlMenu['link'] }}" + "?app_id=" + app_id;
        }

    }


</script>

@endpush
