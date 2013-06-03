var timer = null;
var timer2 = null;
var delay = 3000;
var crt_text;
var draw_timer = null;


function drawStar(ctx,c)
{	
	var img = new Image();
	img.src = "img/star64.png";
	
	var img_red = new Image();
	img_red.src = "img/star64_1.png";
	var img_green = new Image();
	img_green.src = "img/star64_2.png";
	var img_blue = new Image();
	img_blue.src = "img/star64_3.png";
	
	
	
	min = window.innerHeight / 80;
	max = window.innerHeight / 25;
	h = Math.floor((Math.random()*(max-min))+min);
	w = h; //  Math.floor(h * c.width / c.height);
	
	
	
	x = Math.floor((Math.random()*c.width));
	y = Math.floor((Math.random()*c.height));
	
	//ctx.globalAlpha = Math.random() * 0.5 + 0.5;
	//ctx.drawImage(img,x,y,w,h);
	
	ctx.globalAlpha = 0.2;
	var o = 1;
	var opacity_timer = setInterval(function() { 
		if(Math.random()>0.98) { ctx.drawImage(img_red,x,y,w,h);  }
		else if(Math.random()>0.96) { ctx.drawImage(img_blue,x,y,w,h);  }
		//else if(Math.random()>0.95) { ctx.drawImage(img_green,x,y,w,h);  }
		else ctx.drawImage(img,x,y,w,h); 
		o++; 
		if(o>3) { clearInterval(opacity_timer); } 
		}, 100);

	
}

function drawStarMeta(ctx,c)
{
	t = Math.floor(Math.random()*delay);
	
	setTimeout(function() { drawStar(ctx,c); }, t);
}

function changetext()
{
	crt_text++; 
	if(crt_text > 7) crt_text = 1;
	
	$("#1").fadeOut(3000);
	$("#2").fadeOut(3000);
	$("#3").fadeOut(3000);
	$("#4").fadeOut(3000);
	$("#5").fadeOut(3000);
	$("#6").fadeOut(3000);
	$("#7").fadeOut(3000);
	
	switch(crt_text)
	{
		case 1:
			$("#1").fadeIn(6000);
			break;
		case 2:
			$("#2").fadeIn(6000);
			break;
		case 3:
			$("#3").fadeIn(6000);
			break;
		case 4:
			$("#4").fadeIn(6000);
			break;
		case 5:
			$("#5").fadeIn(6000);
			break;
		case 6:
			$("#6").fadeIn(6000);
			break;
		case 7:
			$("#7").fadeIn(6000);
			break;
	}
}

function start()
{
	// you can't see
	setTimeout( function() { $("#eyes_div").fadeIn(6000); }, 1000);
	setTimeout( function() { $("#eyes_div").fadeOut(6000); }, 6000);
	
	// main title
	setTimeout( function() { $("#main_title").fadeIn(3000); }, 14000);
	setTimeout( function() { $("#main_title").fadeOut(3000); }, 19000);
	
	// fade in canvas and let the night begin
	clearTimeout(draw_timer); 
	draw_timer = setTimeout( function() { draw(); }, 23000);
	
	// text rotation
	crt_text = 0;
	setTimeout(function () 
	{
		setInterval(function() { changetext(); },23750);
	},24000);
	
	// cursor
	setTimeout(function()
	{
		document.getElementById("main_canvas").style.cursor = "url(img/star64_yellow.cur), pointer";
	}, 200000);

	// credits
	setTimeout(function()
	{
		$("#copyright").fadeIn(4000);
		$("#song").fadeIn(4000);
	}, 195000);
}

function draw()	//hopefully no one resizes the window in the first few seconds during the intro...because I'm too lazy now to deal with that... oh boy, I could have dealt with it in the time it took to write this sentence
{
	

	clearInterval(timer);
	
	var c=document.getElementById("main_canvas");
    var ctx=c.getContext('2d');
    if(!ctx){return}
	
	wh = window.innerHeight;
	ww = window.innerWidth;
	
	c.height = 80/100 * wh;
	c.width = 98/100 * ww;
	
	ctx.fillStyle="#070411";
	ctx.fillRect(0,0,c.width,c.height);
	
	$("#main_canvas").fadeIn(3000);
	
	timer = setInterval(function() { drawStarMeta(ctx,c); },3000);
}