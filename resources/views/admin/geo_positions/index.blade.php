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
                        <a class="btn btn-primary" href="{{route("geo_positions.create")}}">Добавить новые точки гео-заданий</a>
                    </div>
                </div>

                    <h1>Гео-точки</h1>
                @isset($geo_positions)
                    <h5>Глобальный радиус в системе = {{env('GEO_QUEST_GLOBAL_DISTANCE')}}км.</h5>
                    <table class="table mt-2">

                        <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название точки</th>
                            <th scope="col">latitude & longitude</th>
                            <th scope="col">Радиус активации</th>
                            <th scope="col">Связано с квестом</th>
                            <th scope="col">Действие</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($geo_positions as $key => $position)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td><a href="{{ route('geo_positions.show',$position->id) }}">
                                        {{$position->title}}</a>
                                    <a class="btn btn-link" href="{{ route('geo_positions.edit',$position->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                </td>
                                <td>{{$position->latitude}} & {{$position->longitude}}</td>
                                <td>

                                    {{$position->radius}}
                                </td>
                                <td>
                                    @if ($position->is_assigned==false)
                                        <i class="fas fa-unlink"></i>
                                    @else
                                        <i class="fas fa-link"></i>
                                    @endif

                                </td>

                                <td>
                                    <form action="{{ route('geo_positions.destroy', $position->id)}}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-link" type="submit"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $geo_positions->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
