@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Панель управления</div>

                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div><br/>
                        @endif

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                {{ $message }}
                            </div>
                        @endif

                        <div class="nav-tabs-boxed">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#home"
                                                        role="tab" aria-controls="home">Меню</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cashback" role="tab"
                                                        aria-controls="cashback">Начисления CashBack</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#announce" role="tab"
                                                        aria-controls="announce">Рассылки</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#search" role="tab"
                                                        aria-controls="search">Поиск</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#generate" role="tab"
                                                        aria-controls="generate">Генерация QR</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="home" role="tabpanel">1. Lorem ipsum dolor sit amet,
                                    consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore
                                    magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
                                    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in
                                    voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat
                                    cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est
                                    laborum.
                                </div>
                                <div class="tab-pane" id="cashback" role="tabpanel"> @include("partials.cashback")</div>
                                <div class="tab-pane" id="announce" role="tabpanel"> @include("partials.announce")</div>
                                <div class="tab-pane" id="search" role="tabpanel"> @include("partials.search")</div>
                                <div class="tab-pane" id="generate" role="tabpanel"> @include("partials.generate")</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
