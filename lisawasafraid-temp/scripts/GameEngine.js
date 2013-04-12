GameEngineClass = Class.extend(
{
	rooms: new Array(),
	objects: new Array(),
	
	nrImgLoaded: 0,
	ready: false,
	
	story: null,
	stage: null,
	seq: null,
	
	game_unit: 50,
	play_stage: false,
	seq_fading_in: true,
	seq_fading_out: false,
	crt_time: 0,
	room_fading_in: false,
	room_fading_out: false,
	
	needsInput: true,
	
	targetProbability: 0.5,
	nrObjects: 0,
	
	roomOpened: null,
	lastRoomOpened: null,
	crtObjects: null,
	targetObject: null,
	targetObjectX: null,
	targetObjectY: null,
	objSizeX: 30,
	objSizeY: 30,
	
	objOpacity: 0.8,
	
	gameEnded: false,
	mistakes: 1,
	
	/*
	sequence_time: 8000,
	sequence_pause: 2000,
	is_sequence_pause: true,
	crt_time: 5000,
	*/
	
	loadJSON: function(filename)
	{
		var oRequest = new XMLHttpRequest();
		oRequest.open("GET", "./json/" + filename + ".json", false);
		oRequest.send();
		if (oRequest.status!=200) 
		{
			alert("Error executing XMLHttpRequest call!");
			return;
		}
		var response = oRequest.responseText;
		var parsed = JSON.parse(response);
		return parsed;
	},
	
	setup: function()
	{	
		console.log('loading rooms');
		/* load rooms from json */
		var parsed = this.loadJSON("rooms");
		this.rooms = parsed["rooms"];
		
		/* add some computations to rooms */
		for(var i=0; i < this.rooms.length; ++i)
		{
			this.rooms[i].x = this.rooms[i].left;
			this.rooms[i].y = this.rooms[i].high;
			this.rooms[i].w = this.rooms[i].right - this.rooms[i].left;
			this.rooms[i].h = this.rooms[i].low - this.rooms[i].high;
		}
		
		console.log('loading objects');
		/* load objects json */
		var parsed = this.loadJSON("objects");
		this.objects = parsed["objects"];
				
		console.log('loading story');
		/* load story from json */
		parsed = this.loadJSON("story");
		this.story = parsed["stages"];
		this.stage = 0;
		this.seq = 0;	// usage: this.story[stage].sequences[sequence].text[line]
		
		this.mistakes = 1;
		
		this.roomOpened = null;
		this.lastRoomOpened = null;
		this.crtObjects = null;
		this.targetObject = null;
		this.targetObjectX = null;
		this.targetObjectY = null;
		
		this.play_stage = false;
		this.seq_fading_in = true;
		this.seq_fading_out = false;
		this.crt_time = 0;
		this.room_fading_in = false;
		this.room_fading_out = false;

		this.needsInput = true;
		
		console.log("game engine set up!");
		this.ready = true;
		
				
	},
	
	getCrtRoom: function()
	{
		return this.rooms[this.roomOpened];
	},
	
	start: function()	//send the first text to renderer
	{
		this.crt_time = 0;
		this.seq_fading_in = true;
		gRenderEngine.textToDraw = this.story[this.stage].sequences[this.seq];	// send to renderer
		this.story[this.stage].sequences[this.seq].opacity = 0;					// start with opacity 0
	},
	
	update: function() 
	{		
		if(this.gameEnded) return;
		
		if(!this.play_stage)	// -------------------- if we're in text drawing stage ------------------------------------
		{
			if(this.seq_fading_in)	// if we're fading in a sequence now
			{
				this.crt_time = this.crt_time + this.game_unit;	//increase crt timer
				var opacity = this.crt_time / 1000;		
				gRenderEngine.textToDraw.opacity = opacity;	// increase text opacity
				
				if(opacity == 1 && this.story.length <= this.stage+1 && gRenderEngine.textToDraw.length <= this.seq+1)	// if this was the last sequence of the last stage
						{
							console.log("Thank you for checking out the console for my game :P. Have a beautiful day!");
							// clear everything. yes at this point I am writing unnecessary code because I have 24h to the deadline so it might not all make sense...
							this.needsInput = false;
							this.seq_fading_in = false;
							this.room_fading_in = false;
							this.roo_fading_out = false;
							this.seq_fading_out = false;
							gRenderEngine.objToDraw = null;
							gRenderEngine.roomToDraw = null;
							this.gameEnded = true;
							return;
						}
				
				if(opacity > 0.75)
				{						
					this.needsInput = true;		// for impatient users - allow click before full fade in
					if(gInputEngine.clicked && gInputEngine.isinroom(this.story[this.stage].sequences[this.seq].room-1)) // if the user clicked on the correct text
						this.seq_fading_out = true;			// fade out seq after full fade in
				}
				if(opacity == 1)	// if we're finished fading in the text
				{
					this.seq_fading_in = false;	// no more fading in
				}
			}
			else if(this.seq_fading_out)
			{
				this.crt_time = this.crt_time - this.game_unit;	//increase crt timer
				var opacity = this.crt_time / 1000;		
				this.story[this.stage].sequences[this.seq].opacity = opacity;	// decrease text opacity
				
				if(opacity < 0.7 && this.story[this.stage].sequences.length == this.seq+1)	// if this was actually the last sequence of this stage
				{
					this.seq = 0;
					this.play_stage = true;
					//console.log("entering play stage...");
					this.crt_time = 0;
					this.needsInput = true;
					this.roomOpened = null;
					this.seq_fading_out = false;		
					this.targetProbability = this.targetProbability * 0.95;
					if(this.nrObjects < 5) this.nrObjects++;
				}
				
				if(opacity == 0)	// if we're finished fading in the text
				{
					this.seq_fading_out = false;			// no more fading out
					gRenderEngine.textToDraw = null;		// remove the text to draw from renderer
					this.seq++;								// move to next sequence 
					
					// start fading in next sequence					
					this.crt_time = 0;
					this.seq_fading_in = true;
					gRenderEngine.textToDraw = this.story[this.stage].sequences[this.seq];	// send to renderer
					this.story[this.stage].sequences[this.seq].opacity = 0;					// start with opacity 0
				
				}
			}
			else if(this.needsInput)	// if we were waiting for input to fade out a sequence
			{
				if(gInputEngine.clicked && gInputEngine.isinroom(this.story[this.stage].sequences[this.seq].room-1)) // if the user clicked on the correct text
				{
					
					//start fading out && don't need input anymore
					this.crt_time = 1000;
					this.seq_fading_out = true;
					this.needsInput = false;
					gInputEngine.unclick();
				}
				gInputEngine.unclick();	//clear input
			}
		}
		else 	// --------------------- if it's game stage --------------------------------
		{
			if (this.room_fading_in)	// if we're fading in a room now
			{
				this.crt_time = this.crt_time + this.game_unit;
				var opacity = this.crt_time / 1000;		//increase opacity
				this.rooms[this.roomOpened].opacity = opacity;
				if(opacity < this.objOpacity) 
				{
					for(var i = 0; i < this.crtObjects.length; ++i)
					{
						this.crtObjects[i].opacity = opacity;
					}
				}
				
				if(opacity == 1)	// if we finished fading in the room
				{
					this.room_fading_in = false;
					this.needsInput = true;
				}
			}
			
			else if(this.room_fading_out)	// if we're fading the room after target successfully clicked or game over
			{
				this.crt_time = this.crt_time - this.game_unit;
				var opacity = this.crt_time / 1000;		//decrease opacity
				this.rooms[this.roomOpened].opacity = opacity;
				for(var i = 0; i < this.crtObjects.length; ++i)
					this.crtObjects[i].opacity = opacity;
				if(opacity < 0.7) gRenderEngine.textToDraw.opacity = opacity;
				if(opacity == 0)	// if we finished fading out the room
				{
					this.room_fading_out = false;
					this.roomOpened = null;
					// reset stuff
					this.crtObjects = null;
					this.targetObject = null;
					this.targetObjectX = null;
					this.targetObjectY = null;
					gRenderEngine.objToDraw = null;
					gRenderEngine.roomToDraw = null;
					gRenderEngine.textToDraw = null;
					
					this.crt_time = 0;
					this.stage++;	// move to next stage
					this.seq_fading_in = true;
					if(this.stage > this.story.length)	// if game over
					{
						gRenderEngine.textToDraw = new Object();
						gRenderEngine.textToDraw.text = new Array();
						gRenderEngine.textToDraw.text[0] = 'You slept away...';
						gRenderEngine.textToDraw.text[1] = 'Try again.';
						gRenderEngine.textToDraw.room = 4;
					}
					else
					{
						gRenderEngine.textToDraw = this.story[this.stage].sequences[this.seq];	// send to renderer
					}
					gRenderEngine.textToDraw.opacity = 0;					// start with opacity 0
					this.play_stage = false;
					
				}
			}
		
			//else if(!gInputEngine.clicked) return;	//else if nothing was clicked there's nothing to do // L.E. oh yes there is
			
			if(gInputEngine.clicked)	// if user has clicked
			{
				if(this.roomOpened == null && gInputEngine.clicked)	//if no room is open
				{
					for(var i = 0; i < gEngine.rooms.length; ++i)	// check if user has clicked a room
					{
						if(i == gRenderEngine.textToDraw.room-1) continue;		// skip if text in room
						if(gInputEngine.isinroom(i))	// if a room was clicked
						{
							this.generateRoom(i);	// open it
							break;
						}
					}
				}
				else
				{
					var objClicked = false;
					//check if an object was clicked 
					for(var i = 0; !objClicked && i < this.crtObjects.length; ++i)
					{
						if(gInputEngine.objectWasClicked(i))
						{
							// if it's not the target, remove it
							if(this.crtObjects[i].obj.filename != this.targetObject)
							{
								this.crtObjects.splice(i,1);
								this.mistakes--;
								if(this.mistakes < 1)		// game over
								{
									this.needsInput = false;
									this.room_fading_out = true;
									this.crt_time = 1000;
									this.rooms[this.roomOpened].opacity = 1;
									this.stage = 10000;
									this.sequence = 10000;
									this.crtObjects = new Array();
								}
							}
							else	// if it's the target...
							{
								this.mistakes++;
								// remove other objects
								var j = 0;
								while(this.crtObjects.length > 1)
								{
									if(this.crtObjects[j].obj.filename == this.targetObject) 
									{ 
										this.crtObjects[j].opacity = 1;
										++j; 
									}
									else this.crtObjects.splice(j,1);
								}
								gRenderEngine.objToDraw = this.crtObjects;
								
								this.needsInput = false;
								this.room_fading_out = true;
								this.crt_time = 1000;
								this.rooms[this.roomOpened].opacity = 1;
							}
							objClicked = true;
						}
					}
					
					if(!objClicked) // object was not clicked
					{
						// if a room was clicked, fade it in and close the current
						for(var i = 0; i < gEngine.rooms.length; ++i)	// check if user has clicked a room
						{
							if(i == gRenderEngine.textToDraw.room-1) continue;		// skip if text in room
							if(i == this.roomOpened) continue;		// skip if same room
							if(gInputEngine.isinroom(i))	// if a room was clicked
							{
								// close crt room
								gRenderEngine.roomToDraw = null;
								this.lastRoomOpened = this.roomOpened;
								this.roomOpened = null;
								this.crtObjects = null;
								gRenderEngine.objToDraw = null;
								this.needsInput = true;
						
								this.generateRoom(i);	// open it
								break;
							}
						}
					}
				}
				gInputEngine.unclick();		// erase the click
			}
			
			else if(this.roomOpened != null)	// if user a room is opened, maybe the user has a mouse over an object
			{
				for(var i = 0; i < this.crtObjects.length; ++i)
				{
					if(gInputEngine.mouseOverObject(i))
						this.crtObjects[i].opacity = 1;
					else
					{
						this.crtObjects[i].opacity = this.objOpacity > this.rooms[this.roomOpened].opacity ? this.rooms[this.roomOpened].opacity : this.objOpacity;
					}
				}
			}
			
			
		}
		
		
		/* code for alternate fixed time text flow:
		this.crt_time = this.crt_time - 100;
		if(this.crt_time == 0)
		{
			if(this.is_sequence_pause)	// we need to draw the next sequence of this stage
			{
				console.log("drawing sequence");
				console.log(this.seq);
				this.is_sequence_pause = false;		// no more sequence pause 
				gRenderEngine.textToDraw = this.story[this.stage].sequences[this.seq];	// add text to draw
				this.story[this.stage].sequences[this.seq].opacity = 0;	//start with opacity 0 to fade in
				this.crt_time = this.sequence_time;	// set timer to sequence duration
			}
			else	// we need to erase current text and pause, or switch to next stage
			{
				console.log("pauseing sequence");
				this.crt_time = this.sequence_pause;	// set timer to sequence pause duration
				gRenderEngine.textToDraw = null;		// no text to draw
				this.is_sequence_pause = true;			// start the pause
				this.seq++;								// move to next sequence in current stage
				if(this.story[this.stage]..sequences.length == this.seq)	// if this was the last sequence of this stage
				{
					console.log("entering next stage...");
					this.crt_time = -1;
				}
			}
		}
		else if(gRenderEngine.textToDraw != null && this.crt_time > this.sequence_time-1000)	// if we've recently drawn some text
		{
			this.story[this.stage].sequences[this.seq].opacity = (this.sequence_time - this.crt_time) / 1000;		//fade it in
		}
		
				else if(gRenderEngine.textToDraw != null && this.crt_time < 1000)	// if we're going to remove text shortly
		{
			this.story[this.stage].sequences[this.seq].opacity = this.crt_time / 1000;		//fade it out
		}
		*/
	},
	
	generateRoom: function(i)	// ---------- function to generate a room --------------
	{
		this.roomOpened = i;
		gRenderEngine.roomToDraw = this.rooms[i];	//send it to renderer	
		this.rooms[i].opacity = 0;	//set initial opacity to 0
		this.room_fading_in = true;
		this.crt_time = 0;
		
		// pick some objects to draw in room
		this.targetObject = this.story[this.stage].object;	// set target object to be clicked
		this.targetObjectX = null;
		this.targetObjectY = null;
		var picked = 0;
		this.crtObjects = new Array();
		if(Math.random() > 0.5)		// pick target with probability this.targetProbability... no let's leave this to half-certainty for impatient n00bs
		{
			this.crtObjects[0] = new Object();
			this.crtObjects[0].obj = this.findObjByName(this.targetObject);
			this.crtObjects[0].x = Math.floor( this.rooms[this.roomOpened].left + Math.random() * (this.rooms[this.roomOpened].right - this.rooms[this.roomOpened].left - this.objSizeX));
			this.crtObjects[0].y = Math.floor( this.rooms[this.roomOpened].high + Math.random() * (this.rooms[this.roomOpened].low - this.rooms[this.roomOpened].high - this.objSizeY));
			this.crtObjects[0].opacity = 0;
			picked++;
			this.targetObjectX = this.crtObjects[0].x;	// update target coordinates
			this.targetObjectY = this.crtObjects[0].y;
		}
		
		if(this.nrObjects > 1) while(picked < this.nrObjects)	// pick rest of objects unless it's the first time
		{
			var x,y;
			do	//pick some coordinates that don't colide with the other objects
			{
				x = Math.floor( this.rooms[this.roomOpened].left + Math.random() * (this.rooms[this.roomOpened].right - this.rooms[this.roomOpened].left - this.objSizeX));
				y = Math.floor( this.rooms[this.roomOpened].high + Math.random() * (this.rooms[this.roomOpened].low - this.rooms[this.roomOpened].high - this.objSizeY));
			} while (this.collides(x,y));
			
			var pickedobj;
			do	// pick an object that was not selected already
			{
				 pickedobj = Math.floor(Math.random() * this.objects.length);
			} while(this.alreadySelectedObject(this.objects[pickedobj].filename));
			
			this.crtObjects[picked] = new Object();
			this.crtObjects[picked].obj = this.objects[pickedobj];
			this.crtObjects[picked].x = x;
			this.crtObjects[picked].y = y;
			this.crtObjects[picked].opacity = 0;
			if(this.crtObjects[picked].obj.filename == this.targetObject)	// if this happens to be the target, update target coordinates
			{
				this.targetObjectX = x;
				this.targetObjectY = y;
			}
			picked++;
		}
		
		gRenderEngine.objToDraw = this.crtObjects;
		
		
		this.needsInput = true;
	},
	
	findObjByName: function(name)	// find an object in the array by name, if not there return null
	{
		for(var i = 0; i < this.objects.length; ++i)
			if(this.objects[i].filename == name) return this.objects[i];
		return null;
	},
	
	collides: function(x,y)	// check if an obj placed at x,y collides with any of the already picked objects
	{
		for(var i = 0; i < this.crtObjects.length; ++i)
		{	
			var o = this.crtObjects[i];
			var xbetween = ((x <= o.x + this.objSizeX) && (o.x <= x + this.objSizeX));
			var ybetween = ((y <= o.y + this.objSizeY) && (o.y <= y + this.objSizeY));
			if(xbetween && ybetween) return true;
		}
		return false;
	},
	
	alreadySelectedObject: function(name)
	{
		for(var i = 0; i < this.crtObjects.length; ++i)
		{	
			if(this.crtObjects[i].obj.filename == name) return true;
		}
		return false;
	}
	
}
);