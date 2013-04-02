<?php
/**
 *  
 *  Copyright (C) 2013 paj@gaiterjones.com
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

//
// File upload class using XML HTTP Requests and AJAX
//
class FileUploadAjaxXHR{

	protected $__;
	protected $__config;	

	public function __construct($_variables) {

		$this->loadConfig();

		$this->loadClassVariables($_variables);
		
		$this->fileUploadAjaxXHR();

	}

	// XMLHttpRequest upload via Ajax
	protected function fileUploadAjaxXHR()
	{
		$this->set('success', false);
		
		$_uploadFileName=$this->get('uploadfilename'); // get filename from ajax get variables
		
		$_targetFile=$this->targetFile($_uploadFileName); // get new target filename
		
		// validate file types
		$_fileTypes = $this->get('alloweduploadfiletypes');
		$_fileParts = pathinfo($_uploadFileName);
		
		if (in_array(strtolower($_fileParts['extension']),$_fileTypes)) { // valid filetype

			// file validated, get file from RAW post data
			file_put_contents(
				$_targetFile,
				file_get_contents('php://input')
			);			
			
			$_imageResize=$this->get('uploadresize');
			
			if ($_imageResize != "0x0") // if imagesize request set, then resize image
			{
				$_widthXheight=explode('x',$_imageResize);
				
				$_width=$_widthXheight[0];
				$_height=$_widthXheight[1];
					
					// resize
					$_obj = new imageLib($_targetFile);
					$_obj -> resizeImage($_width, $_height, 'auto');
					$_obj -> saveImage($_targetFile, 100);
					unset($_obj);
			}
			
			$_outputMessage='File was uploaded successfully.';

			$this->set('success', true);
			
			$_output=array(
					  "output" 			=>  $_outputMessage,
					  "filecacheuri" 	=>  $this->__config->get('fileCacheURI'),
					  "filename"		=>	basename($_targetFile)
					);
			
		} else {
		
			throw new Exception('Upload '. $_uploadFileName. ' rejected - invalid file type. Supported image files are'). ' - '. $this->__config->get('allowedUploadFileTypes');
		}
	
		$this->set('output', $_output);
	}
		
	// 
	// create a new filename for the upload
	//
	private function targetFile($_fileName)
	{
		$_uploadFileName=$this->get('uploadfilename');
		
		$_targetFilePrefix=$this->get('uploadfilenameprefix'); // prefix sent with upload
		
		$_targetFileExtension= pathinfo($_fileName, PATHINFO_EXTENSION);
		
		$_targetPath=$this->get('uploadfilecache');
		
		$_targetFile=$_targetFilePrefix. '-'. time(). '-'. $_uploadFileName. '.'. $_targetFileExtension;
		
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
	
	}

	private function loadClassVariables($_variables)
	{
		foreach ($_variables as $_variableName=>$_variableData)
		{
			if (empty($_variableData)) {throw new exception('Class variable '. $_variableName. ' cannot be empty.');}
			
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


