<!doctype html>
<html lang="en">
<head>
	<title>Text 3D (Three.js)</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
	<link rel=stylesheet href="css/base.css"/>
</head>
<body>

<script src="../libs/threejs/build/three.js"></script>
<script src="../libs/threejs/examples/js/Detector.js"></script>
<script src="../libs/threejs/examples/js/Stats.js"></script>
<script src="../libs/threejs/examples/js/controls/OrbitControls.js"></script>
<script src="../libs/threejs/src/extras/THREEx/THREEx.KeyboardState.js"></script>
<script src="../libs/threejs/src/extras/THREEx/THREEx.FullScreen.js"></script>
<script src="../libs/threejs/src/extras/THREEx/THREEx.WindowResize.js"></script>

<!-- load fonts -->
<script src="../libs/threejs/examples/fonts/gentilis_bold.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/gentilis_regular.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/optimer_bold.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/optimer_regular.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/helvetiker_bold.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/helvetiker_regular.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/droid/droid_sans_regular.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/droid/droid_sans_bold.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/droid/droid_serif_regular.typeface.js"></script>
<script src="../libs/threejs/examples/fonts/droid/droid_serif_bold.typeface.js"></script>

<!-- jQuery code to display an information button and box when clicked. -->
<script src="js/jquery-1.9.1.js"></script>
<script src="js/jquery-ui.js"></script>
<link rel=stylesheet href="css/jquery-ui.css" />
<link rel=stylesheet href="css/info.css"/>
<script src="js/info.js"></script>
<div id="infoButton"></div>
<div id="infoBox" title="Demo Information">
This three.js demo is part of a collection at
<a href="http://stemkoski.github.io/Three.js/">http://stemkoski.github.io/Three.js/</a>
</div>
<!-- ------------------------------------------------------------ -->

<div id="ThreeJS" style="position: absolute; left:0px; top:0px"></div>
<script>
/*
	Three.js "tutorials by example"
	Author: Lee Stemkoski
	Date: July 2013 (three.js v59dev)
*/

// MAIN

// standard global variables
var container, scene, camera, renderer, controls, stats;
var keyboard = new THREEx.KeyboardState();
var clock = new THREE.Clock();
// custom global variables
var cube;

init();
animate();

// FUNCTIONS 		
function init() 
{
	// SCENE
	scene = new THREE.Scene();
	// CAMERA
	var SCREEN_WIDTH = window.innerWidth, SCREEN_HEIGHT = window.innerHeight;
	var VIEW_ANGLE = 45, ASPECT = SCREEN_WIDTH / SCREEN_HEIGHT, NEAR = 0.1, FAR = 20000;
	camera = new THREE.PerspectiveCamera( VIEW_ANGLE, ASPECT, NEAR, FAR);
	scene.add(camera);
	camera.position.set(0,150,400);
	camera.lookAt(scene.position);	
	// RENDERER
	if ( Detector.webgl )
		renderer = new THREE.WebGLRenderer( {antialias:true} );
	else
		renderer = new THREE.CanvasRenderer(); 
	renderer.setSize(SCREEN_WIDTH, SCREEN_HEIGHT);
	container = document.getElementById( 'ThreeJS' );
	container.appendChild( renderer.domElement );
	// EVENTS
	THREEx.WindowResize(renderer, camera);
	THREEx.FullScreen.bindKey({ charCode : 'm'.charCodeAt(0) });
	// CONTROLS
	controls = new THREE.OrbitControls( camera, renderer.domElement );
	// STATS
	stats = new Stats();
	stats.domElement.style.position = 'absolute';
	stats.domElement.style.bottom = '0px';
	stats.domElement.style.zIndex = 100;
	container.appendChild( stats.domElement );
	// LIGHT
	var light = new THREE.PointLight(0xffffff);
	light.position.set(0,250,0);
	scene.add(light);
	// FLOOR
	var floorTexture = new THREE.ImageUtils.loadTexture( 'images/checkerboard.jpg' );
	floorTexture.wrapS = floorTexture.wrapT = THREE.RepeatWrapping; 
	floorTexture.repeat.set( 10, 10 );
	var floorMaterial = new THREE.MeshBasicMaterial( { map: floorTexture, side: THREE.DoubleSide } );
	var floorGeometry = new THREE.PlaneGeometry(1000, 1000, 10, 10);
	var floor = new THREE.Mesh(floorGeometry, floorMaterial);
	floor.position.y = -0.5;
	floor.rotation.x = Math.PI / 2;
	scene.add(floor);
	// SKYBOX/FOG
	var skyBoxGeometry = new THREE.CubeGeometry( 10000, 10000, 10000 );
	var skyBoxMaterial = new THREE.MeshBasicMaterial( { color: 0x9999ff, side: THREE.BackSide } );
	var skyBox = new THREE.Mesh( skyBoxGeometry, skyBoxMaterial );
	// scene.add(skyBox);
	scene.fog = new THREE.FogExp2( 0x9999ff, 0.00025 );
	
	////////////
	// CUSTOM //
	////////////
	
	// add 3D text
	var materialFront = new THREE.MeshBasicMaterial( { color: 0xff0000 } );
	var materialSide = new THREE.MeshBasicMaterial( { color: 0x000088 } );
	var materialArray = [ materialFront, materialSide ];
	var textGeom = new THREE.TextGeometry( "Hello, World!", 
	{
		size: 30, height: 4, curveSegments: 3,
		font: "helvetiker", weight: "bold", style: "normal",
		bevelThickness: 1, bevelSize: 2, bevelEnabled: true,
		material: 0, extrudeMaterial: 1
	});
	// font: helvetiker, gentilis, droid sans, droid serif, optimer
	// weight: normal, bold
	
	var textMaterial = new THREE.MeshFaceMaterial(materialArray);
	var textMesh = new THREE.Mesh(textGeom, textMaterial );
	
	textGeom.computeBoundingBox();
	var textWidth = textGeom.boundingBox.max.x - textGeom.boundingBox.min.x;
	
	textMesh.position.set( -0.5 * textWidth, 50, 100 );
	textMesh.rotation.x = -Math.PI / 4;
	scene.add(textMesh);
}

function animate() 
{
    requestAnimationFrame( animate );
	render();		
	update();
}

function update()
{
	if ( keyboard.pressed("z") ) 
	{ 
		// do something
	}
	
	controls.update();
	stats.update();
}

function render() 
{
	renderer.render( scene, camera );
}

</script>

</body>
</html>
