@extends('layout/master')

@php
    use App\Models\transaction;
    use App\Models\User;
@endphp

@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endsection
@section('content')
    {{-- select hotel  --}}
    <div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Toplam Oda Turu Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $room_type_count }}</h3>
                            <div class="d-flex align-items-baseline">
                            </div>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- //select hotel --}}
        <div class="col-5 pb-3">
            <div class="card">
                <div class="card-body">
                    <form id="selected_hotel_id" action="{{ route('roomTypes') }}" method="GET">
                        <select id="select_hotel_id" class="form-select" aria-label="Default select example" name="hotel_id">
                            <option value="-1" selected>Otel Seciniz</option>
                            @foreach ($hotels as $hotel)
                                <option value="{{ $hotel->id }}" {{request()->query('hotel_id')==$hotel->id?"selected":""}}>{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </form>
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
                            <h6 class="card-title mb-0">Oda Turleri</h6>
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
                                            <h5 class="modal-title" id="exampleModalLabel">Oda Turu Ekle</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('createRoomType') }}" method="POST">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="room_type" class="form-label">Oda Tipi ismi</label>
                                                    <input type="text" class="form-control" id="room_type"
                                                        name="room_type" value="{{ old('room_type') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="room_price" class="form-label">Oda Tipi Fiyati</label>
                                                    <input type="text" class="form-control" id="room_price"
                                                        name="room_price" value="{{ old('room_price') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="room_type" class="form-label">Bu Oda Turu Hangi Otele
                                                        Ait</label>
                                                    {{-- <input type="text" class="form-control" id="room_type"
                                                        name="room_type"> --}}
                                                    {{-- //select ile oda turu secilecek foreach types --}}
                                                    <select class="form-select" aria-label="Default select example"
                                                        name="hotel_id">
                                                        @php
                                                            $selected = old('hotel_id');
                                                        @endphp

                                                        @if ($selected == -1)
                                                            <option value="-1" selected>
                                                                Bu Fiyatlandirma Hangi Otele Ait Seciniz</option>
                                                        @else
                                                            <option value="-1">
                                                                Bu Fiyatlandirma Hangi Otele Ait Seciniz</option>
                                                        @endif
                                                        @foreach ($hotels as $hotel)
                                                            <option value="{{ $hotel->id }}"
                                                                @if ($selected == $hotel->id) selected @endif>
                                                                {{ $hotel->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                    <th class="pt-0">Oda Tipi Ismi</th>
                                    <th class="pt-0">ait oldugu Hotel Ismi</th>
                                    <th class="pt-0">Oda Fiyati</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($types as $type)
                                    <tr>
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->room_type }}</td>
                                        <td>{{ $type->hotel()->first()->name }}</td>
                                        <td>{{ $type->room_price }}</td>
                                        <td class="text-right">
                                            <button
                                                onclick="editRoomType(
                                                    { id:{{ $type->id }},hotel_id:{{ $type->hotel()->first()->id }},room_type:'{{ $type->room_type }}',room_price:{{ $type->room_price }}})"
                                                type="button" class="btn btn-xs btn-primary btn-icon"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="link-icon" data-feather="edit"></i>
                                            </button>

                                            <button onclick="deleteRoomType({{ $type->id }})" type="button"
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

    {{-- edit modal --}}
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Oda Turu Duzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- action="{{ route('createRoomType') }}" method="POST" --}}
                <form>
                    <input type="hidden" id="edit_id">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="room_number" class="form-label">Oda Turu Ismi</label>
                            <input type="text" class="form-control" id="edit_room_type">
                        </div>
                        <div class="mb-3">
                            <label for="room_number" class="form-label">Oda Turu Fiyati</label>
                            <input type="text" class="form-control" id="edit_room_price">
                        </div>

                        <div class="mb-3">
                            <label for="room_type" class="form-label">Bu Oda Turu Hangi Otele Ait</label>
                            {{-- <input type="text" class="form-control" id="room_type"
                                name="room_type"> --}}
                            {{-- //select ile oda turu secilecek foreach types --}}
                            <select class="form-select" aria-label="Default select example" id="edit_hotel_id">
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" @if ($selected == $hotel->id) selected @endif>
                                        {{ $hotel->name }}
                                    </option>
                                @endforeach
                            </select>
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
        function deleteRoomType(id) {
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
                    let url = "{{ route('deleteRoomType', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Oda Turu basariyla silindi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Oda Turu silinemedi.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })

        }

        function editRoomType(json) {
            console.log("hello");
            //get element by id edit_id
            let id = document.getElementById('edit_id');
            //set value
            id.value = json.id;
            //get element by id edit_room_number
            let room_type = document.getElementById('edit_room_type');
            //set value
            room_type.value = json.room_type;
            //get element by id edit_room_type
            let room_price = document.getElementById('edit_room_price');
            //set value
            room_price.value = json.room_price;

            let hotel_id = document.getElementById('edit_hotel_id');
            hotel_id.value = json.hotel_id;
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
                        room_type_id: $('#edit_id').val(),
                        room_type_name: $('#edit_room_type').val(),
                        room_price: $('#edit_room_price').val(),
                        hotel_id: $('#edit_hotel_id').val(),
                    }
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "{{ route('editRoomType') }}",
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Degistirildi!',
                                    'Oda Turu basariyla degistirildi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Oda Turu degistirilemedi.',
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


        //$("#selected_hotel_id") onchange submit
        $("#select_hotel_id").change(function() {
            $("#selected_hotel_id").submit();
        });
    </script>
@endsection
