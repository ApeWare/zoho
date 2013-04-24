# Zoho
Provides methods of posting form data to Zoho CRM as a Client or Lead

##Usage

### Generating Auth Token
  require_once ("zoho.php");
	$zoho = new Zoho();
	
	// set the class vars with the Zoho account credentials
	   	$zoho->username = "<Zoho Username>";
	   	$zoho->password = "<Zoho Password>";
	   	
	// get the returned AUTH TOKEN
	   		$authToken = $zoho->getAuthToken();
	   		
	// do something with $authToken; echo or pass to another method
	
### Post Form Data to Zoho CRM
	require_once ("zoho.php");
	$zoho = new Zoho();
	
	$authToken = "<authToken>";
	/*	This is the expected form $_POST. Can be fewer fields if required. 
		Edit the $xml var in the postData method to add more fields
		
		$_POST = array(		"fName"		=>"John",
							"lName"		=>"Doe",
							"email"		=>"jdoe@domain.com",
							"company"	=>"Some Company",
							"phone"		=>"407-555-1212",
							"leadOwner"	=>"Sally Smith",
						);
	*/
	$xmlResult = $zoho->postData($authToken, $_POST, 'Leads');
	
	// Do something with $xmlResult. Profit!
