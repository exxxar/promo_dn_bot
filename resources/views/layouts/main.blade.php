<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <meta property="og:url" content="https://vk.com/exxxar"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="Your Website Title"/>
    <meta property="og:description" content="Your description"/>
    <meta property="og:image" content="https://instaforum.ru/attachments/22-jpg.1774/"/>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div>

    <main class="py-4">
        @yield('content')
    </main>


    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

    <script src="{{asset('/js/jquery.mask.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            alert("Test");
            $('.phone').mask('+38(000) 000-00-00');

            $("#phone,#user_phone,#user_phone_gen").keyup(function () {
                console.log("Test");
                var phone = $(this).val();
                var target = $(this).attr("data-target");
                console.log("Test2");
                $.post('{{route('users.search')}}', {
                    phone: phone
                }).then(resp => {
                    $(target).html(resp.data.users);
                });
            });
        });
    </script>

</div>
</body>
</html>
