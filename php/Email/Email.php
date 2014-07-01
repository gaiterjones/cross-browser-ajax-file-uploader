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

	$_obj=new Email(array(
	  'to'  => TO,
	  'from' => FROM,
	  'subject' => SUBJECT,
	  'body' => BODY txt/html,
	  'cc' => CC,
	  'bcc' => BCC,
	  'attachments' => false / filename1,filename2,filename3 etc...
	));
	
*/

/**
 * Email class.
 -- a class to send MyMedazzaland email
 */
class Application_Email {


	protected $__;
	protected $_headers;
		
	public function __construct($_email) {
	
		$this->loadEmail($_email);
		$this->sendEmail();
	}
	
	protected function loadEmail($_variables)
	{
		foreach ($_variables as $key => $value)
		{
			$this->set($key,$value);
		}
	}


    protected function sendEmail()
    {
		
		$_cc=$this->get('cc');
		$_bcc=$this->get('bcc');
		$_body=$this->get('body');
		$_reference=$this->get('reference');
		
		// get filenames of attachments as array
		$_attachments=$this->get('attachments');
		
		$_fqdnHostname= $_SERVER['SERVER_NAME'];
		$_messageID="<" . sha1(microtime()) . "@" . $_fqdnHostname . ">";
		$this->addHeader('From: '.$this->get('from')."\r\n");
		if (!empty($_cc)) {$this->addHeader('CC: '.$this->get('cc')."\r\n");}
		if (!empty($_bcc)) {$this->addHeader('BCC: '.$this->get('bcc')."\r\n");}
        $this->addHeader('Reply-To: '.$this->get('from')."\r\n");
	    
		if ($_attachments)
		{
			// get mime encoded attachment data
			$_body=$this->mimeEncodeAttachments($_attachments);
			
		} else {
			// normal header
			$this->addHeader("Content-Type:text/html; charset=\"iso-8859-1\"\r\n");
		}
		
        $this->addHeader('Return-Path: '.$this->get('from')."\r\n");
		$this->addHeader("Message-ID: " .$_messageID. "\r\n");
		$this->addHeader('X-mailer: MyMedazzaland 1.0'."\r\n");
		
		if (mail($this->get('to'),$this->get('subject'),$_body,$this->_headers)) 
		{
			$this->set('emailsuccess',true);
		} else {
			$this->set('emailsuccess',false);
		}
        
    }
	
    protected function mimeEncodeAttachments($_attachments){
	
		$files = array($_attachments);

		$semi_rand = md5(time()); 
		$_mimeBoundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		
		// create multipart boundary 
		$_mimeEncode = "This is a multi-part message in MIME format.\n\n" . "--{$_mimeBoundary}\n" . "Content-Type:text/html; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $this->get('body') . "\n\n"; 
		$_mimeEncode .= "--{$_mimeBoundary}\n";
		
		// add headers for attachment 
		$this->addHeader("MIME-Version: 1.0\r\n" . "Content-Type: multipart/mixed;\r\n" . " boundary=\"{$_mimeBoundary}\"\r\n"); 
		
		 // prepare attachments
		for($x=0;$x<count($files);$x++){
			$file = fopen($files[$x],"rb");
			$data = fread($file,filesize($files[$x]));
			fclose($file);
			$data = chunk_split(base64_encode($data));
			$_mimeEncode .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . 
			"Content-Disposition: attachment;\n" . " filename=\"$files[$x]\"\n" . 
			"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
			$_mimeEncode .= "--{$_mimeBoundary}\n";
		} 

		return $_mimeEncode;
    }	

    protected function addHeader($header){
        $this->_headers .= $header;
    }
    
	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}
		
  	public function get($variable)
	{
		return $this->__[$variable];
	}

}
?>