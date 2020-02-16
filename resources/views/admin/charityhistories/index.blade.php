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

                <h1>История донатов</h1>
                @isset($charityhistories)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Кто сделал пожертвование</th>
                            <th scope="col">Сумма пожертвования</th>
                            <th scope="col">Акция</th>
                            <th scope="col">Компания</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($charityhistories as $key => $ch)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>
                                    @isset($ch->sender)
                                        <a href="{{ route('users.show',$ch->user->id) }}">
                                            {{$ch->user->phone??$ch->user->name??$ch->user->telegram_chat_id}}</a>
                                    @endisset
                                </td>

                                <td>
                                    <p>{{$ch->donated_money}}</p>
                                </td>
                                <td>
                                    @isset($ch->charity)
                                        <a href="{{ route('charities.show',$ch->charity->id) }}">
                                            {{$ch->charity->title}}</a>
                                    @endisset
                                </td>
                                <td>
                                    <p>{{$ch->company->title}}</p>
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $charityhistories->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
