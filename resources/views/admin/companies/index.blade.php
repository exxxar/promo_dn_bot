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
                        <a class="btn btn-primary" href="{{route("companies.create")}}">Новая компания</a>
                    </div>
                </div>

                @isset($companies)
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Адрес</th>
                            <th scope="col">Телефон</th>
                            <th scope="col">Почта</th>
                            <th scope="col">Ответственное лицо</th>
                            <th scope="col">Логотип (ссылка)</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companies as $key => $company)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('companies.show',$company->id) }}">
                                        {{$company->title}}</a>

                                    <a class="btn btn-link" href="{{ route('companies.edit',$company->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                                <td>{{$company->description}}</td>
                                <td>{{$company->address}}</td>
                                <td>{{$company->phone}}</td>
                                <td>{{$company->email}}</td>
                                <td>{{$company->bailee}}</td>
                                <td><img class="img-thumbnail" style="width:150px;height:150px;"
                                         src="{{$company->logo_url}}" alt=""></td>

                                <td>
                                    <form action="{{ route('companies.destroy', $company->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $companies->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection