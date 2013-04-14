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
	
	containsName: function(name)
	{
		console.log('contains?');
		for(var i = 0; i < this.objects.length; ++i)
		{
			console.log(this.objects[i].obj.filename + " " + name);
			if(this.objects[i].obj.filename == name) return true;
		}
		return false;
	}
}
);