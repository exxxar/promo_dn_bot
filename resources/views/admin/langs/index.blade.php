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

                <h1>Параметры</h1>
                @isset($params)
                    {{dd($params)}}
                    <table class="table mt-2">
                        <thead class="thead-light">
                        <tr>

                            <th scope="col">#</th>
                            <th scope="col">Ключ</th>
                            <th scope="col">Значение</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($params as $key => $param)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>

                                </td>
                                <td>
                                    test
                                </td>

                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                @endisset
            </div>
        </div>
    </div>
@endsection
