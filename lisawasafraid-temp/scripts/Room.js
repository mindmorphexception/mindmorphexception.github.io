RoomClass = Class.extend(
{
	objects: new Array(),
	
	nrObjects: function()
	{
		if(this.objects) 
			return this.objects.length();
		return -1;
	},
	
	add: function(object)
	{
		var newObj = new Object();
		newObj.obj = object;
		this.objects.push(newObj);
	},
	
	contains: function(name)
	{
		for(var i = 0; i < this.objects.length; ++i)
			if(this.objects[i].filename == name) return true;
		return false;
	}
}
);