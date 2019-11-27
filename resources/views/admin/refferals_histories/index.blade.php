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

                @isset($refferals)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Кто поделился</th>
                            <th scope="col">Кто пришел</th>
                            <th scope="col">Подтверждение</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($refferals as $key => $refferal)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('users.show',$refferal->sender()->id) }}">
                                        {{$refferal->sender()->phone}}</a>
                                </td>
                                <td><a href="{{ route('users.show',$refferal->recipient()->id) }}">
                                        {{$refferal->recipient()->phone}}</a>
                                </td>
                                <td>
                                    {{$refferal->activated?"Активирован":"Не активирован"}}
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $refferals->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection