function AJAX(url, callback)
{
	var req = init();
	req.onreadystatechange = processRequest;
	
	function init()
	{
		if (window.XMLHttpRequest)
		{
			return new XMLHttpRequest();
		}
		else
		{
			try
			{
				return new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				return new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
	}

	function processRequest()
	{
		if (req.readyState == 4)
		{
			if (req.status == 200)
			{
				if (callback)
					callback(req.responseText);
			}
		}
	}

	this.doGet = function()
	{
		req.open("GET", url, true);
		req.send(null);
	}

	this.doPost = function(body)
	{
		req.open("POST", url, true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.setRequestHeader("Content-length", body.length);
		req.setRequestHeader("Connection", "close");
		req.send(body);
	}
}

// One thing to note when making multiple AJAX calls from the client is that the calls are not guaranteed to return in any given order. 
// Having closures within the callback of a closure object can be used to ensure dependencies are processed correctly.

// There is a discussion titled Ajaxian Fire and Forget Pattern that is helpful. 