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

                @isset($photos)

                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">Имя</th>
                            <th scope="col">Телефон</th>
                            <th scope="col">Root</th>
                            <th scope="col">Текущий\Максимум</th>
                            <th scope="col">Потрачено</th>
                            <th scope="col">Администратор</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($photos as $key => $photo)
                            <tr>
                                <td>{{$photo->user->id}}</td>
                                <td><a href="{{ route('users.show',$photo->user->id) }}">
                                        {{$photo->user->fio_from_telegram??$photo->user->email}}</a>
                                    <a class="btn btn-link" href="{{ route('users.edit',$photo->user->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>{{$photo->user->phone}}</td>
                                <td>
                                    @isset($photo->user->parent)
                                        <a href="{{ route('users.show',$photo->user->parent->id) }}">
                                            {{$photo->user->parent->phone??$photo->user->parent->name??$photo->user->parent->telegram_chat_id}}</a>
                                    @endisset


                                </td>
                                <td>
                                    {{$photo->user->cashback_bonus_count+$photo->user->referral_bonus_count??0}} \ {{$photo->user->summary}}
                                </td>
                                <td>
                                    {{$photo->user->spent}}
                                </td>
                                <td>
                                    {{$photo->user->is_admin?"Администратор":"Пользователь"}}
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $photos->links() }}
                @endisset

            </div>
        </div>
    </div>
@endsection
