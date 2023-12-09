function search(mode)
{

	var patt = /^\s+|\s+$/g;
	// DO a search
	if(mode)
	{
		var url = '';
		var q = '';

		if( jQuery("[name='q']").length )
		{
		  q = jQuery("[name='q']").val().replace(patt,'');
    }

		// User Listing
		if( jQuery("select[name='role_id']").length )
		{
	     var v = jQuery("select[name='role_id'] option:selected").val();
		   if(v!='')
		   {
			  url += "&role_id=" + v;
		   }
		}

		// text query
		if(q!='')
		{
			url += "&q=" + q;
		}

		if( url != "" )
		{
		  location.href = base_url + "?" + url;
		}
	}
	// RESET
	else
	{
		location.href = base_url;
	}
 }

 function resetsearch()
 {
    location.href = base_url;
 }
