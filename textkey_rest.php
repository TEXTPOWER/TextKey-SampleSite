 <?php

// TextKey REST path
define('TK_REST', 'https://secure.textkey.com/REST/TKRest.asmx/');

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

	}
	
	public function sendAPIRequest($url, $postdata) {

		// Handle the API request via CURL
		$curl = curl_init($url);

		// Set the CURL params and make sure it is a JSON request
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);  // Wildcard certificate
		
		$response = curl_exec($curl);
		
		curl_close($curl);
		
		// Handle the payload
		$textkey_payload = json_decode($response);
		if ($textkey_payload->d) {
			$textkey_result = $textkey_payload->d;
		}
		else {
			$textkey_result = $textkey_payload;
		};

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
    public function perform_registerTextKeyUser($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $UserID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'registerTextKeyUser';

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
				'userID' => urlencode($UserID),
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

	// Handle the issueTextKeyFromUserId request
    public function perform_IssueTextKeyFromUserId($UserID, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'issueTextKeyFromUserId';
		
		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'userID' => urlencode($UserID),
				'isHashed' => urlencode($isHashed?"true":"false")
			)
		),
		JSON_PRETTY_PRINT);
		
		// Handle the API Call
		return $this->sendAPIRequest($url, $postdata);
	}

	// Handle the doesRegistrationUserIdExist request
    public function perform_DoesRegistrationUserIDExist($UserID, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'doesRegistrationUserIdExist';
		
		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'userID' => urlencode($UserID),
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

	// Handle the ValidateTextKeyFromUserId request
    public function perform_ValidateTextKeyFromUserId($UserID, $textkey, $textkeyvc, $isHashed) {
		// Setup
		$error_msg = "";

		// Set API Key
		$apikey = $this->AuthKey;
	
		// Build the REST API URL
		$url = TK_REST . 'validateTextKeyFromUserId';
		
		// Setup data
		$postdata = json_encode(
		array('DataIn' => 
			array(
				'apiKey' => urlencode($apikey),
				'textKey' => urlencode($textkey),
				'userID' => urlencode($UserID),
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