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
 
 	usage
	cross browser ajax file uploader using html5 and flash
 	
	
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 */

/* Main application wrapper class */
class Application
{
	
	protected $__;
	protected $__config;
	
	public function __construct() {
		
		try
		{
			$this->set('errorMessage','');
			$this->loadConfig();
			$this->getExternalVariables();				
		
		}
		catch (Exception $e)
	    {
		    echo $e->getMessage();
	    	exit;
	    }
	}

	private function loadConfig()
	{
		$this->__config= new config();
	
	}

	private function getExternalVariables()
	{

		if(isset($_GET['ajax'])){ $_ajaxRequest = true;} else { $_ajaxRequest = false;}								// ajax requests boolean
		if(isset($_GET['class'])){ $_ajaxClass = $_GET['class'];} else { $_ajaxClass = false;}						// ajax request class


		// process ajax requests
		if ($_ajaxRequest)
		{
			if ($_ajaxClass) { // ajax class set

				$_ajaxRequest=new Application_Ajax_Request($_ajaxClass);
				header('Content-type: text/json');
					echo $_ajaxRequest;
						unset($_ajaxRequest);
							exit;
			}
			// invalid ajax class
			exit;
		}
	
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