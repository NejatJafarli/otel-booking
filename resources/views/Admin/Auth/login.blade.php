<!DOCTYPE html>
<html lang="tr">

<head>
    <title>Yönetici Girişi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="/assets/admin/images/icons/favicon.ico" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/util.css">
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/main.css">
    <!--===============================================================================================-->
</head>

<body>

    <div class="limiter">
        <div class="container-login100" style="background-image: url('/assets/admin/images/bg-01.jpg');">
            <div class="wrap-login100 p-l-110 p-r-110 p-t-62 p-b-33">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger solid alert-dismissible fade show">
                        <h5>Hata</h5>
                        <p class="mb-0">{{ session('error') }}</p>
                    </div>
                @endif

                {{-- redirect()->back()->with('error', '3D Model dosyası glb formatında olmalıdır!');  catch this erron show --}}


                {{-- //get success message
                @if (session('success'))
                    <div class="alert alert-success solid alert-dismissible fade show">
                        <h5>Başarılı</h5>
                        <p class="mb-0">{{ session('success') }}</p>
                    </div>
                @endif --}}
                <form class="login100-form validate-form flex-sb flex-w" method="post"
                    action="{{ route('adminLoginPost') }}">
                    @csrf
                    <div class="p-t-10 p-b-9">
                        <span class="txt1">
                            E-Posta
                        </span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Mail Adresi">
                        <input value="{{ old('email') }}" class="input100" type="email" name="email">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="p-t-13 p-b-9">
                        <span class="txt1">
                            Şifre
                        </span>

                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Şifre Gerekli">
                        <input class="input100" type="password" name="password">
                        <span class="focus-input100"></span>
                    </div>

                    <div class="container-login100-form-btn m-t-17 p-b-19">
                        <button type="submit" class="login100-form-btn">
                            Giriş Yap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="dropDownSelect1"></div>

    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/bootstrap/js/popper.js"></script>
    <script src="/assets/admin/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/daterangepicker/moment.min.js"></script>
    <script src="/assets/admin/vendor/daterangepicker/daterangepicker.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/vendor/countdowntime/countdowntime.js"></script>
    <!--===============================================================================================-->
    <script src="/assets/admin/js/main.js"></script>

</body>

</html>
