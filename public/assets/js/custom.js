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
 
 function goto(t)
 {
	 if(t != '')
	 {
	   location.href = base_url;
	 }
 }
 function togglestatus(id)
 {
   var frm = document.getElementById("toggleStatusForm");
   if(frm && frm.elements[0])
   {
     frm.elements[0].value=id;
     frm.submit();
   }
 }

function setTicketType(x,y)
{
	var t = document.querySelector("[name=type]");
	if( t ) t.value = y;
  jQuery(".ticketType").removeClass('selected');
	jQuery(jQuery(".ticketType")[x]).addClass('selected');
}

function setPaymode(x,y)
{
	var t = document.querySelector("[name=payment_type]");
	if( t ) t.value = y;
  jQuery(".payMode").removeClass('selected');
	jQuery(jQuery(".payMode")[x]).addClass('selected');
}
// making "cash default"
$(document).ready(function () {
    //setPaymode(0, 1);
});

function setVerification(x,y)
{
	var t = document.querySelector("[name=verification]");
	if( t ) t.value = y;
  jQuery(".verification").removeClass('selected');
	jQuery(jQuery(".verification")[x]).addClass('selected');
}
