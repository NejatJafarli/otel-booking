@extends('layout/master')

@php
    use App\Models\transaction;
    use App\Models\User;
@endphp

@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
    <div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Toplam Config Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $config_count }}</h3>
                            <div class="d-flex align-items-baseline">
                            </div>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7">
                            <div id="customersChart" class="mt-md-3 mt-xl-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- //show error messages --}}
                    @if ($errors->any())
                        <div class="alert alert-danger solid alert-dismissible fade show">
                            <h5>Hata</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- //show success messages --}}
                    @if (session('success'))
                        <div class="alert alert-success solid alert-dismissible fade show">
                            <h5>Basarili</h5>
                            <ul class="mb-0">
                                <li>{{ session('success') }}</li>
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-11">
                            <h6 class="card-title mb-0">Configler</h6>
                        </div>
                        <div class="col-1">
                            {{-- //+ butonu ile odalar eklenecek  MODAL ACILACAK --}}
                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                +
                            </button>
                            {{-- // add room modal --}}
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Config Ekle</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('createMyConfig') }}" method="POST">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="hotelname" class="form-label">Anahtar Isim</label>
                                                    <input type="text" class="form-control" id="key" name="key"
                                                        value="{{ old('key') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="hotelprice" class="form-label">Deger</label>
                                                    <input type="text" class="form-control" id="value" name="value"
                                                        value="{{ old('value') }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Kaydet</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="MyDataTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Key</th>
                                    <th class="pt-0">Value</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($configs as $con)
                                    <tr>
                                        <td>{{ $con->id }}</td>
                                        <td>{{ $con->key }}</td>
                                        <td>{{ $con->value }}</td>
                                        <td class="text-right">
                                            <button
                                                onclick="editMyConfig(
                                                    { id:{{ $con->id }},key:'{{ $con->key }}',value:'{{ $con->value }}'})"
                                                type="button" class="btn btn-xs btn-primary btn-icon"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="link-icon" data-feather="edit"></i>
                                            </button>

                                            <button onclick="deleteMyConfig({{ $con->id }})" type="button"
                                                class="btn btn-xs btn-danger btn-icon">
                                                <i class="link-icon" data-feather="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- edit modal --}}
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Config Duzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- action="{{ route('createRoomType') }}" method="POST" --}}
                <form>
                    <input type="hidden" id="edit_id">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="hotelname" class="form-label">Anahtar Isim</label>
                            <input type="text" class="form-control" id="edit_key">
                        </div>
                        <div class="mb-3">
                            <label for="hotelprice" class="form-label">Deger</label>
                            <input type="text" class="form-control" id="edit_value">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button onclick="ConfirmEdit()" type="button" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        function deleteMyConfig(id) {
            //swal ile silme onayi
            swal.fire({
                title: 'Emin misiniz?',
                text: "Bu islem geri alinamaz!",
                icon: 'warning',
                //change content color
                customClass: {
                    content: 'text-dark'
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayir, iptal et!'
            }).then((result) => {
                if (result.isConfirmed) {
                    //ajax ile silme islemi
                    let url = "{{ route('deleteMyConfig', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Config basariyla silindi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Config silinemedi.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })

        }

        function editMyConfig(json) {

            //get element by id edit_id
            let id = document.getElementById('edit_id');
            //set value
            id.value = json.id;
            //get element by id edit_hotel_name
            let editkey = document.getElementById('edit_key');
            //set value
            editkey.value = json.key;
            //get element by id edit_hotel_name
            let editvalue = document.getElementById('edit_value');
            //set value
            editvalue.value = json.value;
        }

        function ConfirmEdit() {
            //swal ile silme onayi
            swal.fire({
                title: 'Emin misiniz?',
                text: "Bu islem geri alinamaz!",
                icon: 'warning',
                //change content color
                customClass: {
                    content: 'text-dark'
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, degistir!',
                cancelButtonText: 'Hayir, iptal et!'
            }).then((result) => {
                if (result.isConfirmed) {
                    //ajax ile silme islemi
                    let data = {
                        _token: "{{ csrf_token() }}",
                        id: $('#edit_id').val(),
                        key: $('#edit_key').val(),
                        value: $('#edit_value').val(),
                    }
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "{{ route('editMyConfig') }}",
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Degistirildi!',
                                    'Config basariyla degistirildi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Config degistirilemedi.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })

        }
    </script>
@endsection

@section('js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // npm package: datatables.net-bs5
        // github link: https://github.com/DataTables/Dist-DataTables-Bootstrap5
        // npm package: datatables.net-bs5
        // github link: https://github.com/DataTables/Dist-DataTables-Bootstrap5

        $(function() {
            'use strict';

            $(function() {
                $('#MyDataTable').DataTable({
                    "aLengthMenu": [
                        [10, 30, 50, -1],
                        [10, 30, 50, "All"]
                    ],
                    "iDisplayLength": 10,
                    "language": {
                        search: ""
                    },
                    "order": [
                        [0, "desc"]
                    ]
                });
                $('#MyDataTable').each(function() {
                    var datatable = $(this);
                    // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                    var search_input = datatable.closest('.dataTables_wrapper').find(
                        'div[id$=_filter] input');
                    search_input.attr('placeholder', 'Search');
                    search_input.removeClass('form-control-sm');
                    // LENGTH - Inline-Form control
                    var length_sel = datatable.closest('.dataTables_wrapper').find(
                        'div[id$=_length] select');
                    length_sel.removeClass('form-control-sm');
                });
            });

        });
    </script>
@endsection
