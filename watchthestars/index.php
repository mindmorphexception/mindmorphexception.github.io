<!DOCTYPE html>

<?php
include("../header.php");
mylog("Iulia watching the stars");
?>

<html prefix="og: http://ogp.me/ns#">
<head>
	<title> Iulia watching the stars </title>
	<meta name="author" content="Iulia M. Comsa" />
	<meta name="description" content="A starry introspection" />
	<meta name="keywords" content="html5 canvas sky stars starry introspection iulia comsa" />	
	<meta property="og:title" content="A starry introspection" />
	<meta property="og:description" content="Iulia watching the stars" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="http://iulia.hostz.ro" />
	<meta property="og:image" content="http://iulia.hostz.ro/img/preview4.png" />
	
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href='http://fonts.googleapis.com/css?family=Tangerine:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Dancing+Script' rel='stylesheet' type='text/css'>
	<script src="script.js"></script>
	<script src="http://iulia.hostz.ro/jquery/jquery.js"></script>
</head>

<body onload="start()" onresize="draw()">

	<div class="intro" id="main_title">Iulia's starry introspection</div>
		
	<audio autoplay="autoplay" controls="controls" id="audio_elem" loop="loop" preload="auto">
		<source src="http://iulia.hostz.ro/snd/Windermere - Watch the Stars.mp3" type="audio/mpeg">
		<source src="http://iulia.hostz.ro/snd/Windermere - Watch the Stars.ogg" type="audio/ogg">
		For an optimal catharsis effect on your psyche infested with daily worries, a song should be playing right now. Unfortunately, your browser does not support the HTML5 audio tag. Get a browser that does! Try Chrome or Firefox.
	</audio>
	
	<!--iframe id="audio_elem" height="60" src="http://www.youtube.com/embed/P7Yf-r3nFHo" frameborder="0"></iframe-->
	
	<div id="iulia_div" class="info">I'm Iulia and I like to watch the stars.</div>
	<div id="why_div" class="info">So I made a starry window in HTML5.</div>
	<div id="eyes_div" class="info">You can't see the stars until your eyes get used to the dark.</div>
	<div id="3" class="info">How long will it take to see something beautiful?</div>
	<div id="4" class="info">Do the stars ever gaze back?</div>
	<div id="1" class="info">They say that if you gaze long enough at the stars, you become one of them...</div>
	<div id="2" class="info">What do you see?</div>
	<div id="5" class="info">How many ways are there to join the dots into constellations?</div>
	<div id="6" class="info">People can be miles apart, yet they all see the same sky every night.</div>
	<div id="7" class="info">Will you watch the stars tonight?</div>
	
	<div id="copyright" class="credits">A starry introspection by <a href="../old/" target="_blank">Iulia M. Comsa</a></div>
	<div id="song" class="credits">Song: Watch the Stars by <a href="http://www.myspace.com/windermere" target="_blank">Windermere</a></div>
		
	<canvas id="main_canvas"> </canvas>

</body>

</html> 