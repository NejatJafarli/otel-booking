@extends('layout/master')

@php
    use App\Models\transaction;
    use App\Models\User;
@endphp
@section('content')
    <div class="row flex-grow-1">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Toplam Oda</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $rooms_count }}</h3>
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
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Aktif Kullanilan Odalar</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $rooms_count_1 }}</h3>
                            <div class="d-flex align-items-baseline">
                            </div>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7">
                            <div id="ordersChart" class="mt-md-3 mt-xl-0"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
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
                                <h6 class="card-title mb-0">Odalar</h6>
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
                                                <h5 class="modal-title" id="exampleModalLabel">Oda Ekle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('CreateRoom') }}" method="POST">
                                                <div class="modal-body">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="room_number" class="form-label">Oda Numarasi</label>
                                                        <input type="text" class="form-control" id="room_number"
                                                            name="room_number" value="{{ old('room_number') }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="room_type" class="form-label">Oda Turu</label>
                                                        {{-- <input type="text" class="form-control" id="room_type"
                                                            name="room_type"> --}}
                                                        {{-- //select ile oda turu secilecek foreach types --}}
                                                        <select class="form-select" aria-label="Default select example"
                                                            name="room_type">
                                                            @php
                                                                $selected = old('room_type');
                                                            @endphp

                                                            @if ($selected == -1)
                                                                <option value="-1" selected>
                                                                    Oda Turu Seciniz</option>
                                                            @else
                                                                <option value="-1">
                                                                    Oda Turu Seciniz</option>
                                                            @endif
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type->id }}"
                                                                    @if ($selected == $type->id) selected @endif>
                                                                    {{ $type->room_type }}
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
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="pt-0">#</th>
                                        <th class="pt-0">Oda Numarasi</th>
                                        <th class="pt-0">Oda Turu</th>
                                        <th class="pt-0">Oda Fiyati</th>
                                        <th class="pt-0">Oda Durumu</th>
                                        <th class="pt-0">In / Out Date</th>
                                        <th colspan="2" class="pt-0">Kiralayan Kullanici</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($rooms as $room)
                                        <tr>
                                            <td>{{ $room->id }}</td>
                                            <td>{{ $room->room_number }}</td>
                                            <td>{{ $room->room_type->room_type }}</td>
                                            <td>{{ $room->room_type->room_price }}</td>

                                            @if ($room->room_status == 1)
                                                <td><span class="badge bg-warning">Dolu</span></td>
                                            @elseif($room->room_status == 0)
                                                <td><span class="badge bg-success">Bos</span></td>
                                            @endif

                                            @php
                                                //use model user
                                                
                                                $transaction = transaction::where('room_id', $room->id)->first();
                                                $in_date = $out_date = $user = '';
                                                if ($transaction) {
                                                    $in_date = $transaction->check_in_date;
                                                    $out_date = $transaction->check_out_date;
                                                    $user = User::where('wallet_id', $transaction->wallet_id)->first();
                                                }
                                                //use model user
                                            @endphp
                                            @if ($transaction)
                                                <td>{{ $in_date }} To {{ $out_date }}</td>
                                                <td>{{ $user->username }}</td>
                                                <td></td>
                                            @else
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td class="text-right">
                                                <button
                                                    onclick="editRoom(
                                                    { id:{{ $room->id }},room_number:{{ $room->room_number }},room_type:{{ $room->room_type->id }}})"
                                                    type="button" class="btn btn-xs btn-primary btn-icon"
                                                    data-bs-toggle="modal" data-bs-target="#exampleModal2">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </button>

                                                <button onclick="deleteRoom({{ $room->id }})" type="button"
                                                    class="btn btn-xs btn-danger btn-icon">
                                                    <i class="link-icon" data-feather="trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center pt-4">
                                {{ $rooms->onEachSide(2)->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- edit modal --}}
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Oda Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- action="{{ route('CreateRoom') }}" method="POST" --}}
                    <form>
                        <input type="hidden" id="edit_id">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Oda Numarasi</label>
                                <input type="text" class="form-control" id="edit_room_number">
                            </div>
                            <div class="mb-3">
                                <label for="room_type" class="form-label">Oda Turu</label>
                                <select class="form-select" aria-label="Default select example" id="edit_room_type">
                                    @foreach ($types as $type)
                                        <option value="{{ $type->id }}"
                                            @if ($selected == $type->id) selected @endif>
                                            {{ $type->room_type }}
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
        function deleteRoom(id) {
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
                    let url = "{{ route('DeleteRoom', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Oda basariyla silindi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Oda silinemedi.',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })

        }

        function editRoom(json) {
            //get element by id edit_id
            let id = document.getElementById('edit_id');
            //set value
            id.value = json.id;
            //get element by id edit_room_number
            let room_number = document.getElementById('edit_room_number');
            //set value
            room_number.value = json.room_number;
            //get element by id edit_room_type
            let room_type = document.getElementById('edit_room_type');
            //set value
            room_type.value = json.room_type;
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
                        room_id: $('#edit_id').val(),
                        room_number: $('#edit_room_number').val(),
                        room_type: $('#edit_room_type').val(),
                    }
                    console.log(data);
                    $.ajax({
                        type: "POST",
                        data: data,
                        url: "{{ route('editRoom') }}",
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Degistirildi!',
                                    'Oda basariyla degistirildi.',
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                })
                            } else {
                                Swal.fire(
                                    'Hata!',
                                    'Oda degistirilemedi.',
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
