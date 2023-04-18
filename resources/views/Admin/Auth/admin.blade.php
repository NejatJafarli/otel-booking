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
                        <h6 class="card-title mb-0">Toplam Admin Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 class="mb-2">{{ $admin_count }}</h3>
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
                            <h6 class="card-title mb-0">Adminler</h6>
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
                                            <h5 class="modal-title" id="exampleModalLabel">Admin Ekle</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('adminRegister') }}" method="POST">
                                            <div class="modal-body">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="adminusername" class="form-label">Admin username</label>
                                                    <input type="text" class="form-control" id="adminusername"
                                                        name="adminusername" value="{{ old('adminusername') }}">
                                                    <label for="adminemail" class="form-label">Admin Email (not
                                                        required)</label>
                                                    <input type="email" class="form-control" id="adminemail" name="adminemail"
                                                        value="{{ old('adminemail') }}">
                                                    <label for="email" class="form-label">Admin Role</label>
                                                    <input type="text" class="form-control" id="adminrole"
                                                        name="adminrole" value="{{ old('adminrole') }}">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password" class="form-control" id="adminpassword"
                                                        name="adminpassword" value="{{ old('adminpassword') }}">
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
                                    <th class="pt-0">Admin Username</th>
                                    <th class="pt-0">Admin Email</th>
                                    <th class="pt-0">Admin Role</th>
                                    <th class="pt-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($admins as $admin)
                                    <tr>
                                        <td>{{ $admin->id }}</td>
                                        <td>{{ $admin->username }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->role }}</td>
                                        <td class="text-right">
                                            <button onclick="deleteAdmin({{ $admin->id }})" type="button"
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
        <script>
            function deleteAdmin(id) {
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
                    let url = "{{ route('deleteAdmin', -1) }}";
                    $.ajax({
                        type: "GET",
                        url: url.replace('-1', id),
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                Swal.fire(
                                    'Silindi!',
                                    'Admin basariyla silindi.',
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

        // function editUser(json) {

        //     //get element by id edit_id
        //     let id = document.getElementById('edit_id');
        //     //set value
        //     id.value = json.id;
          
        //     //get element by id edit_username
        //     let user= document.getElementById('edit_username');
        //     //set value
        //     user.value = json.username;

        //     let email = document.getElementById('edit_email');
        //     email.value = json.email;

        //     let wallet_id = document.getElementById('edit_wallet_id');
        //     wallet_id.value = json.wallet_id;

        //     console.log(json);
        //     let char_number = document.getElementById('edit_char_number');
        //     char_number.value = json.char_number;
          
        // }

        // function ConfirmEdit() {
        //     //swal ile silme onayi
        //     swal.fire({
        //         title: 'Emin misiniz?',
        //         text: "Bu islem geri alinamaz!",
        //         icon: 'warning',
        //         //change content color
        //         customClass: {
        //             content: 'text-dark'
        //         },
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Evet, degistir!',
        //         cancelButtonText: 'Hayir, iptal et!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             //ajax ile silme islemi
        //             let data = {
        //                 _token: "{{ csrf_token() }}",
        //                 id: $('#edit_id').val(),
        //                 username: $('#edit_username').val(),
        //                 wallet_id: $('#edit_wallet_id').val(),
        //                 email: $('#edit_email').val(),
        //                 char_number: $('#edit_char_number').val(),
        //             }
        //             console.log(data);
        //             $.ajax({
        //                 type: "POST",
        //                 data: data,
        //                 url: "{{ route('editUser') }}",
        //                 success: function(response) {
        //                     console.log(response);
        //                     if (response.status) {
        //                         Swal.fire(
        //                             'Degistirildi!',
        //                             'Kullanici basariyla degistirildi.',
        //                             'success'
        //                         ).then((result) => {
        //                             if (result.isConfirmed) {
        //                                 location.reload();
        //                             }
        //                         })
        //                     } else {
        //                         Swal.fire(
        //                             'Hata!',
        //                             'Kullanici degistirilemedi.',
        //                             'error'
        //                         )
        //                     }
        //                 }
        //             });
        //         }
        //     })

        // }
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
