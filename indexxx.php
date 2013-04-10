<!DOCTYPE html>

<?php
include("header.php");
mylog("frontpage");
?>

<html>
	<head>
		<title>Iulia</title>
		<style type="text/css">
			html, body, #container { height: 100%; margin: 0; padding: 0; }
			body > #container { height: auto; min-height: 100%; }
			body 
			{
				font-family: "Bookman Old Style", Verdana, Geneva, sans-serif;
				font-size: 12px;
			}
			#footer 
			{
				 clear: both;
				 position: relative;
				 height: 16em;
				 margin-top: -18em;
				 
			}
			#aboutme
			{
				margin: 0px auto;
				text-align:center; 
				padding: 50px;
				
			}
			#pdfiframe
			{
				margin: 0px auto;
				border: 0;
			}
			#pdfdiv
			{
				margin: 0px auto;
				width:600px;
				padding-bottom: 17em;
			}
			a:link {text-decoration:none; color:inherit; border-bottom:1px dotted;}
			a:visited {text-decoration:none; color:inherit;}
			a:hover {text-decoration:none;color:inherit; border-bottom:1px solid;}
			a:active {text-decoration:none;color:inherit;}
			
			
			#footerlist
			{
				list-style-type:none;
				border-top: 1px solid #9493b8;
				margin-top:20px;
			}
			#footerlist li
			{
				float:left;
				width:300px;
				text-align:center;
				margin-top:10px;
				color: #2e2d6c;
			}
			#footerlist li p
			{
				color:#9493b8;
				text-align: center;
				margin-top: 0px;
				padding: 0;
			}
			#footerlist li a
			{
				text-align:center;
				padding-top:10px;
				
			}
			
		</style>
		
		<script type="text/javascript" src="jquery/jquery.js"> </script>
		<script type="text/javascript">	
			
			function iframevent()
			{
				var pdfiframe = document.getElementById("pdfiframe");
				if(pdfiframe.contentDocument.location != "about:blank") expandIFrame();
			}
			function expandIFrame()
			{
				$('#pdfiframe').height(0);
				$('#pdfiframe').animate({ height: "780px"}, 1000);
			}
		</script>
		
		
	</head>

	<body>

		<div id="container">
			<div id="aboutme">
				<p> My name is Iulia. I like to play. This is my temporary website.<!--I like intellectual art, artistic science, Leicester and Dalia cheese, psychological video games, psychological sci-fi movies, the Scandinavian countries, indie rock music, playing the piano, snow, myself, my urology medic, watching the sky at night, taking over the world, solving puzzles, writing about what I like and several other things.--></p>
				<p> Want to see what awesome things I've been up to? Take a look at my 
				<a href="files/cv-iulia-comsa-research.pdf" target="pdfiframe" onclick="expandIFrame();">research cv</a> or 
				<a href="files/cv-iulia-comsa-professional.pdf" target="pdfiframe" onclick="expandIFrame();">professional cv</a>. </p>
				<p> Or <a href="/stars">watch the stars</a> with my little HTML5 experiment. </p>
			</div>
			
			<div id="pdfdiv">
				<iframe name="pdfiframe" id="pdfiframe" width="600" height="1"></iframe>
			</div>
		</div>
		
		<div id="footer">
			<ul id="footerlist">
				<li><i>Bits of Iulia <br/> scattered on the net:</i></li>
				<li><a href="http://www.facebook.com/iulia.comsa" target="_blank">Facebook</a>
				<p>The place where I post awesome stuff!</p></li>
				<li><a href="http://plus.google.com/u/0/106532679685962165170" target="_blank">Google+</a>
				<p>A nice place that I <i>almost</i> use.</p></li>
				<li><a href="http://mindmorphexception.deviantart.com" target="_blank">Deviantart</a>
				<p>Excusable photos. Featuring trees & kidney stones.</p></li>
				<li><a href="http://www.youtube.com/playlist?list=FLTSdb8LxEs0OgLJ8ryfZDJQ" target="_blank">YouTube</a>
				<p>My favourite videos from the web.</p></li>
				<li><a href="http://www.goodreads.com/mindmorphexception" target="_blank">Goodreads</a>
				<p>Books and floating words.</p></li>
				<li><a href="http://steamcommunity.com/id/astronomind/" target="_blank">Steam</a>
				<p>Play with me!</p></li>
				<li><a href="http://infoarena.ro/utilizator/astronothing" target="_blank">Infoarena</a>
				<p>Bits of code.</p></li>
				<li><a href="http://www.grooveshark.com/#!/astronomind" target="_blank">Grooveshark</a>
				<p>This actually tells what songs I'm <i>obsessed</i> with.</p></li>
				<li><a href="http://duolingo.com/#/iulia.comsa" target="_blank">Duolingo</a>
				<p>I need study buddies.</p></li>
				<li><a href="http://mindmorphexception.wordpress.com" target="_blank">Wordpress</a>
				<p>My deepest inquisitions... for the privileged.</p></li>
			</ul>
		</div>

	</body>

</html>