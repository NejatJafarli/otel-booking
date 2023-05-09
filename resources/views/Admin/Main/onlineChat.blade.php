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

        .chat-sidebar {
            height: 100%;
            background-color: #172340;
        }

        .chat-user {
            cursor: pointer;
            margin-bottom: 10px;
            padding: 10px;
            background-color: black;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chat-user h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .chat-user p {
            font-size: 14px;
            margin: 0;
            color: #999;
        }

        .ChatActive .chat-sidebar {
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        .ChatActive .chat-user {
            background-color: #f2f2f2;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .ChatActive .chat-user h4 {
            color: #333;
        }

        .ChatActive .chat-user p {
            color: #666;
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
                        <div class="col-12">
                            <h6 class="card-title mb-0">Chat</h6>
                        </div>
                        {{-- <div class="table-responsive"> --}}
                        <div id="MainChat" class="chat col-9">
                            {{-- <div class="message">
                                        message
                                    </div> --}}
                        </div>
                        <div class="chat-sidebar col-3">
                            <ul id="chat-sidebar-ul">

                            </ul>
                        </div>


                        {{-- //text area and button --}}
                        <div class="col-10 pt-5">
                            <div class="row">
                                <div class="col-10">
                                    <input type="text" id="chat-message-main" class="form-control" placeholder="Message">
                                </div>
                                <div class="col-2">
                                    <button onclick="SendMessage()" class="btn btn-primary">Send</button>
                                </div>
                            </div>
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
        //get $users from php
        var users = @json($users);

        console.log(users);
        var socket = io.connect('https://cyprusvarosha.com');

        let chats = []
        let activeChat = null;
        //get chats

        socket.emit("JOIN_ADMIN");
        window.addEventListener('beforeunload', function(event) {
            event.preventDefault();
            event.returnValue = '';

            socket.emit("LEAVE_ADMIN")
        });
        socket.on("RECEIVE_ALL_CHATS", data => {
            console.log("RECEIVE_ALL_CHATS");
            console.log(data);
            //data array inside json id otherUserId OtherUserName
            // console.log(data);
            //get elemeny by id chat-sidebar-ul
            let chatSidebarUl = document.getElementById("chat-sidebar-ul");
            data.forEach(element => {
                //create li
                let li = document.createElement("li");
                //set li id to chat id
                li.id = element.id;
                if (!element.otherUserName) {
                    element.otherUserName = users.find(x => x.id == element.otherUserId).username
                }

                //inner html
                li.innerHTML = `<div class="chat-user" onclick="getChat(this,'${element.id}')">
                                        <h4>${element.otherUserName}</h4>
                                    </div>`;
                //append li to ul
                chatSidebarUl.appendChild(li);


                chats.push({
                    chatid: element.id,
                    otherUserId: element.otherUserId,
                    otherUserName: element.otherUserName,
                    messages: []
                })
            });


            //if in query string otherUserId
            let urlParams = new URLSearchParams(window.location.search);
            let otherUserId = urlParams.get('otherUserId');
            if (otherUserId) {
                //find chat with otherUserId
                let chat = chats.find(x => x.otherUserId == otherUserId);
                if (chat) {
                    //load chat
                    loadChat(chat);
                    //find this which one is onclick equal to chat id
                    let chatUser = document.querySelector(`[onclick="getChat(this,'${chat.chatid}')"]`);
                    //click the div
                    chatUser.click();


                    activeChat = chat;
                }

            }
        })


        socket.on("CHAT_CREATED", (chatid, messages) => {
            console.log(chatid, messages);
            // find chat with chatid
            let chat = chats.find(chat => chat.chatid == chatid);
            if (chat) {
                chat.messages = messages.map(x => x.message);
                //load chat
                loadChat(chat);
            } else {
                //reload chats
                if (activeChat) {
                    //set activechat.otherUserId to url

                    // redirect to url
                    let url = window.location.href;

                    window.open(url + "?otherUserId=" + activeChat.otherUserId, '_self');
                } else {
                    window.location.reload();
                }
            }

        })

        socket.on("CHAT_MESSAGE_RECEIVED", (id, message) => {
            console.log(id, message);
            //find chat with id
            let mainChat = document.getElementById("MainChat");

            if (activeChat.chatid == id) {
                //create message div
                let messageDiv = document.createElement("div");
                //add class message
                messageDiv.classList.add("message");
                //add inner html
                messageDiv.innerHTML = message;
                //append message div to main chat
                mainChat.appendChild(messageDiv);

                activeChat.messages.push(message);
            } else {

                //find
                let chat = chats.find(x => x.chatid == id);
                if (chat) {
                    let chatUser = document.querySelector(`[onclick="getChat(this,'${chat.chatid}')"]`);

                    //chahnge background color red
                    chatUser.style.backgroundColor = "red";
                }

            }

        });

        socket.emit("GET_ALL_CHATS", {
            id_one: "adminUser"
        });


        socket.on("USER_DISCONNECT_FROM_GAME", data => {
            console.log(data);
            let chatid = data.chatid;
            
            if (activeChat.chatid == chatid) {
                //create message div
                let mainChat = document.getElementById("MainChat");
                let messageDiv = document.createElement("div");
                //add class message
                messageDiv.classList.add("message");
                //add inner html
                messageDiv.innerHTML = "---User disconnected from game---";
                //append message div to main chat
                mainChat.appendChild(messageDiv);
            }
        });

        function SendMessage() {
            let message = document.getElementById('chat-message-main');

            //get textarea  value

            if (message.value == '') {
                alert('Lutfen mesaj giriniz');
                return;
            }
            let data = {
                message: "<color=yellow>Server:<color=white> " + message.value,
                chatid: activeChat.chatid
            }
            data = JSON.stringify(data);
            socket.emit('SEND_MESSAGE_TO_CHAT', data);
            message.value = '';

        }

        function loadChat(chat) {
            // MainChat get element by id
            let mainChat = document.getElementById("MainChat");
            //clear main chat
            mainChat.innerHTML = "";

            chat.messages.forEach(message => {
                //create message div
                let messageDiv = document.createElement("div");
                //add class message
                messageDiv.classList.add("message");
                //add inner html
                messageDiv.innerHTML = message;
                //append message div to main chat
                mainChat.appendChild(messageDiv);
            })
            activeChat = chat;
        }

        function getChat(element, chatid) {
            // console.log(chatid);
            console.log(element);
            //check active class
            if (document.querySelector(".ChatActive")) {
                //remove active class
                document.querySelector(".ChatActive").classList.remove("ChatActive");
            }

            //add active class to element
            // element.classList.add("ChatActive");
            //add ChatActive to this element parent
            element.parentElement.classList.add("ChatActive");

            socket.emit("GET_CHAT", {
                chat_id: chatid
            });

        }
    </script>
@endsection
