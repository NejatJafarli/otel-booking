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
            <div class="col-9">
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
                                                    <div class="mb-3">
                                                        <label for="room_price" class="form-label">Oda Fiyati</label>
                                                        <input type="text" class="form-control" id="room_price"
                                                            name="room_price" value="{{ old('room_price') }}">
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
                                        <th class="pt-0">Transaction Id</th>
                                        <th colspan="2" class="pt-0">Kiralayan Kullanici</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($rooms as $room)
                                        <tr>
                                            <td>{{ $room->id }}</td>
                                            <td>{{ $room->room_number }}</td>
                                            <td>{{ $room->room_type->room_type }}</td>
                                            <td>{{ $room->room_price }}</td>

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
                                                <td>{{ $transaction->transaction_id }}</td>
                                                <td>{{ $user->username }}</td>
                                            @else
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @endif


                                            {{-- <td>{{ $room->created_at }}</td> --}}
                                        </tr>
                                    @endforeach
                                    {{-- <tr>
                                        <td>1</td>
                                        <td>Kaan Pargan</td>
                                        <td>kaanpargan@gmail.com</td>
                                        <td>+905338436662</td>
                                        <td>2022-11-09 22:06:39</td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-xs btn-primary btn-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-check-square">
                                                    <polyline points="9 11 12 14 22 4"></polyline>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger btn-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-x-square">
                                                    <rect x="3" y="3" width="18" height="18"
                                                        rx="2" ry="2"></rect>
                                                    <line x1="9" y1="9" x2="15" y2="15">
                                                    </line>
                                                    <line x1="15" y1="9" x2="9" y2="15">
                                                    </line>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr> --}}
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
    @endsection
