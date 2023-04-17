@extends('layout/master')

@php
    use App\Models\transaction;
    use App\Models\User;
@endphp
@section('content')
    <div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Toplam Otel Turu Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $hotel_count }}</h3>
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
                            <h6 class="card-title mb-0">Oteller</h6>
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
                                            <h5 class="modal-title" id="exampleModalLabel">Otel Ekle</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('createHotel') }}" method="POST">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="hotelname" class="form-label">Otel Ismi</label>
                                                    <input type="text" class="form-control" id="hotelname"
                                                        name="hotelname" value="{{ old('hotelname') }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="hoteladdress" class="form-label">Otel Adresi</label>
                                                    <input type="text" class="form-control" id="hoteladdress"
                                                        name="hoteladdress" value="{{ old('hoteladdress') }}">
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
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Otel Ismi</th>
                                    <th colspan="2" class="pt-0">Otel Adresi</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($hotels as $hotel)
                                    <tr>
                                        <td>{{ $hotel->id }}</td>
                                        <td>{{ $hotel->name }}</td>
                                        <td>{{ $hotel->address }}</td>
                                        <td class="text-right">
                                            <button
                                                onclick="editHotel(
                                                    { id:{{ $hotel->id }},hotel_name:'{{$hotel->name}}',hotel_address:'{{ $hotel->address }}'})"
                                                type="button" class="btn btn-xs btn-primary btn-icon"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                <i class="link-icon" data-feather="edit"></i>
                                            </button>

                                            <button onclick="deleteHotel({{ $hotel->id }})" type="button"
                                                class="btn btn-xs btn-danger btn-icon">
                                                <i class="link-icon" data-feather="trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center pt-4">
                            {{ $hotels->onEachSide(2)->links() }}
                        </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">Oteli Duzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- action="{{ route('createRoomType') }}" method="POST" --}}
                <form>
                    <input type="hidden" id="edit_id">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="hotelname" class="form-label">Otel Ismi</label>
                            <input type="text" class="form-control" id="edit_hotelname" name="hotelname">
                        </div>
                        <div class="mb-3">
                            <label for="hoteladdress" class="form-label">Otel Adresi</label>
                            <input type="text" class="form-control" id="edit_hoteladdress" name="hoteladdress">
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
        function deleteHotel(id) {
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
                    let url = "{{ route('deleteHotel', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Otel basariyla silindi.',
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

        function editHotel(json) {

            //get element by id edit_id
            let id = document.getElementById('edit_id');
            //set value
            id.value = json.id;
            //get element by id edit_hotel_name
            let hotel_name = document.getElementById('edit_hotelname');
            //set value
            hotel_name.value = json.hotel_name;
            let hotel_address = document.getElementById('edit_hoteladdress');
            //get element by id edit_room_type
            hotel_address.value = json.hotel_address;


          
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
