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

                <h1>Компании</h1>
                @isset($companies)

                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название</th>
                            <th scope="col">Описание</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companies as $key => $company)

                            <div class="card card-accent-success" style="width: 300px">
                                <div class="card-header ">
                                    <a class="btn btn-link" href="{{ route('companies.edit',$company->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-link" href="{{ route('companies.channel',$company->id) }}"
                                       title="Отправить в канал">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                    <a class="btn btn-link" href="{{ route('companies.hide',$company->id) }}"
                                       title="Скрыть компанию, акции и мероприятия!">
                                        @if($company->is_active)
                                            <i class="fas fa-eye"></i>
                                        @else
                                            <i class="fas fa-eye-slash"></i>
                                        @endif
                                    </a>
                                    <form action="{{ route('companies.destroy', $company->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                                <img class="card-img-top" src="{{$company->logo_url}}" >
                                <div class="card-body">
                                    <h5 class="card-title">{{$company->title}}</h5>
                                    <p class="card-text">{{$company->description}}</p>
                                    <a class="btn btn-primary"  href="{{ route('companies.show',$company->id) }}">
                                        Подробнее</a>

                                </div>
                            </div>


                        @endforeach

                    {{ $companies->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
