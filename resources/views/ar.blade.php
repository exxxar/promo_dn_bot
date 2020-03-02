<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <script src="https://aframe.io/releases/0.6.0/aframe.min.js"></script>
    <script src="https://jeromeetienne.github.io/AR.js/aframe/build/aframe-ar.js"></script>
</head>

<body style='margin : 0px; overflow: hidden;'>
<a-scene embedded arjs='sourceType: webcam;'>


    <a-marker preset="pattern" type="pattern" url="http://skidka-service.ru/1.patt">
        <a-box position='0 0.5 0' material='color: black;' soundhandler></a-box>
    </a-marker>
    <!-- add a simple camera -->
    <a-entity camera></a-entity>
</a-scene>
</body>
</html>
