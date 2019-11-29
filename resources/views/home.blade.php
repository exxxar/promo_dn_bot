@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Панель управления</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                      {{--  @include("partials.announce")
                        <hr>
                        @include("partials.cashback")
                        <hr>
                        @include("partials.search")--}}
                        <hr>
                        @include("partials.generate")
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
