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
                        <h6 class="card-title mb-0">Toplam Kullanici Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $user_count }}</h3>
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
                            <h6 class="card-title mb-0">Kullanicilar</h6>
                        </div>

                    </div>
                    <div class="table-responsive">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Data Table</h6>
                                    <p class="text-muted mb-3">Read the <a href="https://datatables.net/" target="_blank">
                                            Official
                                            DataTables Documentation </a>for a full list of instructions and other
                                        options.</p>
                                    <table id="MyDataTable" class="table">
                                        <thead>
                                            <tr>
                                                <th class="pt-0">#</th>
                                                <th class="pt-0">Kullanici Adi</th>
                                                <th class="pt-0">Kullanici Emaili</th>
                                                <th class="pt-0">Kullanici Kayit Tarihi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->username }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->created_at }}</td>
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
        </div>
    </div>
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
                    }
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
