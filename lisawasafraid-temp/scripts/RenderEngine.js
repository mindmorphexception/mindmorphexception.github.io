RenderEngineClass = Class.extend(
{
	canvas: null,
	context: null,
	w: null,
	h: null, 
	
	bgr: null,
	objects: null,
	rooms: null,
	
	loadNeeded: 3,
	ready: false,
	loadCnt: 0,
	
	bgrsrc: "img/background.png",
	objsrc:"img/objects.png",
	roomssrc: "img/rooms.png",
	
	roomToDraw: null,
	textToDraw: null,
	objToDraw: null,
	
	render_unit: 50,
	
	storyModeOpacity: 0.65,
	storyOpacity: 0.65,
	scoreOpacity: 0,
	scoreTargetOpacity: 0,
	
	mistakeOpacity: 0,
	lastNrMistakes: null,
	
	setup: function()
	{
		this.canvas = document.getElementById("canvas"); 
		this.context = this.canvas.getContext("2d"); 
		this.w = this.canvas.width;
		this.h = this.canvas.height;
		
		/* load background image here */
		var bgr = new Image();
		bgr.onload = function() { gRenderEngine.bgr = this; gRenderEngine.loadCnt++; if(gRenderEngine.loadCnt == gRenderEngine.loadNeeded) gRenderEngine.ready = true; };
		bgr.src = this.bgrsrc;
		
		/* load objects image */
		var objimg = new Image();
		objimg.onload = function() { gRenderEngine.objects = this; gRenderEngine.loadCnt++; if(gRenderEngine.loadCnt == gRenderEngine.loadNeeded) gRenderEngine.ready = true; }; 
		objimg.src = this.objsrc;
		
		/* load rooms image here */
		var roomsimg = new Image();
		roomsimg.onload = function() { gRenderEngine.rooms = this; gRenderEngine.loadCnt++; if(gRenderEngine.loadCnt == gRenderEngine.loadNeeded) gRenderEngine.ready = true; };
		roomsimg.src = this.roomssrc;
		
		this.context.textBaseline="top";
		this.context.font = "26px 'Dancing Script'";
		
		this.storyOpacity = 0;
		
		this.lastNrMistakes = 3;
		
		console.log("game renderer set up!");
	},
	
	drawSprite: function(img,x,y)
	{
		this.context.drawImage(img,x,y);
	},
	
	drawRoom: function(room,x,y)
	{
		this.context.drawImage(this.rooms, room.frame.x, room.frame.y, room.frame.w, room.frame.h, x, y, room.frame.w, room.frame.h);
	},
	
	drawObject: function(obj,x,y)
	{
		this.context.drawImage(this.objects, obj.frame.x, obj.frame.y, obj.frame.w, obj.frame.h, x, y, obj.frame.w, obj.frame.h);
	},
	
	render: function()
	{
		this.context.clearRect(0,0,this.w,this.h); 
		/* draw background */
		if(gEngine.gameEnded) 
		{ 
			if(this.storyOpacity > 0.01) { var o = this.storyOpacity - (this.render_unit/10000); this.storyOpacity = o > 0.01 ? o : 0.01; }
		}
		else if(gEngine.gameOver)
		{
			if(this.storyOpacity > 0.01) { var o = this.storyOpacity - (this.render_unit/3500); this.storyOpacity = o > 0.01 ? o : 0.01; }
		}
		else
		{
			if(!gEngine.gameOver && this.storyOpacity < this.storyModeOpacity) 
			{ 
				this.storyOpacity = this.storyOpacity + (this.render_unit/10000);
				if(this.storyOpacity > this.storyModeOpacity) this.storyOpacity = this.storyModeOpacity;
			}
			if(gEngine.play_stage && this.storyOpacity < 1) this.storyOpacity = this.storyOpacity + (this.render_unit/2000); 
			if(!gEngine.play_stage && this.storyOpacity > this.storyModeOpacity) this.storyOpacity = this.storyOpacity - (this.render_unit/2000);
		}
		this.context.globalAlpha = this.storyOpacity;
		this.drawSprite(this.bgr,0,0);		
		
		/* draw score */
		
		if(gEngine.mistakes < this.lastNrMistakes)
		{
			this.mistakeOpacity = 1;
		}
		this.context.fillStyle = "#FF66BB"; 
		this.context.globalAlpha = this.mistakeOpacity;
		var text = "";
		if(gEngine.mistakes == 2) text = "Not, not that .";
		else if(gEngine.mistakes == 1) text = "No . One more try .";
		this.context.fillText(text,350,520);
		this.mistakeOpacity = this.mistakeOpacity - this.render_unit / 2500;
		if(this.mistakeOpacity < 0.01) this.mistakeOpacity = 0;
		this.lastNrMistakes = gEngine.mistakes;
		
		/*
		if(gEngine.gameEnded)
		{
			this.scoreTargetOpacity = this.storyOpacity;
		}
		else if(gEngine.gameOver)
		{
			this.scoreTargetOpacity = 1;
		}
		else if(!gEngine.play_stage)
		{
			this.scoreTargetOpacity = 0;
		}
		else
		{
			var strength = gEngine.mistakes < 1 ? 1 : 1/(gEngine.mistakes);
			this.scoreTargetOpacity = (this.context.globalAlpha + strength)/2;
			if(this.scoreTargetOpacity > 1) this.scoreTargetOpacity = 1;
			if(this.scoreTargetOpacity < 0) this.scoreTargetOpacity = 0;
		}
		this.context.fillStyle = "#AAFFCC"; 
		this.scoreOpacity = this.scoreOpacity + (this.scoreTargetOpacity - this.scoreOpacity) * (this.render_unit / 500);
		if(this.scoreOpacity > 1) this.scoreOpacity = 1;
		if(this.scoreOpacity < 0) this.scoreOpacity = 0;
		this.context.globalAlpha = this.scoreOpacity;
		this.context.fillText("Attempts left: " + gEngine.mistakes,350,520);
		*/
		
		/* if there is text */
		if(this.textToDraw != null) 
		{
			if(gEngine.gameOver) this.context.fillStyle = "#FF66BB";
			else this.context.fillStyle = "#BBAAFF";
			this.context.globalAlpha = this.textToDraw.opacity;
			if(!gEngine.gameOver && !gEngine.gameEnded  && this.storyModeOpacity >= this.storyOpacity) this.context.globalAlpha = this.context.globalAlpha * (1 - (this.storyModeOpacity - this.storyOpacity) / this.storyModeOpacity);
			var textroom = gEngine.rooms[this.textToDraw.room-1];
			for(var i = 0; i < this.textToDraw.text.length; ++i)
				this.context.fillText(this.textToDraw.text[i],textroom.x,textroom.y + 30*i);
			
		}
		
		/* if a room is open */
		if(this.roomToDraw != null) 
		{
			this.context.globalAlpha = this.roomToDraw.opacity;
			this.drawRoom(this.roomToDraw,0,0); 
		}
		
		/* if there are objects to draw */
		if(this.objToDraw != null) for(var i = 0; i < this.objToDraw.length; ++i)
		{
			this.context.globalAlpha = this.objToDraw[i].opacity;
			this.drawObject(this.objToDraw[i].obj,this.objToDraw[i].x,this.objToDraw[i].y);
		}
				
		this.context.globalAlpha = 1;
		
		/* preview room contour
		for(var i = 1; i <= 6; ++i)
		{
			var room = gEngine.rooms[i];
			this.context.strokeRect(room.x,room.y,room.w,room.h);
		}*/
	}
}
);