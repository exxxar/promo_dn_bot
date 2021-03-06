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
                        <a class="btn btn-primary" href="{{route("geo_quests.create")}}">Новое Гео-задание</a>
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary" href="{{route("geo_positions.create")}}">Новая Гео-точка</a>
                    </div>
                </div>

                <h1>Список Гео-заданий</h1>
                @isset($geo_quests)
                    <div class="row">


                        @foreach($geo_quests as $key => $quest)
                            <div class="col">
                                <div class="wrapper" style="padding: 10px">
                                    <div class="card" style="width:350px;">
                                        <!-- Изображение -->
                                        <img class="card-img-top" src="{{$quest->image_url}}" style="width:100%;">
                                        <!-- Текстовый контент -->
                                        <div class="card-body">
                                            <h5>{{$quest->title}}</h5>
                                            <p><em>{{$quest->description}}</em></p>
                                            <p>Точек в цепочке {{count($quest->quest_points_list)}}</p>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item">Доступно с
                                                    <strong>{{$quest->start_at}}</strong>
                                                    до
                                                    <strong>{{$quest->end_at}}</strong>
                                                </li>
                                                <li class="list-group-item">Победный бонус {{$quest->reward_bonus}}
                                                    баллов
                                                </li>
                                                @if ($quest->promotion!=null)
                                                    <li class="list-group-item">
                                                        Бонус из акции:
                                                        <a class="btn btn-link"
                                                           href="{{route("promotions.show",$quest->promotion->id)}}">{{$quest->promotion->title}}</a>
                                                    </li>
                                                @endif

                                                @if ($quest->company!=null)
                                                    <li class="list-group-item">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <a class="btn btn-info"
                                                                   href="{{route("companies.show",$quest->company->id)}}">{{$quest->company->title}}</a>
                                                            </div>
                                                        </div>

                                                    </li>
                                                @endif
                                            </ul>

                                            <div class="row mt-2">
                                                <div class="col-sm-12">
                                                    <a href="{{ route('geo_quests.show',$quest->id) }}"
                                                       class="btn btn-primary"><i
                                                                class="fas fa-eye"></i></a>
                                                    <a href="{{ route('geo_quests.edit',$quest->id) }}"
                                                       class="btn btn-info"><i
                                                                class="fas fa-edit"></i></a>

                                                    <a class="btn btn-info"
                                                       href="{{ route('geo_quests.duplication',$quest->id) }}"
                                                       title="Дублировать">
                                                        <i class="far fa-copy"></i>
                                                    </a>

                                                    <a class="btn btn-info"
                                                       href="{{ route('geo_quests.channel',$quest->id) }}"
                                                       title="Отправить в канал">
                                                        <i class="fab fa-telegram"></i>
                                                    </a>

                                                    <a class="btn btn-info"
                                                       href="{{ route('geo_quests.points.append',$quest->id) }}"
                                                       title="Гео-точки">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </a>

                                                    <form class="btn btn-warning"
                                                          action="{{ route('geo_quests.destroy', $quest->id)}}"
                                                          method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-link" type="submit"><i
                                                                    class="fas fa-times"></i></button>
                                                    </form>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{ $geo_quests->links() }}
                @endisset
            </div>
        </div>
    </div>
@endsection
