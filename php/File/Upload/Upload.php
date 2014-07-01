<?php
/**
 *  
 *  Copyright (C) 2014 paj@gaiterjones.com
 *
 *	This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 *
 */

 
class Application_File_Upload {

	protected $__;
	protected $__config;
	protected $__t; 

	public function __construct($_variables) {

		$this->loadConfig();
		$this->loadClassVariables($_variables);
		$this->loadTranslator();		
		$this->fileUpload();

	}

	
	protected function fileUpload()
	{
		// set defaults
		$_output='';
		$_xhrAjaxUpload=false;
		$this->set('success', false);
		
		// get class variables
		$_xhrAjaxUpload=$this->get('xhrajaxupload');
		$_tempFileName=$this->get('tempfilename');
		$_origFileName=$this->get('origfilename');
		
		try
		{
			if ($_xhrAjaxUpload) // get the file from xhr upload
			{
					// file from RAW post data
					file_put_contents(
						$_tempFileName,
						file_get_contents('php://input')
					);
				
			}
		}
		catch (Exception $e)
	    {
		    throw new Exception('Upload error - '. $e->getMessage());

	    }		
		
		$_targetFile=$this->targetFile($_origFileName); // get target filename
		
		// validate file types
		$_fileTypes = $this->get('alloweduploadfiletypes');
		$_fileParts = pathinfo($_origFileName);
		
		if (in_array(strtolower($_fileParts['extension']),$_fileTypes)) { // valid filetype

			// we have three upload methods
			// flash, xhr file drop and xhr upload
			// flash and filedrop store the files in post
			// xhr generic upload uses raw data in post
			// we use move for the first two and rename for the latter
			if (!$_xhrAjaxUpload) { move_uploaded_file($_tempFileName,$_targetFile); } // save file
			if ($_xhrAjaxUpload) { rename($_tempFileName,$_targetFile);} //rename file

			$_output='File was uploaded successfully.';
			
			$_imageResize=$this->get('uploadresize');
			
			if ($_imageResize != "0x0") // if imagesize request set, then resize image
			{
				$_widthXheight=explode('x',$_imageResize);
				
				$_width=$_widthXheight[0];
				$_height=$_widthXheight[1];
					
					// resize
					$_obj = new Application_Image_Lib($_targetFile);
					$_obj -> resizeImage($_width, $_height, 'auto');
					$_obj -> saveImage($_targetFile, 100);
					unset($_obj);
			}
			
			// send email with attachment
			$_emailEnabled=$this->__config->get('emailEnabled');
			if ($_emailEnabled)
			{
				$_fileType=$this->get('filetype');
				
				$_obj=new Application_Email(array(
				  'to'  => $this->__config->get('emailTo'),
				  'from' => $this->__config->get('emailFrom'),
				  'subject' => 'Uploaded '. $_fileType. ' Attached',
				  'body' => 'Uploaded '. $_fileType. ' Attached',
				  'cc' => '',
				  'bcc' => '',
				  'reference' => '',
				  'attachments' => $_targetFile
				));
				unset ($_obj);
			}	
			
			$this->set('success', true);
			$this->set('filename', basename($_targetFile));
			
			$_output=array(
					  "output" 		=>  $_output,
					  "filename"	=>	basename($_targetFile)
					);
			
		} else {
		
			unlink($_tempFileName);
			throw new Exception($this->__t->__('Invalid file type. Allowed files are'). ' - '. $this->__config->get('allowedUploadFileTypes'));
		}
	
		$this->set('output', $_output);
	}
		
		
	// 
	// create a new filename for the upload
	//
	private function targetFile($_fileName)
	{
		$_uploadFileName=$this->get('origfilename');
		
		$_targetFilePrefix=$this->get('uploadfilenameprefix'); // prefix sent with upload
		
		$_targetPath=$this->get('uploadfilecache');
		
		$_targetFile=$_targetFilePrefix. '-'. time(). '-'. $_uploadFileName;
		
		$_targetFile = rtrim($_targetPath,'/') . '/' . $_targetFile;
		
		return $_targetFile;
	}
	
	public function __destruct()
	{

	}

	private function loadConfig()
	{
		$this->__config= new config();
		
		$_uploadFileCache=$this->__config->get('uploadFileCache');
		$this->set('uploadfilecache',$_uploadFileCache);
		
		$_allowedUploadFileTypes=explode(',',$this->__config->get('allowedUploadFileTypes'));
		$this->set('alloweduploadfiletypes',$_allowedUploadFileTypes);
		
		// default filetype
		$this->set('filetype','file');
		// default language
		$this->set('languagecode','en');
		
	
	}
	
	private function loadTranslator()
	{
		// load app translator			
		$_languageCode=$this->get('languagecode');
		if (empty($_languageCode)) { $_languageCode='en';}
		$this->__t=new Application_Translator($_languageCode);
	}	

	private function loadClassVariables($_variables)
	{
		foreach ($_variables as $_variableName=>$_variableData)
		{
			if (!isset($_variableData)) {throw new exception(get_class($this). ' class variable '. $_variableName. ' cannot be empty.');}
			
			$this->set($_variableName,$_variableData);
						
		}
	}	

	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}
		
  	public function get($variable)
	{
		if (!isset($this->__[$variable])) {throw new exception('The requested class variable "'. $variable. '" does not exist.');}
		
		return $this->__[$variable];
	}
}