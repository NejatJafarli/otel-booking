@extends('layout/master')

@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
    <div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Kullanici Bilgileri</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            {{-- //show user info like $user->username --}}
                            <h5 class="my-2">username: {{ $user->username }}</h5>
                            <h5 class="mb-2">email: {{ $user->email }}</h5>
                            <h5 class="mb-2">wallet_id: {{ $user->wallet_id }}</h5>
                            <h5 class="mb-2">Character Number: {{ $user->character_number }}</h5>
                            {{-- //delete user button --}}
                            <button type="button" onclick="deleteUser({{ $user->id }})"
                                class="btn btn-danger">Kullaniciyi Sil</button>
                            {{-- //update user button --}}
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal2"
                                class="btn btn-primary">Kullaniciyi Guncelle</button>


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
                            <h6 class="card-title mb-0">Oda Satin Alimlari</h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="MyDataTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Odanin Ait Oldugu Otel Ismi</th>
                                    <th class="pt-0">Oda numarasi</th>
                                    <th class="pt-0">Oda Tipi</th>
                                    <th class="pt-0">Giris Tarihi</th>
                                    <th class="pt-0">Cikis Tarihi</th>
                                    <th class="pt-0">Odenen Fiyati</th>
                                    <th class="pt-0">Odeme Durumu</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($userRoomTrans as $trans)
                                    <tr>
                                        <td>{{ $trans->id }}</td>
                                        <td>{{ $trans->room()->first()->room_type()->first()->hotel()->first()->name }}</td>
                                        <td>{{ $trans->room()->first()->room_number }}</td>
                                        <td>{{ $trans->room()->first()->room_type()->first()->room_type }}</td>
                                        <td>{{ date('d-m-Y  H:i:s ', strtotime($tran->check_in_date)) }}</td>
                                        <td>{{ date('d-m-Y  H:i:s ', strtotime($tran->check_out_date)) }}</td>
                                        <td>{{ $trans->transaction_amount }}</td>
                                        @if ($tran->transaction_status == 0)
                                            <td><span class="badge bg-success">Onaylandi</span></td>
                                        @elseif($tran->transaction_status == 2)
                                            <td><span class="badge bg-warning">Onay Bekliyor</span></td>
                                        @elseif($tran->transaction_status == 1)
                                            <td><span class="badge bg-danger">Iptal Edildi</span></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 pt-5">
            <div class="card">
                <div class="card-body">

                    <div class="col-11">
                        <h6 class="card-title mb-0">Hotel Giris Satin Alimlari</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="MyDataTable2" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pt-0">#</th>
                                <th class="pt-0">Satin Alinan Otel Ismi</th>
                                <th class="pt-0">Odenen Fiyat</th>
                                <th class="pt-0">Giris Tarihi</th>
                                <th class="pt-0">Cikis Tarihi</th>
                                <th class="pt-0">Odeme Durumu</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($userHotelTrans as $trans)
                                <tr>
                                    <td>{{ $trans->id }}</td>
                                    <td>{{ $trans->hotel()->first()->name }}</td>
                                    <td>{{ $trans->transaction_amount }}</td>
                                    <td>{{ date('d-m-Y  H:i:s ', strtotime($tran->check_in_date)) }}</td>
                                    <td>{{ date('d-m-Y  H:i:s ', strtotime($tran->check_out_date)) }}</td>
                                    <td>{{ $trans->room()->first()->room_type()->first()->room_price }}</td>
                                    @if ($tran->transaction_status == 0)
                                        <td><span class="badge bg-success">Onaylandi</span></td>
                                    @elseif($tran->transaction_status == 2)
                                        <td><span class="badge bg-warning">Onay Bekliyor</span></td>
                                    @elseif($tran->transaction_status == 1)
                                        <td><span class="badge bg-danger">Iptal Edildi</span></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- edit modal --}}
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kullaniciyi Duzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- action="{{ route('createRoomType') }}" method="POST" --}}
                <form>
                    <input type="hidden" id="edit_id">
                    <div class="modal-body">
                        @csrf
                        <label for="username" class="form-label">Kullanici username</label>
                        <input type="text" class="form-control" id="edit_username" name="username"
                            value="{{ $user->username }}">
                        <label for="email" class="form-label">Kullanici Email (not
                            required)</label>
                        <input type="email" class="form-control" id="edit_email" name="email"
                            value="{{ $user->email }}">
                        <label for="email" class="form-label">Kullanici Wallet Id</label>
                        <input type="text" class="form-control" id="edit_wallet_id" name="wallet_id"
                            value="{{ $user->wallet_id }}">
                        <label for="email" class="form-label">Kullanici Karakter
                            Secimi</label>
                        <input type="number" class="form-control" id="edit_char_number" name="char_number"
                            value="{{ $user->character_number }}">
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
        function deleteUser(id) {
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
                    let url = "{{ route('deleteUser', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Kullanici basariyla silindi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Kullanici silinemedi.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })

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
                        username: $('#edit_username').val(),
                        wallet_id: $('#edit_wallet_id').val(),
                        email: $('#edit_email').val(),
                        char_number: $('#edit_char_number').val(),
                    }
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "{{ route('editUser') }}",
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Degistirildi!',
                                    'Kullanici basariyla degistirildi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Kullanici degistirilemedi.',
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
                $('#MyDataTable2').DataTable({
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
