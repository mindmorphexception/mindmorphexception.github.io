function xhrGet(reqUri,reqCred,callback)
{
	var caller = xhrGet.caller;
	var xhr = new XMLHttpRequest();
	xhr.open("GET", reqUri, true);
	xhr.onreadystatechange = function() 
	{
		if (xhr.readyState == 4)
		{
			if (xhr.status != 200) {
				throw 'xhrGet failed:\n' + reqUri + '\nHTTP ' + xhr.status + ': ' + xhr.responseText + '\ncaller: ' + caller;
			}
			if(callback) {
				try {
					callback(xhr);
				} catch(e) {
					throw 'xhrGet failed:\n' + reqUri + '\nException: ' + e + '\nresponseText: ' + xhr.responseText + '\ncaller: ' + caller;
				}
			}
		}
	};
	if(reqCred)
		xhr.withCredentials = "true";
	xhr.send();
}