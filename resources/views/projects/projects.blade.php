<!doctype html>

<html>

@include('layouts.header')
<link href="{{ asset('css/projects.css') }}" rel="stylesheet" type="text/css">

<title>Projects by Clay</title>
</head>
<body>

@include('layouts.topstuff')

<h1>Back-end</h1>
<ul>
    <li><a href="/projects/movies">Favorite Movies List (CRUD demo)</a></li>
</ul>
<h1>Javascript</h1>
<ul>
    <li><a href="/projects/snake">Snake Game (Phaser.js)</a></li>
    <li><a href="/projects/simon">Simon</a></li>
    <li><a href="/projects/heatmap">Campus Building Populations Heat Map</a></li>
</ul>
<h1>Android/Java</h1>
<ul>
    <li><a href="https://play.google.com/store/apps/details?id=com.clay.meditation">Meditation Timer</a></li>

</ul>
<h1>Node.js</h1>
<ul>
    <li><a href="https://github.com/tarwater/node-game-bot">IRC Game Bot</a></li>
</ul>

</body>
</html>
