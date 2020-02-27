@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                        <div class="card">
                            <div class="card-header">Рассылки в боте</div>
                            <div class="card-body">
                                <div class="row bd-example2">
                                    <div class="col-4">
                                        <div class="list-group" id="list-example">
                                            <a class="list-group-item list-group-item-action active"
                                               href="#list-item-1">Начисления CashBack</a>
                                            <a class="list-group-item list-group-item-action" href="#list-item-2">Оповещения</a>
                                            <a class="list-group-item list-group-item-action" href="#list-item-3">Поиск
                                                пользователя</a>
                                            <a class="list-group-item list-group-item-action" href="#list-item-4">Генерация
                                                QR-кода</a></div>
                                    </div>
                                    <div class="col-8">
                                        <div id="spy-example2" data-spy="scroll" data-target="#list-example"
                                             data-offset="0" style="height: 200px; overflow: auto">
                                            <h4 id="list-item-1">Начисления CashBack</h4>
                                            @include("partials.cashback")
                                            <h4 id="list-item-2">Оповещения</h4>
                                            @include("partials.announce")
                                            <h4 id="list-item-3">Поиск пользователя</h4>
                                            @include("partials.search")
                                            <h4 id="list-item-4">Генерация QR-кода</h4>
                                            @include("partials.generate")
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
