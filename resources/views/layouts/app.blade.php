<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/admin/') }}">
                {{ config('app.name', 'PromoDN') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            @auth
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->


                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('promotions.index') }}">Акции</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('events.index') }}">Мероприятия</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('companies.index') }}">Компании</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('categories.index') }}">Категории</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Пользователи</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashback.index') }}">История кэшбэка</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('refferals.index') }}">История рефералов</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('payments.index') }}">История оплаты баллами</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('achievements.index') }}">Достижения</a>
                        </li>

                    </ul>


                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('articles.index') }}">
                                            Статьи
                                        </a>

                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                                @endguest
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>


    <script src="{{asset('/js/app.js')}}"></script>

    <script>
        var botmanWidget = {
            title: 'SkidkiDN - веб-версия',
            introMessage: 'Спасибо что используете Веб-версию нашего бота. Полноценный набор функций доступен в боте telegram',
            mainColor: '#ff9800',
            chatServer: 'https://promodnbot.herokuapp.com/public/botman'
        };

    </script>
    <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>

    <script src="{{asset('/js/jquery.mask.min.js')}}"></script>
    <script src="{{asset('/js/bootstrap-typeahead.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.phone').mask('+38(000) 000-00-00');

            $('#user_phone, #phone,#user_phone_gen').typeahead({
                source: [
                ],
                displayField:'phone',
                items: 10,
                scrollBar: false,
                ajax: {
                    url: '{{route('users.ajax.search')}}',
                    timeout: 300,
                    method: 'get',
                    preDispatch: function (query) {
                        return {
                            search: query
                        }
                    },
                    preProcess: function (data) {
                        if (data.success === false) {
                            // Hide the list, there was some error
                            return false;
                        }

                        console.log(data);
                        // We good!
                        return data;
                    }
                }
            });



        });
    </script>
</div>
</body>
</html>
