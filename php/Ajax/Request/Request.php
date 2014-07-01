<?php
/**
 *  Generic Class for processing AJAX requests and returning data
 
	v 2.0
	
 *  Copyright (C) 2013 paj@gaiterjones.com
 *
 * 	
 *
 	usage index.php?ajex=true&class=className&variables=variable1=data1|variables2=data2
 */
 
/**
 * Application_Ajax_Request class
 */
 
class Application_Ajax_Request {

	protected $__;			// variable class object
	protected $__config;	// configuration class object
	protected $__t; 		// translation class object

	// -- constructor
	//
	public function __construct($_class) {

		$this->loadConfig();
		$this->loadTranslator();
		$this->processAjaxRequest($_class);
	}
	
	// -- Ajax request processor
	//
	private function processAjaxRequest($_class)
	{
		
			// function variables
			$_responseArray = array();
			$_loggedIn=$this->get('loggedin');
			$_userId=$this->get('userid');
			$_sessionKey=$this->get('sessionkey');
			$_output=''; // ajax output
			$_return=''; // class return string
			$_classVariableArray=array();
			$_authenticationExcludeClasses=array('Application_File_Upload','Application_Security_Login','Application_Security_Password_Reset','Application_DB_RegisterUser');
			
				
			try // to work
			{
				// validate session data:
				//
				// -- validate logged in user (exclude classes that do not require user to be logged in)
				//
				if (!$_loggedIn && !in_array($_class, $_authenticationExcludeClasses)) {throw new exception($this->__t->__('Ajax request not authenticated, not logged in.'));}
				
				// -- validate session key (exclude classes that do not require user to be logged in)
				//
				if ($_sessionKey != md5($_userId) && !in_array($_class, $_authenticationExcludeClasses)) {throw new exception($this->__t->__('Invalid session key.'));}
				
				// -- File Uploads HTML5 and uploadify flash plugin
				//
				if(array_key_exists('Filedata',$_FILES) && $_FILES['Filedata']['error'] == 0 ){
					
					$_tempFileName=$_FILES['Filedata']['tmp_name'];
					$_origFileName=$_FILES['Filedata']['name'];
					
					$_classVariableArray['tempfilename']= $_tempFileName;
					$_classVariableArray['origfilename']= $_origFileName;
					
					$_classVariableArray['xhrajaxupload']= false;
					
					if (isset($_GET['variables'])) {
						
						foreach ($_GET as $_key=>$_variableData)
						{
							$_classVariableArray[$_key]=$_variableData;
						}
					}					

				// -- File Uploads via AJAX XHR Ajax
				//
				} elseif (isset($_SERVER['HTTP_X_FILENAME'])) { // filename 
				
					$_classVariableArray['uploadfilename']= $_SERVER['HTTP_X_FILENAME']; // used for generic FileUploadXHR class
					$_classVariableArray['tempfilename']= $this->__config->get('uploadFileCache').time().'-'.$_SERVER['HTTP_X_FILENAME'];
					$_classVariableArray['origfilename']= $_SERVER['HTTP_X_FILENAME'];
					$_classVariableArray['xhrajaxupload']= true;
					
					if (isset($_GET['variables'])) {
						
						foreach ($_GET as $_key=>$_variableData)
						{
							$_classVariableArray[$_key]=$_variableData;
						}
					}
					
				// validate class variables from POST or GET data
				// if POST variable array set then data is contained
				// in POST
				//
				} elseif (isset($_POST['variables'])) { // if post contains variables
				
				 	foreach ($_POST as $_key=>$_variableData) // extract post variables to associative array
				 	{
					 	$_classVariableArray[$_key]=$_variableData;	
				 	}
					

				// -- No POST[variables] use GET
				//
				} elseif (isset($_GET['variables'])) {
				
					$_variables = $_GET['variables'];
					
					// -- extract variable data into array
					$_classVariables=explode('|',$_variables);
					
					
					foreach ($_classVariables as $_key=>$_variableData) // create new associative variable array
					{
						
						if (empty($_variableData)) { throw new exception($this->__t->__('Ajax class variable contains no data.'));}
						
						$_variableDataArray=explode('=',$_variableData);
						
						if (!isset($_variableDataArray[0])){ throw new exception($this->__t->__('Ajax class variable incorrectly formatted.'));}
						if (!isset($_variableDataArray[1])){ throw new exception($this->__t->__('Ajax class variable incorrectly formatted.'));}
						
						// -- create class associative variable array
						$_classVariableArray[$_variableDataArray[0]]=$_variableDataArray[1];
					
					}					
					
					
				} else { // -- no variable data found in POST or GET
					throw new exception('Ajax class variables or data not found');
				}
				
				// -- add default variables to class variables associative array
				// set user id for all logged in session requests
				//
				if (!in_array($_class, $_authenticationExcludeClasses)) { $_classVariableArray['userid']= $_userId; }
				
				
				// -- call class with associative variable array from ajax request
				//
				$_obj=new $_class($_classVariableArray);
				
				// -- set success flag
				$_objSuccess=$_obj->get('success');

				// -- process failure - exception
				if (!$_objSuccess){ throw new exception($_obj->get('errormessage')); }
					
				// -- process success - send output
				$_output=$_obj->get('output'); // class output data
				
				if (is_array($_output)) // output might be an array
				{
					foreach ($_output as $_outputKey=>$_outputData)
					{
						$_responseArray[$_outputKey] =  $_outputData;
					}
					
				} else {
				
					$_responseArray['output'] =  $_output;
				}
				
				// -- set success response object
				//
				$_responseArray['status'] = 'success';
				
				// -- clean up
				unset($_obj);
				
				// -- create json encoded response and set return variable for __toString
				//
				$_ajaxRequestReturn=json_encode($_responseArray);				
					$this->set('return',$_ajaxRequestReturn);
				
			}
			
			catch (Exception $e)
		    {
			    // -- return status as error
				$_responseArray['status'] = 'error';
				// -- return exception error
			    $_responseArray['output'] = $e->getMessage();
				
				$_ajaxMessage=$e->getMessage();
				// -- include friendly(er) message saved in session for ajax requests
				if (isset($_SESSION['errormessage']) && $_SESSION['errormessage'] != 'undefined') { $_ajaxMessage=$_SESSION['errormessage'];}
				
				$_responseArray['errormessage'] = $_ajaxMessage;
				
				// -- create json encoded response and set return variable for __toString
				//
				$_ajaxRequestReturn=json_encode($_responseArray);
					$this->set('return',$_ajaxRequestReturn);
		    
		    }
	}

	// -- load config
	//
	private function loadConfig()
	{
		$this->__config= new config();
	}
	

	// -- load translator
	private function loadTranslator()
	{
		$_languageCode=$this->get('languagecode');
		if (empty($_languageCode)) { $_languageCode='en';}
		$this->__t=new Application_Translator($_languageCode);	
	}	


	// -- ajax output
	//
	public function __toString()
	{
		$_return=$this->get('return');
				return $_return;
	}
	
	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}
		
  	public function get($variable)
	{
		return $this->__[$variable];
	}


	// -- class destructor - POW!
	//
	public function __destruct()
	{
	       unset($this->__config);
		   unset($this->__security);
	       unset($this->__);
	       unset($this->__t);
	}

}