jQuery(document).ready(function(){

	var xhr = new XMLHttpRequest();
	
	// Detect if XHR is available
	//
	if (xhr.upload) { // use xhr
		
		$('#file_upload').css('display','block');
		
		$('#file_upload_filename').live('change', function(){
			
			var file=$("#file_upload_filename")[0].files[0];
			
			UploadFile(file);
		});	
		
	} else { // use flash
	
		$('#file_upload_flash').css('display','block');
	}
	
	$('span.close').live('click',
		function(){
			// clicking the close span causes the closest message to fadeout
			$(this).closest('.message').fadeOut(1000);
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
	
	// XHR HTML5 upload
	//
	function UploadFile(file) {

		var xhr = new XMLHttpRequest();

			// create progress bar
			var progressDiv = $id("file_upload_progress");
			$(progressDiv).fadeIn('fast');
			var progress = progressDiv.appendChild(document.createElement("p"));
			progress.appendChild(document.createTextNode($("#file_upload_loading").val()));

			// progress bar
			xhr.upload.addEventListener("progress", function(e) {
				var pc = parseInt(100 - (e.loaded / e.total * 100));
				progress.style.backgroundPosition = pc + "% 0";
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				
				if (xhr.readyState==4) //4: request finished and response is ready
				{
					var response=jQuery.parseJSON(xhr.responseText); // response from server
					
						if (response.status==='success')
						{
							// filename in response.filename
							progress.className = (xhr.status == 200 ? "success" : "failure");
							progress.innerHTML=$("#file_upload_complete").val();
							$(progressDiv).fadeOut('slow');
							progress.parentNode.removeChild(progress);
							UploadFileSuccess(response);
							
						} else {
							$(progressDiv).fadeOut('slow');
							progress.parentNode.removeChild(progress);
							message(1,response.errormessage,'error');
						}
				}
			};

			// start upload
			// modify path to server side php as required
			xhr.open("POST","fileupload.php?ajax=true&class=FileUpload&variables=true&uploadfilenameprefix=upload&uploadresize=0x0",true);
			xhr.setRequestHeader("X_FILENAME", file.name);
			xhr.send(file);
			// reset file upload input element
			var control = $("#file_upload_filename");
			control.replaceWith( control = control.clone( true ) );

	}
	
	// Flash upload using uploadify
	//
    $('#file_upload_flash_uploader').uploadify({
        'swf'      : 'lib/flash/js/uploadify.swf',
		// modify path to server side php as required
        'uploader' : 'fileupload.php?ajax=true&class=FileUpload&variables=true&uploadfilenameprefix=upload&uploadresize=0x0',
		'buttonText' : 'Upload',
        'onUploadStart' : function(file) {
			hideshow('file_upload_flash_loading',1);
        },		
        'onUploadSuccess' : function(file, data, response) {
         
		 var response = jQuery.parseJSON(data);
		 
			 if (response.status==='success')
			 {
				hideshow('file_upload_flash_loading',0);
				UploadFileSuccess(response);
				
			 } else {
				hideshow('file_upload_flash_loading',0);
				message(1,response.errormessage,'error');
			}
			 
		},

        'onUploadError' : function(file, data, response) {
		 var response = jQuery.parseJSON(data);
			hideshow('file_upload_flash_loading',0);
			message(1,response.errormessage,'error');
			//alert('Error: ' + response.output);
		}		
		
    });	
	

	// upload success function
	function UploadFileSuccess(response)
	{
		//message(1,response.output + " - " + response.filename,'success');						
		
		// DEMO MESSAGE
		message(1,response.output + " - " + response.filename + " " + $("#demo_message").val(),'success');
	}

	// display message
	function message(act,txt,el)
	{
		hideshow(el,act);
		if(txt) $('#' + el).html(txt + '<span class="close"><img src="lib/images/close_icon.png"</span>');
	}		
	
	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}
	
	// hide / show element with or without timer fade
	function hideshow(el,act,fade)
	{
		if(act) {
			$('#'+el).css('visibility','visible');
			fade = typeof fade !== 'undefined' ? fade : true;
			if (fade) {
				setTimeout(function() {
					$('#'+el).fadeOut("slow", function() {
						$('#'+el).show().css({visibility: "hidden"});
					});
				}, 10000);
			}
		} else {
			$('#'+el).css('visibility','hidden');
		}
	}

});