<?php
/*


*/

// Edit configuration settings here
//
//

class config
{

	// path to upload area - must have write access
	const uploadFileCache='/home/www/medazzaland/cache/';
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

// PAJ PHP class autoloader
//	-- e.g. Application_Class_Name1_Name2 loads > ./php/Class/Name1/Name2.php
//
//
function classAutoLoader($_class) {

	$_applicationRoot='php/';
	
		$_includeArray=explode('_',$_class);
		$_includeFilename=$_includeArray[count($_includeArray) -1]. '.php';
		$_includePath=$_applicationRoot;
		
		if (count($_includeArray) > 1)
		{
			foreach ($_includeArray as $_folder)
			{
				if ($_folder === 'Application') { continue; }
				$_includePath=$_includePath. $_folder. '/';
			}
			
			$_includePath=$_includePath.$_includeFilename;
			
		} else {
			
			if ($_includeArray[0] === 'Application')
			{
				$_includePath=$_includePath. $_includeArray[0]. '.php';
			} else {
				$_includePath= $_applicationRoot. 'class.' . $_class . '.php';			
			}
		}

	require_once $_includePath;
	
}

spl_autoload_register('classAutoLoader');
?>