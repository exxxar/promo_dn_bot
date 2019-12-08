@extends('layouts.main')

@section('content')

    <style>
        .cabinet {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cabinet h5,
        .cabinet h1 {
            text-align: center;
            width: 100%;
        }

        .socials {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
        }

        .social-item {
            width: 150px;
            height: 250px;
            padding: 10px;
            box-sizing: border-box;
            list-style: none;
        }

        .social-item .top {
            padding-bottom: 20px;
        }


        .social-item .bottom p {
            font-weight: 100;
            text-align: center;
            font-size: 14px;
        }

        .social-item .bottom h6 {
            font-weight: 800;
            text-align: center;
        }

        .social-item img {
            width: 100%;

        }

        h2 {
            width: 100%;
            text-align: center;
        }

        button,
        input {
            padding:5px;
            width:100%;
        }

        table {
            width: 500px;
        }

        table tr td p {
            padding:10px;
        }

        nav {
            width:100%;
            display:flex;
            justify-content: center;
        }

        nav ul {
            display: flex;
            justify-content: center;
        }

        nav ul li {
            padding:10px;
            list-style: none;
        }

        nav ul li a{
            text-transform: uppercase;

        }
    </style>
    <div class="cabinet">
        <h1>Кабинет промоутера</h1>
        <h5>Алексей Гукай (<a href="#">4145612345</a>)</h5>
        <div class="progress-wrapper">
            <div class="progress">
                <div class="line"></div>
                <div class="text"></div>
            </div>
        </div>

        <hr>
        <nav>
            <ul>
                <li>
                    <a href="#">Достижения</a>
                </li>
                <li>
                    <a href="#">Моя сеть</a>
                </li>
            </ul>
        </nav>
        <hr>
        <ul class="socials">
            <li class="social-item">
                <div class="top">
                    <a id="share" href="http://www.facebook.com/sharer.php?u={{$url}}">
                        <img src="https://i.pinimg.com/originals/58/f4/72/58f4723d8f23906bdcb058604075ad2a.png"/>
                    </a>
                </div>
                <div class="bottom">
                    <h6>Количество активаций из Facebook</h6>
                    <p>0</p>
                </div>
            </li>

            <li class="social-item">
                <div class="top" id="vkShare">

                </div>
                <div class="bottom">
                    <h6>Количество активаций из VK</h6>
                    <p>0</p>
                </div>
            </li>


            <li class="social-item">
                <div class="top">
                    <img src="https://www.freepnglogos.com/uploads/new-instagram-logo-vector-png-8.png" alt="">
                </div>
                <div class="bottom">
                    <h6>Количество активаций из VK</h6>
                    <p>0</p>
                </div>
            </li>

        </ul>


        <hr>
        <h2>Ваши ссылки для рекламы</h2>
        <table>
            <thead>
                <th>Название</th>
                <th>URL</th>
                <th>Действие</th>
            </thead>
            <tbody>
                <tr>
                    <td>ВКонтакте</td>
                    <td>
                        <input type="text" id="vk" class="linkToCopy" value="https://t.me/skidki_dn_bot?start=MDAxMDQ4NDY5ODcwMzAwMDAwMDAwMDA=">
                    </td>
                    <td>
                        <button class="copyLinkBtn" data-id="vk">Копировать</button>
                    </td>
                </tr>

                <tr>
                    <td>Facebook</td>
                    <td>
                        <input type="text" id="fb" class="linkToCopy" value="https://t.me/skidki_dn_bot?start=MDAxMDQ4NDY5ODcwMzAwMDAwMDAwMDA=">
                    </td>
                    <td>
                        <button class="copyLinkBtn" data-id="fb">Копировать</button>
                    </td>
                </tr>

                <tr>
                    <td>Instagram</td>
                    <td>
                        <input type="text" class="linkToCopy" id="insta" value="https://t.me/skidki_dn_bot?start=MDAxMDQ4NDY5ODcwMzAwMDAwMDAwMDA=">
                    </td>
                    <td>
                        <button class="copyLinkBtn" data-id="insta">Копировать</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Ваш QR-код для рекламы</h2>
        <hr>
        <table>
            <tbody>
            <tr>
                <td>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Qr-uk-m-wikipedia.svg/1200px-Qr-uk-m-wikipedia.svg.png" class="img-thumbnail" style="width:150px;height:150px;"
                         alt="">
                </td>
                <td>
                    <p>Размещайте данный QR-код на любой печатной продукции!</p>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" src="http://swip.codylindley.com/jquery.popupWindow.js"></script>
    <script type="text/javascript" src="https://vk.com/js/api/share.js?95" charset="windows-1251"></script>
    <script type="text/javascript">


        $(document).ready(function () {

            $(".copyLinkBtn").click(function () {
                var dataId = $(this).attr("data-id");
                $("#"+dataId).select();
                document.execCommand("copy");
            });

            $("#vkShare").html(VK.Share.button({url: "{{$url}}"}, {
                type: "custom",
                text: '<img src="https://cdn4.iconfinder.com/data/icons/social-media-flat-7/64/Social-media_VK-512.png"  />'
            }));

            $('#share').popupWindow({
                height:500,
                width:800,
                top:50,
                left:50
            });

        });
    </script>
@endsection
