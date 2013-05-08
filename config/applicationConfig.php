<?php
/*


*/

// Edit configuration settings here
//
//

class config
{

	// path to upload area - must have write access
	const uploadFileCache='/home/www/medazzaland/cache';
	// allowed file types
	const allowedUploadFileTypes='txt,doc';
	const emailEnabled=true;
	const emailTo='files@gaiterjones.com';
	const emailFrom='files@gaiterjones.com';	

	public function __construct()
	{


	}
	
	
    public function get($constant) {
	
	    $constant = 'self::'. $constant;
	
	    if(defined($constant)) {
	        return constant($constant);
	    }
	    else {
	        return false;
	    }

	}

	/**
	 * serverURL function.
	 * 
	 * @access public
	 * @return string
	 */
	public function serverURL() {
	 $_serverURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$_serverURL .= "s";}
	 $_serverURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $_serverURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	 } else {
	  $_serverURL .= $_SERVER["SERVER_NAME"];
	 }
	 return $_serverURL;
	}
	
	private function serverPath() {
	 $_serverPath=$_SERVER["REQUEST_URI"];
	 //$_serverPath=explode('?',$_serverPath);
	 //$_serverPath=$_serverPath[0];
	 
	 return $_serverPath;
	}
	
}

function autoloader($class) {
	if ($class==='Memcache') { return; }
	require_once 'php/class.' . $class . '.php';
}

spl_autoload_register('autoloader');
?>