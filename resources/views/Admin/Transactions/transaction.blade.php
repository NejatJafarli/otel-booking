@extends('layout/master')

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
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="pt-0">#</th>
                                    <th class="pt-0">Oda Numarasi</th>
                                    <th class="pt-0">Oda Tipi</th>
                                    <th class="pt-0">Kullanici Adi</th>
                                    <th class="pt-0">Check in Tarihi</th>
                                    <th class="pt-0">Check Out Tarihi</th>
                                    <th class="pt-0">Durum</th>
                                    <th colspan="2" class="pt-0">Oda Kayit Tarihi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $tran)
                                    <tr>
                                        <td>{{ $tran->id }}</td>
                                        <td>{{ $tran->room->room_number }}</td>
                                        <td>{{ $tran->room->room_type()->first()->room_type }}</td>
                                        <td>{{ $tran->user->username }}</td>
                                        <td>{{ $tran->check_in_date }}</td>
                                        <td>{{ $tran->check_out_date }}</td>
                                        <td>{{ $tran->status }}</td>
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
                            {{ $transactions->onEachSide(2)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
