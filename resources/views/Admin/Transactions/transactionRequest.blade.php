@extends('layout/master')
@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"
        integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous">
    </script>
@endsection
@section('content')
    <div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Toplam Transaction Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $trans_count }}</h3>
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
                            <h6 class="card-title mb-0">Transactions</h6>
                        </div>

                    </div>
                    <div class="table-responsive">

                        <table id="MyDataTable" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Oda Numarasi</th>
                                    <th class="pt-0">Oda Tipi</th>
                                    <th class="pt-0">Otel Ismi</th>
                                    <th class="pt-0">Kullanici Adi</th>
                                    <th class="pt-0">Trancation Id</th>
                                    <th class="pt-0">Transaction Turu</th>
                                    <th class="pt-0">Islem Tarihi</th>
                                    <th class="pt-0">Durum</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $tran)
                                    <tr>
                                        <td>{{ $tran->id }}</td>
                                        @if ($tran->room_id == null)
                                            <td>Yok</td>
                                            <td>Yok</td>
                                            <td>{{ $tran->hotel()->first()->name }}</td>
                                        @else
                                            <td>{{ $tran->room->room_number }}</td>
                                            <td>{{ $tran->room->room_type()->first()->room_type }}</td>
                                            <td>{{ $tran->room->room_type()->first()->hotel()->first()->name }}</td>
                                        @endif
                                        @if ($tran->user != null)
                                            <td>{{ $tran->user->username }}</td>
                                        @else
                                            <td>Kullanici Bulunamadi</td>
                                        @endif
                                        {{-- //tranactions status with badge  --}}

                                        <td>{{ $tran->tran_id }}</td>
                                        {{-- <td>{{$tran->created_at}}</td> created at is 
                                        2023-04-18 
                                        18-04-2023
                                        --}}
                                        @php
                                            if ($tran->room_id != null && $tran->hotel_id == null) {
                                                echo '<td>Oda Satin Alim</td>';
                                            } elseif ($tran->hotel_id != null && $tran->room_id == null) {
                                                echo '<td>Hotel Giris Istegi Satin Alim</td>';
                                            } else {
                                                echo '<td>HATA HEM OTEL HEM ODA ID SI NULL DEGIL VEYA IKISIDE NULL</td>';
                                            }
                                        @endphp

                                        <td>{{ date('d-m-Y  H:i:s ', strtotime($tran->created_at)) }}</td>

                                        {{-- //tranactions status with badge  --}}

                                        <td><span class="badge bg-warning">Onay Bekliyor</span></td>

                                        <td class="text-right">
                                            <button
                                                onclick="ConfirmTranOrReject('{{ $tran->id }}',0,'{{ $tran->user_id }}')"
                                                type="button" class="btn btn-xs btn-success btn-icon"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="link-icon" data-feather="check"></i>
                                            </button>

                                            <button
                                                onclick="ConfirmTranOrReject('{{ $tran->id }}',1,'{{ $tran->user_id }}')"
                                                type="button" class="btn btn-xs btn-danger btn-icon">
                                                <i class="link-icon" data-feather="x"></i>
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
    @endsection
    @section('js')
        <script>
            var socket = io.connect('https://cyprusvarosha.com');

            function ConfirmTranOrReject(id, status, user_id) {

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
                    confirmButtonText: 'Evet, onayla!',
                    cancelButtonText: 'Hayir, iptal et!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        //ajax ile silme islemi
                        let data = {
                            _token: "{{ csrf_token() }}",
                            transaction_id: id,
                            transaction_status: status,
                        }
                        let url = "{{ route('confirmOrRejectTransaction') }}";
                        $.ajax({
                            type: "POST",
                            data: data,
                            url: url,
                            success: function(response) {
                                console.log(response);
                                let message = "";
                                if (status == 0)
                                    message = "Your purchase request has been approved.";
                                else
                                    message = "Your purchase request has been rejected.";

                                if (response.status) {
                                    Swal.fire(
                                        'Basarili!',
                                        'Islem basariyla gerceklestirildi.',
                                        'success'
                                    ).then((result) => {
                                        if (result.isConfirmed) {

                                            socket.emit('SEND_SPECIFIC_MESSAGE_BY_USER_ID', {
                                                message: message,
                                                id: user_id
                                            });

                                            location.reload();
                                        }
                                    })
                                } else {
                                    Swal.fire(
                                        'Hata!',
                                        response.message,
                                        'error'
                                    )
                                }
                            }
                        });
                    }
                })

            }
        </script>

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
