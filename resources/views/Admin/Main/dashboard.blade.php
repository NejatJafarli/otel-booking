@extends('layout/master')

@section('header')
    <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"
    integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous">
    </script>
@endsection
@section("content")

<div class="row flex-grow-1">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0">Aktif Oyuncu Sayisi</h6>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-12 col-xl-5">
                            <h3 class="mb-2"></h3>
                            <h3 id="onlineUsers" class="mb-2"></h3>
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
    </div>
</div
@endsection

    @section('js')
     <script>
  var socket = io.connect('https://cyprusvarosha.com');
  
   window.addEventListener('load', function () {
   socket.on("RECEIVE_USER_COUNT",(count)=>{
     let message = document.getElementById('onlineUsers');
    message.innerHTML = count;  
   
   })
    socket.emit('GET_USER_COUNT');
  });
  </script>

    @endsection
