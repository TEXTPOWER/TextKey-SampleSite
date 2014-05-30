 <?php

// Include the shared code
include_once('apiconfig.php');
include_once('textkeyshared.php');

// Class object for all TextKey Requests
class textKey {

	// Private values
	private $AuthKey;
	private $outputdetail;
	
	// Public values
	public $tk_state;

	// Handle setting up the default values
	public function __construct($APIKey = "") {

		// Set the API key
		$this->AuthKey = $APIKey;

		// Show the settings
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			$params = 'AuthKey: ' . $this->AuthKey . TK_NEWLINE;
			print_r2($params, "API Authentication Settings");
		}
	}
	
    public function sendAPIRequest($url, $postdata) {
		// Show the parameters
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			$textkey_params = json_decode($postdata);
			$textkey_params_elem = $textkey_params->DataIn;
			$tkResultsArr = get_object_vars($textkey_params_elem);
			foreach($tkResultsArr as $key => $value) { 
				$results .= $key . ': ' . $value . "<BR>";
			} 			
			print_r2($results, "API Call Parameters");
		};
		
		
		// Show the JSON
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($postdata, "API Call JSON");
		};

		// Handle the API request via CURL
		$curl = curl_init($url);

		// Enable tracking
		if (OUTPUT_STATE == OUTPUT_FULL) {
			curl_setopt($curl, CURLINFO_HEADER_OUT, true); 
		}
		
		// Set the CURL params and make sure it is a JSON request
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Wildcard certificate

		$response = curl_exec($curl);
		
		// Request headers
		if (OUTPUT_STATE == OUTPUT_FULL) {
			$headerSent = curl_getinfo($curl, CURLINFO_HEADER_OUT ); 
			print_r2($headerSent, "CURL Header Sent");
		}
		curl_close($curl);
		
		// Show the JSON
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2(format_json($response), "API Results JSON");
		}
		
		// Handle the payload
		$textkey_payload = json_decode($response);
		if ($textkey_payload->d) {
			$textkey_result = $textkey_payload->d;
		}
		else {
			$textkey_result = $textkey_payload;
		};

		if (DEBUGGING) {
			print_r2($textkey_payload, "TextKey Full Payload");
		}
		
		if (OUTPUT_STATE == OUTPUT_FULL) {
			print_r2($textkey_result, "TextKey Results");
		}

		// Handle the return object
		if (!($textkey_result)) {
			$textkey_result = new stdclass();
			$textkey_result->errorDescr = $error_msg;
		}

		return $textkey_result;
	}
	
	// Handle the GetTempAPI_Key request
    public function perform_GetTempAPI_Key($minutesDuration) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'getTempAPIKey';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'minutesDuration' => urlencode($minutesDuration)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the RegisterTextKeyUser request
    public function perform_registerTextKeyUser($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $userID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'registerTextKeyUser';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'command' => urlencode($Command),
				'cellNumber' => urlencode($CellNumber),
				'ownerFName' => urlencode($OwnerFName),
				'ownerLName' => urlencode($OwnerLName),
				'suppl1' => urlencode($Suppl1),
				'suppl2' => urlencode($Suppl2),
				'userID' => urlencode($userID),
				'isHashed' => urlencode($isHashed?"true":"false"),
				'pinCode' => urlencode($PinCode),				
				'distressPinCode' => urlencode($DistressPinCode),				
				'TextKeyMode' => urlencode($TextKeyMode),				
				'ReceiveMode' => urlencode($ReceiveMode)			
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the RegisterTextKeyUser request
    public function perform_registerTextKeyUserCSA($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $Ownerbirthdate, $Gender, $RegUserID, $isHashed, $PinCode, $DistressPinCode, $q1, $a1, $q2, $a2, $q3, $a3, $TextKeyMode, $ReceiveMode) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'registerTextKeyUserCSA';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'command' => urlencode($Command),
				'cellNumber' => urlencode($CellNumber),
				'ownerFName' => urlencode($OwnerFName),
				'ownerLName' => urlencode($OwnerLName),
				'ownerBirthDate' => urlencode($Ownerbirthdate),
				'ownerGender' => urlencode($Gender),
				'suppl1' => urlencode($Suppl1),
				'suppl2' => urlencode($Suppl2),
				'userID' => urlencode($RegUserID),
				'isHashed' => urlencode($isHashed?"true":"false"),
				'pinCode' => urlencode($PinCode),				
				'distressPinCode' => urlencode($DistressPinCode),				
				'q1' => urlencode($q1),				
				'a1' => urlencode($a1),				
				'q2' => urlencode($q2),				
				'a2' => urlencode($a2),				
				'q3' => urlencode($q3),				
				'a3' => urlencode($a3),				
				'TextKeyMode' => urlencode($TextKeyMode),				
				'ReceiveMode' => urlencode($ReceiveMode)			
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the GetTextKeyRegistration request
    public function perform_getTextKeyRegistration($RetrieveBy, $CellNumber, $Suppl1, $Suppl2) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'getTextKeyRegistration';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'retrieveBy' => urlencode($RetrieveBy),
				'cellNumber' => urlencode($CellNumber),
				'suppl1' => urlencode($Suppl1),
				'suppl2' => urlencode($Suppl2)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the issueTextKeyFromuserID request
    public function perform_IssueTextKeyFromuserID($userID, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'issueTextKeyFromUserId';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'userID' => urlencode($userID),
				'isHashed' => urlencode($isHashed?"true":"false")
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the doesRegistrationuserIDExist request
    public function perform_DoesRegistrationuserIDExist($userID, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'doesRegistrationuserIDExist';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'userID' => urlencode($userID),
				'isHashed' => urlencode($isHashed?"true":"false")
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the doesRegistrationCellNumberExist request
    public function perform_DoesRegistrationCellNumberExist($CellNumber) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'doesRegistrationCellNumberExist';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'cellNumber' => urlencode($CellNumber)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the createNewCellNumberProxy request
    public function perform_CreateNewCellNumberProxy($CellNumber) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'createNewCellNumberProxy';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'trueCellNumber' => urlencode($CellNumber)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the removeTempAPIKey request
    public function perform_RemoveTempAPIKey($tempapiKey, $MinutesDuration) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'removeTempAPIKey ';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($tempapiKey),
				'minutesDuration' => urlencode($MinutesDuration)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the IssueTextKeyFromCellNumber request
    public function perform_IssueTextKeyFromCellNumber($CellNumber) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'issueTextKeyFromCellNumber';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'cellNumber' => urlencode($CellNumber)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the PollForIncomingTextKey request
    public function perform_PollForIncomingTextKey($textkey) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'pollForIncomingTextKey';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'textKey' => urlencode($textkey)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the ValidateTextKeyFromuserID request
    public function perform_ValidateTextKeyFromuserID($userID, $textkey, $textkeyvc, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'validateTextKeyFromuserID';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'textKey' => urlencode($textkey),
				'userID' => urlencode($userID),
				'isHashed' => urlencode($isHashed?"true":"false"),
				'validationCode' => urlencode($textkeyvc)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the ValidateTextKeyFromCellNumber request
    public function perform_ValidateTextKeyFromCellNumber($CellNumber, $textkey, $textkeyvc) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'validateTextKeyFromCellNumber';
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_r2($url, "REST URL", "#D6FCFF");
		}

		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'textKey' => urlencode($textkey),
				'cellNumber' => urlencode($CellNumber),
				'validationCode' => urlencode($textkeyvc)
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}
}
?>