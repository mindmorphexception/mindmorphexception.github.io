InputEngineClass = Class.extend(
{
	x: null,
	y: null,
	clicked: false,
	lastRoomHovered: null,
	hoveredTime: 0,
	hoveredTimeRequired: 2000,
	
	setup: function()
	{
		gRenderEngine.canvas.addEventListener('mouseup', this.mouseup, false);	
		console.log("input engine set up!");
	},
	
	mouseup: function(event) 
	{
		if (gEngine.needsInput && event.button == 0)	// text stage and needs input to fade out text
		{
			gInputEngine.clicked = true;
			gInputEngine.x = (event.offsetX || event.clientX - $(event.target).offset().left + window.pageXOffset );	// annoying Firefox
			gInputEngine.y = (event.offsetY || event.clientY - $(event.target).offset().top + window.pageYOffset );
		}
		
	},
	
	mouseover: function()	// this updates the room hovered over in play stage
	{
		//if(!gEngine.play_stage) return;
		console.log('hover');
	},
	
	unclick: function()
	{
		this.x = null; this.y = null; this.clicked = false;
	},
	
	isinroom: function(roomIndex)
	{
		var room = gEngine.rooms[roomIndex];
		if(this.x && this.y && this.x > room.left && this.x < room.right && this.y > room.high && this.y < room.low) return true;
		return false;
	},
	
	targetWasClicked: function()
	{
		if(!this.x || !this.y || !gEngine.targetObjectX || !gEngine.targetObjectY) return false;
			
		if(this.x > gEngine.targetObjectX && 
			this.x < gEngine.targetObjectX + gEngine.objSizeX && 
			this.y > gEngine.targetObjectY && 
			this.y < gEngine.targetObjectY + gEngine.objSizeY) return true;
			
		return false;
	}
}
);