// global vars

jQuery(document).ready(function(){

	$("#message_container").on("click", ".close", function() {	
			// clicking the close span causes the closest message to fadeout
			$(this).closest('.message').fadeOut(500);
    });		

	$('.message').hover(
    function() {
        // while hovering over the message, it fades the close element in after a delay
        $(this).find('.close').delay(500).fadeIn(500);
    },
    function() {
        // after leaving/mouseout of the the message, has a delay and then fades the close out
        $(this).find('.close').delay(1000).fadeOut(500);
    });	
	
	$("#toggle_uploaders").click(function(){
		$('#file_upload_flash').toggle();
		$('#file_upload').toggle();
	});	
	
});	

// global functions


// AJAX Requests
//	
function ajaxRequest(ajaxVars,el,callbackFunction,phpuri)
{
	if (typeof phpuri === "undefined" || phpuri===null) phpuri = '/index.php';
	
	var xmlhttp;
	
	if ('withCredentials' in new XMLHttpRequest()) {
		/* supports cross-domain requests */
		//console.log("CORS supported (XHR)");
		xmlhttp=new XMLHttpRequest();
		typeCor='XHR2';
	} else {
	
		if(typeof XDomainRequest !== "undefined"){
			//Use IE-specific "CORS" code with XDR
			//console.log("CORS supported (XDR)");
			xmlhttp=new XDomainRequest();
			typeCor='ie-XDR';
			
		} else {
	  
			//console.log("No CORS Support!");
			typeCor='ie-X';
			if($.browser.msie) {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			} else {
				// browser not supported
				callbackFunction(false,'Error - browser not supported.',el);
			}
		}
	}	

	function alert_timeout()
	{
		callbackFunction(false,'Timeout',el);
	}

	function alert_error()
	{
		callbackFunction(false,'Error',el);
	}		
	
	function alert_loaded()
	{
		var php=jQuery.parseJSON(xmlhttp.responseText);
		
			if(php.status==='success')
			{			
				callbackFunction(true,php,el);
			
			} else {
			
				callbackFunction(false,php,el);
			}	
		
	}	
	
	var sendString='';
	for (var i = 0; i < ajaxVars.phpClassVariableNames.length; i++) {
		sendString=sendString+ajaxVars.phpClassVariableNames[i] + '=' + ajaxVars.phpClassVariableValues[i];
			if ((i+1) < ajaxVars.phpClassVariableNames.length) { sendString=sendString + '&'; }
	}	
	
	xmlhttp.open("POST",phpuri + "?ajax=true&class=" + ajaxVars.phpClassName[0],true);
	xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xmlhttp.timeout = 30000; // 30 seconds
	xmlhttp.ontimeout = alert_timeout;
	xmlhttp.onerror = alert_error;
	xmlhttp.onload = alert_loaded;		
	xmlhttp.send("variables=true&" + sendString);
}

function replaceAll(find, replace, str) {
  while( str.indexOf(find) > -1)
  {
	str = str.replace(find, replace);
  }
  return str;
}
	
function $id(id) {
	return document.getElementById(id);
}

function success(el,message,fade) {
	if (typeof fade === "undefined" || fade===null) fade = 1;
	$('#' + el).removeClass('error').addClass('success');
	if(message) $('#' + el).html(message + '<span class="close"><img src="lib/images/close_icon.png"</span>');
	hideshow(el,1,fade);	
}

function info(el,message,fade) {
	if (typeof fade === "undefined" || fade===null) fade = 1;
	$('#' + el).removeClass('error').addClass('info');
	if(message) $('#' + el).html(message + '<span class="close"><img src="lib/images/close_icon.png"</span>');
	hideshow(el,1,fade);	
}		

function error(el,message,fade)
{
	if (typeof fade === "undefined" || fade===null) fade = 1;
	$('#' + el).removeClass('success').addClass('error');
	if(message) $('#' + el).html(message + '<span class="close"><img src="lib/images/close_icon.png"</span>');
	hideshow(el,1,fade);
}

function hideshow(el,act,fade)
{
	if (typeof fade === "undefined" || fade===null) fade = 1;
	
	if(act) {
		$('#'+el).css('visibility','visible');
		
		if(fade) {
		$('#'+el).stop(true).hide().fadeTo(500,1);
		
		clearTimeout(window.elTimeout);
		
			window.elTimeout=setTimeout(function() {
				$('#'+el).fadeOut("slow", function() {
					$('#'+el).show().css({visibility: "hidden"});
				});
			}, 5000);
		}			
		
	} else {
		$('#'+el).css('visibility','hidden');
	}
}

function isChecked(el)
{
	if ($(el).prop('checked')) { return true; }
	
	return false;
}

function goBack() {
    window.history.back();
	return false;
}

