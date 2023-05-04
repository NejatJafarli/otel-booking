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
                        <h6 class="card-title mb-0">Toplam Online Users Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 id="onlineUsers" class="mb-2"></h3>
                            <div class="d-flex align-items-baseline">
                            </div>
                        </div>
                        <div class="col-6 col-md-12 col-xl-7">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#exampleModal4">
                            Tum Kullanicilara Mesaj Gonder
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row flex-grow-1">
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
                                <h6 class="card-title mb-0">Online Users</h6>
                            </div>

                        </div>
                        <div class="table-responsive">
                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal"
                                data-bs-target="#exampleModal3">
                                Secilen Kullanicilara Mesaj Gonder
                            </button>
                            <table id="MyDataTable" class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="pt-0">Check</th>
                                        <th class="pt-0">#</th>
                                        <th class="pt-0">Kullanici Id</th>
                                        <th class="pt-0">Kullanici Adi</th>
                                        <th class="pt-0">Wallet Id </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mesaj Gonder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- action="{{ route('CreateRoom') }}" method="POST" --}}
                    <form>
                        <input type="hidden" id="edit_id">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                {{-- //text area message --}}
                                <label for="message-text" class="col-form-label">Mesaj:</label>
                                <textarea class="form-control" id="message-text"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button onclick="SendMessage()" type="button" class="btn btn-primary">Gonder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mesaj Gonder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- action="{{ route('CreateRoom') }}" method="POST" --}}
                    <form>
                        <input type="hidden" id="edit_id">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                {{-- //text area message --}}
                                <label for="message-text" class="col-form-label">Mesaj:</label>
                                <textarea class="form-control" id="message-text-two"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button onclick="sendMesageToSelectedsCheckboxes()" type="button"
                                class="btn btn-primary">Gonder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel4"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Mesaj Gonder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    {{-- action="{{ route('CreateRoom') }}" method="POST" --}}
                    <form>
                        <input type="hidden" id="edit_id">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                {{-- //text area message --}}
                                <label for="message-text" class="col-form-label">Mesaj:</label>
                                <textarea class="form-control" id="message-text-three"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button onclick="BroadCastMessage()" type="button" class="btn btn-primary">Gonder</button>
                        </div>
                    </form>
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
            var socket = io.connect('https://cyprusvarosha.com');

            let SelectedId = null;

            function SetId(id) {
                SelectedId = id;
            }

            function SendMessage() {
                let message = document.getElementById('message-text').value;
                let id = SelectedId;
                let data = {
                    message: message,
                    id: id
                }
                socket.emit('SEND_SPECIFIC_MESSAGE', data);
                $('#exampleModal2').modal('hide');
            }

            function BroadCastMessage() {
                let message = document.getElementById('message-text-three');
                if (message.value == '') {
                    alert('Lutfen mesaj giriniz');
                    return;
                }
                socket.emit('SEND_BROADCAST_MESSAGE', message.value);

                message.value = '';

                $('#exampleModal4').modal('hide');
            }

            function sendMesageToSelectedsCheckboxes() {

                //get input selecteds[]
                let selecteds = document.querySelectorAll('input[type="checkbox"]:checked');

                //if selecteds[] is empty
                if (selecteds.length == 0) {
                    alert('Lutfen en az bir kisi seciniz');
                    return;
                }

                //get attr data-id
                let ids = [];
                let message = document.getElementById('message-text-two').value;

                if (message == '') {
                    alert('Lutfen mesaj giriniz');
                    return;
                }
                selecteds.forEach(element => {
                    socket.emit('SEND_SPECIFIC_MESSAGE', {
                        message: message,
                        id: element.getAttribute('data-id')
                    });
                });

                //clear selecteds

                selecteds.forEach(element => {
                    element.checked = false;
                });

                //ALERT
                message = document.getElementById('message-text-two');
                message.value = '';

                //close modal
                $('#exampleModal3').modal('hide');
            }

            $(function() {
                'use strict';

                $(function() {
                    socket.on("RECEIVE_USER_COUNT", (count) => {
                        console.log(count);
                        let message = document.getElementById('onlineUsers');
                        message.innerHTML = count;
                    })
                    socket.emit('GET_USER_COUNT');
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

                    socket.on("RECEIVE_ALL_USERS", (data) => {
                        let count = $('#MyDataTable').DataTable().rows().count();
                        data.forEach(element => {
                            let html = `<button
                            onclick="SetId(\'` + element.id + `\')"
                            type="button" class="btn btn-xs btn-primary btn-icon"
                            data-bs-toggle="modal" data-bs-target="#exampleModal2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                               class="feather feather-send link-icon"><line x1="22" y1="2"
                                x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>`;
                            $('#MyDataTable').DataTable().row.add([
                                //add checkbox
                                "<input type='checkbox' name='selecteds[]' data-id=\"" +
                                element.id + "\" value=''>",
                                ++count,
                                element.id,
                                element.name,
                                element.wallet_id,
                            ]).draw();
                        });
                    });

                    socket.emit("GET_ALL_USERS");

                    socket.on("NEW_USER_CONNECTED", data => {
                        console.log(data.name + " connected");
                        let count = $('#MyDataTable').DataTable().rows().count();
                        let html = `<button
                            onclick="SetId(\'` + data.id + `\')"
                            type="button" class="btn btn-xs btn-primary btn-icon"
                            data-bs-toggle="modal" data-bs-target="#exampleModal2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                               class="feather feather-send link-icon"><line x1="22" y1="2"
                                x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    </button>`;
                        $('#MyDataTable').DataTable().row.add([
                            "<input type='checkbox' name='selecteds[]' value=''>",
                            ++count,
                            data.id,
                            data.name,
                            data.wallet_id,
                        ]).draw();
                    })
                    socket.on("NEW_USER_DISCONNECTED", id => {
                        //remove row from datatable 
                        let table = $('#MyDataTable').DataTable();
                        let row = table.rows().indexes().filter(function(value, index) {
                            return table.cell(value, 2).data() == id ? true : false;
                        });
                        let count = document.getElementById('onlineUsers');
                        count.innerHTML = parseInt(count.innerHTML) - 1;

                        table.row(row).remove().draw();
                    })

                    socket.on("UPDATE_USER", data => {
                        //remove row from datatable 
                        let id = data.id;
                        let name = data.name;
                        let wallet_id = data.wallet_id;
                        let table = $('#MyDataTable').DataTable();
                        let row = table.rows().indexes().filter(function(value, index) {
                            return table.cell(value, 2).data() == id ? true : false;
                        });
                        //update row
                        table.cell(row, 3).data(name).draw();
                        table.cell(row, 4).data(wallet_id).draw();
                    })

                })
            });
        </script>
    @endsection
