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

                <h1>История прохождения квестов</h1>
                @isset($geo_histories)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Квест</th>
                            <th scope="col">Позиция</th>
                            <th scope="col">Пользователь</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($geo_histories as $key => $history)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>
                                    @isset($history->quest)
                                        <a href="{{ route('geo_quests.show',$history->quest->id) }}">
                                            {{$history->quest->title}}</a>
                                    @endisset
                                </td>

                                <td>
                                    @isset($history->position)
                                        <a href="{{ route('get_positions.show',$history->position->id) }}">
                                            {{$history->position->title}}</a>
                                    @endisset
                                </td>
                                <td>
                                    @isset($history->user)
                                        <a href="{{ route('users.show',$history->user->id) }}">
                                            {{$history->user->name??$history->user->telegram_chat_id}}</a>
                                    @endisset
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $geo_histories->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
