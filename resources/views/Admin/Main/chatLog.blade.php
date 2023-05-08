@extends('layout/master')
@section('header')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"
        integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous">
    </script>
    <style>
        .chat {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: black;
            border-radius: 10px;
            max-width: 1200px;
            height: 600px;
            overflow-y: scroll;
        }

        .message {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
        }
    </style>
@endsection
@section('content')
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
                            <h6 class="card-title mb-0">Chat Log</h6>
                        </div>
                    </div>
                    {{-- <div class="table-responsive"> --}}
                    <div class="chat">
                        {{-- //foreach content --}}
                        @foreach ($content as $chat)
                            <div class="message">
                                {{ $chat }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- </div> --}}
            </div>
        </div>
    </div>
    </div>
@endsection
@section('js')
    <script>
        var socket = io.connect('https://cyprusvarosha.com');

        window.addEventListener('load', function() {
            socket.on("UPDATE_MESSAGE",(id,message,name)=>{

                console.log(message);
                let chat = document.querySelector('.chat');
                let newMessage = document.createElement('div');
                newMessage.classList.add('message');
                //current date 
                let date = new Date();
                let day = date.getDate();
                let month = date.getMonth() + 1;
                let year = date.getFullYear();
                let hours = date.getHours();
                let minute = date.getMinutes();

                if (day < 10) day = '0' + day;
                if (month < 10) month = '0' + month;
                if (hours < 10) hours = '0' + hour;
                if (minute < 10) minute = '0' + minute;
				let msg = "[" + day + "-" + month + "-" + date.getFullYear() + " " +hours + ":" + minute + "] "+name+" : "+ message + "\n";

                //write plain text not inner html
                newMessage.innerText = msg;
                chat.appendChild(newMessage);
                chat.scrollTop = chat.scrollHeight;
            })
        });
    </script>
@endsection
