// EVENTS FRONTEND

jQuery(document).ready(function(){

// frontend / backend event listeners
$(document).on("PAJ_Application_Media_Upload_Started", PAJ_Application_Media_Upload_Started);
$(document).on("PAJ_Application_Media_Upload_Complete", PAJ_Application_Media_Upload_Complete);
$(document).on("PAJ_Application_Media_Upload_Failed", PAJ_Application_Media_Upload_Failed);


});

//
// MEDIA UPLOAD
//

var timeoutId1;

function PAJ_Application_Media_Upload_Failed(e)
{
	clearTimeout(timeoutId1); // clear timers
	
	$("#file_upload_radial_progress").fadeOut("slow");
	$("#file_upload_filename").prop('disabled', false);
	$("#file_upload_control_wrapper").fadeIn("slow");

	$('#file_upload_commentary').text('');
	$('#file_upload_speed').text('');
	
	error('notification',e.errormessage);
}

function PAJ_Application_Media_Upload_Complete(e)
{
	clearTimeout(timeoutId1); // clear timers
	
	$("#file_upload_filename").prop('disabled', false);
	$("#file_upload_control_wrapper").fadeIn("slow");
	
	$('#file_upload_commentary').text('');
	$('#file_upload_speed').text('');
	
	success('notification',e.filename + ' uploaded');
	
}

function PAJ_Application_Media_Upload_Started(e)
{
	$("#file_upload_radial_progress").fadeIn("slow");
	$("#file_upload_control_wrapper").fadeOut("slow");
	$("#file_upload_filename").prop('disabled', true);
	
	timeoutId1=setTimeout(function() {
		$('#file_upload_commentary').text('the file (' + e.fileSize + ') is uploading...');
	}, 2000);
}