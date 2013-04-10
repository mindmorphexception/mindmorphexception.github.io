RenderEngineClass = Class.extend(
{
	canvas: null,
	context: null,
	w: null,
	h: null, 
	bgr: null,
	roomToDraw: null,
	textToDraw: null,
	objToDraw: null,
	
	render_unit: 100,
	
	storyOpacity: 0.7,
	
	setup: function()
	{
		this.canvas = document.getElementById("canvas"); 
		this.context = this.canvas.getContext("2d"); 
		this.w = this.canvas.width;
		this.h = this.canvas.height;
		
		/* load backgrund image here */
		var img = new Image();
		img.onload = function() { gRenderEngine.bgr = this; };
		img.src = "img/background.png";
		
		this.context.textBaseline="top";
		this.context.font = "26px 'Dancing Script'";
		this.context.fillStyle = "#BBAAFF";
		
		this.hasChanged = true;
		
		console.log("game renderer set up!");
	},
	
	drawSprite: function(img,x,y)
	{
		this.context.drawImage(img,x,y);
	},
	
	render: function()
	{
		this.context.clearRect(0,0,this.w,this.h); 
		/* draw background */
		if(gEngine.gameEnded) 
		{ 
			if(this.storyOpacity > 0.01) this.storyOpacity = this.storyOpacity - 0.01; 
		}
		else
		{
			if(gEngine.play_stage && this.storyOpacity < 1) this.storyOpacity = this.storyOpacity + 0.05;
			if(!gEngine.play_stage && this.storyOpacity > 0.7) this.storyOpacity = this.storyOpacity - 0.05;
		}
		this.context.globalAlpha = this.storyOpacity;
		this.drawSprite(this.bgr,0,0);
		this.context.globalAlpha = 1;
		
		/* if there is text */
		if(this.textToDraw != null) 
		{
			this.context.globalAlpha = this.textToDraw.opacity;
			var textroom = gEngine.rooms[this.textToDraw.room-1];
			for(var i = 0; i < this.textToDraw.text.length; ++i)
				this.context.fillText(this.textToDraw.text[i],textroom.x,textroom.y + 30*i);
			this.context.globalAlpha = 1;
		}
		
		/* if a room is open */
		if(this.roomToDraw != null) 
		{
			this.context.globalAlpha = this.roomToDraw.opacity;
			this.drawSprite(this.roomToDraw.backgr,0,0);
		}
		
		/* if there are objects to draw */
		if(this.objToDraw != null) for(var i = 0; i < this.objToDraw.length; ++i)
		{
			this.drawSprite(this.objToDraw[i].obj.img,this.objToDraw[i].x,this.objToDraw[i].y);
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