@extends('layouts.app')

@section("content")

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if ($errors->any())
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

                <div class="row">
                    <div class="col-sm-4">
                        <a class="btn btn-primary" href="{{route("bot_hubs.create")}}">Новый бот</a>
                    </div>
                </div>

                    <h1>Список ботов в системе</h1>
                @isset($bots)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Ссылка</th>
                            <th scope="col">Картинка</th>
                            <th scope="col">Деньги на счету</th>
                            <th scope="col">Деньг за день</th>
                            <th scope="col">Отображение</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bots as $key => $bot)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('bot_hubs.show',$bot->id) }}">
                                        {{$bot->bot_url}}</a>
                                    <a class="btn btn-link" href="{{ route('bot_hubs.edit',$bot->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>
                                    <img src="{{$bot->bot_pic}}" class="img-thumbnail" style="width: 200px;height: 200px; object-fit: cover;" alt="">
                                </td>
                                <td>
                                    {{$bot->money}}₽
                                </td>

                                <td>
                                    {{$bot->money_per_day}}₽\День
                                </td>

                                <td>
                                    @if ($bot->is_active==0)
                                        <i class="fas fa-eye-slash"></i>
                                    @else
                                        <i class="fas fa-eye"></i>
                                    @endif

                                </td>

                                <td>
                                    <form action="{{ route('bot_hubs.destroy', $bot->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $bots->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
