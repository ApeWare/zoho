<?php

class Zoho{
	//	
	// 	Class Vars
	//	-------------------------------------------------------------------------------------
		public $username = "";
		public $password = "";
    
    /**	
     * 	getAuthToken
     *	
     *	Generate Zoho API auth token for a given, valid username and password
     *
     *	@return string authtoken or error
     *	---------------------------------------------------------------------------------------------*/    
	    public function getAuthToken() {
			if(!empty($this->username) && !empty($this->password))
			{
				$param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$this->username."&PASSWORD=".$this->password;
				$ch = curl_init("https://accounts.zoho.com/apiauthtoken/nb/create");
				curl_setopt($ch, CURLOPT_POST, true);			// set method to POST
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// set to return data to string ($result)
				curl_setopt($ch, CURLOPT_TIMEOUT, 30); 			// set timeout to 30s
				curl_setopt($ch, CURLOPT_POSTFIELDS, $param); 	// pass $param as post fields
				$result = curl_exec($ch);						// execute curl
				 
				//
				//	Extract auth token from $result
				//	-------------------------------------------------------------------------------------
				$resultArray 	= explode("\n",$result); 						// curl result array
				
				$success = explode("=",$resultArray['3']); 						// [3] => RESULT=TRUE
				// Error Checking
					if($success['1'] == "TRUE")
					{
						$authToken 		= explode("=",$resultArray['2']); 		// [2] => AUTHTOKEN=f960d61c0f43292cb274d773c24bc679
						$compare 		= strcmp($authToken['0'],"AUTHTOKEN"); 	// verify node[2] returned is 'AUTHTOKEN'
						if ($compare == 0)
						{
							return $authToken['1'];
						}
					}
					else
					{
						$errorArray = explode("=",$resultArray['2']); //[2] => CAUSE=INVALID_PASSWORD
						return "ERROR in method " . __METHOD__. ": " . $errorArray['1'] . "!<br>";
					}
					curl_close($ch);
				
			}
			elseif(empty($this->username))
			{
				return "ERROR in method " . __METHOD__. ": Required class var `username` is empty!";
			}
			elseif(empty($this->password))
			{
				return "ERROR in method " . __METHOD__. ": Required class var `password` is empty!";
			}
			else
			{
				return "ERROR in method " . __METHOD__. ":  ¯\(°_o)/¯";
			}
	    }   

	/**	
	 * 	postData
	 *
	 *	posts form data to Zoho. Can optionally post form as a Contact or Lead
	 *
	 *	@param	string 	$auth	auth token for Zoho account
	 *	@param	array	$data	array containing form data (fName, lName, email, company, phone, leadOwner)
	 *	@param	string	$create	[default "Leads", "Contacts"] 
	 *	---------------------------------------------------------------------------------------------*/
	    public function postData($auth, $data, $create='Leads')
	    {
	    	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			        <$create>
			        <row no=\"1\">
				        <FL val=\"First Name\">" . 	$data['fName'] 		. "</FL>
				        <FL val=\"Last Name\">" . 	$data['lName'] 		. "</FL>
				        <FL val=\"Email\">" . 		$data['email'] 		. "</FL>
				        <FL val=\"Company\">" . 	$data['company'] 	. "</FL>
				        <FL val=\"Phone\">" . 		$data['phone'] 		. "</FL>
				        <FL val=\"Lead Owner\">" . $data['leadOwner'] 	. "</FL>
			        </row>
			        </$create>";
			        
			$url = "https://crm.zoho.com/crm/private/xml/$create/insertRecords";
			       
	    	$query = "authtoken=$auth&scope=crmapi&newFormat=1&xmlData=$xml";
	    	
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url); 			// URL for post request
		    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // allow redirects
		    curl_setopt($ch, CURLOPT_POST, true);			// set method to POST
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response as a string ($response)
		    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 			// set timeout to 30s
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);	// set POST field parameters
		    $response = curl_exec($ch);						// execute curl
		    curl_close($ch);
		   // echo'<pre style=\'text-align:left;\'>';print($response);echo'</pre>'; // DEV: Output for dev
		   return $response;
		}
} // End class Zoho


/**	**************************************************************************************************
 * 	Example Code
 *
 *	@example
 *	<code>
 *		$zoho = new Zoho();
 *		$auth = "83645ec4555bb2h67d79f360961b8cf46";
 *		$result = $zoho->postData($auth, $_POST);
 *	</code>
 *	**************************************************************************************************/
 	$zoho = new Zoho(); // Instantiate class Zoho
   
   	//	-------------------------------------------------------------------------------------
   	// 	Generate an AUTH TOKEN for the user's account
   	//	-------------------------------------------------------------------------------------
   		// set the class vars with the Zoho account credentials
	   		$zoho->username = "<Zoho Username>";
	   		$zoho->password = "<Zoho Password>";
	   	
	   	// get the returned AUTH TOKEN
	   		$auth = $zoho->getAuthToken();
	   		
	   	//	Now at this point we will output the token for developer. This token should be 
	   	//  set to a variable and should not need ot be generated again, unless the user
	   	// 	screws up their account or something.
	   	//	So once it is generated you should add it to the code like this
	   	// $auth = "83645ec4555bb2h67d79f360961b8cf46";
    
	   	echo'<pre style=\'text-align:left;\'>Created Token :';print($auth);echo'</pre>';
    
    //	-------------------------------------------------------------------------------------
    // 	Post form data to Zoho
    //	-------------------------------------------------------------------------------------
	    // Normally $_POST would contain your form field data. I am just recreating that test form data here
	    $_POST = array(	"fName"=>"John",
						"lName"=>"Doe",
						"email"=>"jdoe@domain.com",
						"company"=>"Some Company",
						"phone"=>"407-555-1212",
						"leadOwner"=>"Sally Smith",
					);
		$user = $_POST;
			
		$result = $zoho->postData($auth, $user, 'Leads');
			
		// 
		//	convert XML response to an array for DEV output
		//	
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$array = json_decode($json,TRUE);
			echo'<pre style=\'text-align:left;\'>RESPONSE: ';print_r($array);echo'</pre>';
    
?>
