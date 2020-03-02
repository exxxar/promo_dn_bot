<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Hello, world!</title>
    <!-- include three.js library -->
    <script src='https://stemkoski.github.io/AR-Examples/js/three.js'></script>
    <!-- include jsartookit -->
    <script src="https://stemkoski.github.io/AR-Examples/jsartoolkit5/artoolkit.min.js"></script>
    <script src="https://stemkoski.github.io/AR-Examples/jsartoolkit5/artoolkit.api.js"></script>
    <!-- include threex.artoolkit -->
    <script src="https://stemkoski.github.io/AR-Examples/threex/threex-artoolkitsource.js"></script>
    <script src="https://stemkoski.github.io/AR-Examples/threex/threex-artoolkitcontext.js"></script>
    <script src="https://stemkoski.github.io/AR-Examples/threex/threex-arbasecontrols.js"></script>
    <script src="https://stemkoski.github.io/AR-Examples/threex/threex-armarkercontrols.js"></script>
</head>

<body style='margin : 0px; overflow: hidden; font-family: Monospace;'>

<!-- 
  Example created by Lee Stemkoski: https://github.com/stemkoski
  Based on the AR.js library and examples created by Jerome Etienne: https://github.com/jeromeetienne/AR.js/
-->

<script>

    var scene, camera, renderer, clock, deltaTime, totalTime;

    var arToolkitSource, arToolkitContext;

    var markerRoot1;

    var mesh1;

    initialize();
    animate();

    function initialize()
    {
        scene = new THREE.Scene();

        let ambientLight = new THREE.AmbientLight( 0xcccccc, 0.5 );
        scene.add( ambientLight );

        camera = new THREE.Camera();
        scene.add(camera);

        renderer = new THREE.WebGLRenderer({
            antialias : true,
            alpha: true
        });
        renderer.setClearColor(new THREE.Color('lightgrey'), 0)
        renderer.setSize( 640, 480 );
        renderer.domElement.style.position = 'absolute'
        renderer.domElement.style.top = '0px'
        renderer.domElement.style.left = '0px'
        document.body.appendChild( renderer.domElement );

        clock = new THREE.Clock();
        deltaTime = 0;
        totalTime = 0;

        ////////////////////////////////////////////////////////////
        // setup arToolkitSource
        ////////////////////////////////////////////////////////////

        arToolkitSource = new THREEx.ArToolkitSource({
            sourceType : 'webcam',
        });

        function onResize()
        {
            arToolkitSource.onResize()
            arToolkitSource.copySizeTo(renderer.domElement)
            if ( arToolkitContext.arController !== null )
            {
                arToolkitSource.copySizeTo(arToolkitContext.arController.canvas)
            }
        }

        arToolkitSource.init(function onReady(){
            onResize()
        });

        // handle resize event
        window.addEventListener('resize', function(){
            onResize()
        });

        ////////////////////////////////////////////////////////////
        // setup arToolkitContext
        ////////////////////////////////////////////////////////////

        // create atToolkitContext
        arToolkitContext = new THREEx.ArToolkitContext({
            cameraParametersUrl: 'https://stemkoski.github.io/AR-Examples/data/camera_para.dat',
            detectionMode: 'mono'
        });

        // copy projection matrix to camera when initialization complete
        arToolkitContext.init( function onCompleted(){
            camera.projectionMatrix.copy( arToolkitContext.getProjectionMatrix() );
        });

        ////////////////////////////////////////////////////////////
        // setup markerRoots
        ////////////////////////////////////////////////////////////

        // build markerControls
        markerRoot1 = new THREE.Group();
        scene.add(markerRoot1);
        let markerControls1 = new THREEx.ArMarkerControls(arToolkitContext, markerRoot1, {
            type: 'pattern', patternUrl: "https://skidka-service.ru/1.patt",
        })

        let geometry1 = new THREE.PlaneBufferGeometry(1,1, 4,4);
        let loader = new THREE.TextureLoader();
        let texture = loader.load( 'https://skidka-service.ru/bot.jpg', render );
        let material1 = new THREE.MeshBasicMaterial( { map: texture } );

        mesh1 = new THREE.Mesh( geometry1, material1 );
        mesh1.rotation.x = -Math.PI/2;

        markerRoot1.add( mesh1 );
    }


    function update()
    {
        // update artoolkit on every frame
        if ( arToolkitSource.ready !== false )
            arToolkitContext.update( arToolkitSource.domElement );
    }


    function render()
    {
        renderer.render( scene, camera );
    }


    function animate()
    {
        requestAnimationFrame(animate);
        deltaTime = clock.getDelta();
        totalTime += deltaTime;
        update();
        render();
    }

</script>

</body>
</html>
