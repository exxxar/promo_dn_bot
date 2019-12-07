@extends('layouts.main')

@section('content')
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

                <h1>Достижения</h1>

                    <div class="row">
                        <div class="col">
                            <a id="share" href="http://www.facebook.com/sharer.php?u={{$url}}">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Facebook_logo_%28square%29.png/480px-Facebook_logo_%28square%29.png" />
                            </a>

                            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
                            <script src="http://swip.codylindley.com/jquery.popupWindow.js"></script>

                            <script type="text/javascript">
                                $('#share').popupWindow({
                                    width:550,
                                    height:400,
                                    centerBrowser:1
                                });
                            </script>
                        </div>
                        <div class="col">
                            <script type="text/javascript" src="https://vk.com/js/api/share.js?95" charset="windows-1251"></script>

                            <!-- Put this script tag to the place, where the Share button will be -->
                            <script type="text/javascript"><!--
                                document.write(VK.Share.button({url: "{{$url}}"}, {
                                    type: "custom",
                                    text: '<img src="https://cdn4.iconfinder.com/data/icons/social-media-flat-7/64/Social-media_VK-512.png" width="128" height="128" />'
                                }));
                                --></script>
                        </div>
                        <div class="col">

                        </div>
                    </div>


                <!-- Put this script tag to the <head> of your page -->

            </div>
        </div>
    </div>
@endsection
