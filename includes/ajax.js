function getAJAXRequest()
{
	var ajaxRequest; 
	
    try
    {
    	ajaxRequest = new XMLHttpRequest();
    }
    catch (e)
    {
    	try
        {
        	ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e)
        {
        	try
            {
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e)
            {
				ajaxRequest = false;
            }
        }
	}

	return ajaxRequest;
}
