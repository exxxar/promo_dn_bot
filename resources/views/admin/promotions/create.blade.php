@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left">
                            <h2>Добавление новой акции</h2>
                        </div>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="{{ route('companies.index') }}"> Назад</a>
                        </div>

                        @if (count($errors) > 0)
                            <div class="alert alert-danger mt-2">
                                <strong>Упс!</strong> Возникли ошибки при заполнении полей.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    </div>
                </div>


                <form method="post" action="{{ route('promotions.store') }}">
                    @csrf
                    <table class="table mt-2">
                        <thead class="thead-light ">
                        <th>Параметр</th>
                        <th>Значение</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Заголовок</td>
                            <td>
                                <input type="text" name="title" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <textarea name="description" class="form-control" required></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Ссылка на изображение к акции</td>
                            <td>
                                <input type="url" name="promo_image_url" placeholder="https://example.com"
                                       pattern="http://.*|https://.*" size="200" class="form-control" required>
                            </td>
                        </tr>


                        <tr>
                            <td>Дата и время начала акции</td>
                            <td>
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Дата и время окончания акции</td>
                            <td>
                                <input type="datetime-local" name="end_at" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Колличество активаций</td>
                            <td>
                                <input type="number" min="0" name="activation_count" class="form-control" required>
                            </td>
                        </tr>
                        <tr>
                            <td>Адрес расположения</td>
                            <td>
                                <input type="text" name="location_address" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Координаты расположения, пример: 61.766130, -6.822510</td>
                            <td>
                                <input type="text" name="location_coords" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Текст для пользователя после активации</td>
                            <td>
                                <textarea name="activation_text" class="form-control"></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td>Активировать сразу после выполнения условий</td>
                            <td>
                                <input type="checkbox" name="immediately_activate" class="form-control" >
                            </td>
                        </tr>

                        <tr>
                            <td>Реферальный бонус</td>
                            <td>
                                <input type="number" min="0" name="refferal_bonus" class="form-control" required>
                            </td>
                        </tr>

                        <tr>
                            <td>Выбрать компанию</td>
                            <td>
                                <select name="company_id" class="form-control" required>
                                    @foreach($companies as $compay)
                                        <option value="{{$compay->id}}">{{$compay->title}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Выбрать категорию</td>
                            <td>
                                <select name="category_id" class="form-control" required>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary">Добавить</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection