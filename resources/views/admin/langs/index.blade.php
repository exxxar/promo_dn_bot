@extends('layouts.app')

@section("content")

    <div class="container">
        <div class="row justify-content-center">
            <iframe class="col-md-12" src="{{url('admin/translations')}}" style="width: 100%;height: 100vh;border: none"></iframe>
        </div>
    </div>
@endsection
