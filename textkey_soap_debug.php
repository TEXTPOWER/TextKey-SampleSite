<?php

// Include the shared code
include_once('apiconfig.php');
include_once('textkeyshared.php');

// Overide the standard class to massage the SOAP request into a .net compliant request.
class MSSoapClient extends SoapClient
{

	// Show the SOAP call settings for debugging
    function __soapCall($function, $arguments, $options = array(), $input_headers = null, &$output_headers = null) {

		// Show the settings
		if (DEBUGGING) {
			print_r2($arguments, "API SOAP Request - arguments: " . $function);
			print_r2($options, "API SOAP Request - options");
			print_r2($input_headers, "API SOAP Request - input_headers");
			print_r2($output_headers, "API SOAP Request - output_headers");
		};

        return parent::__soapCall($function, $arguments, $options, $input_headers, $output_headers);
	}
	
	// Massage the standard PHP SOAP request into the correct MS SOAP format
    function __doRequest($request, $location, $action, $version) {

		// Setup
        $namespace = TK_NS;
		
		// Convert SOAP to correct .net format
		$request = preg_replace('/<ns1:(\w+)/', '<$1 xmlns="'.$namespace.'"', $request, 1);
		$request = preg_replace('/<ns1:(\w+)/', '<$1', $request);
		$request = str_replace(array('/ns1:', 'xmlns:ns1="'.$namespace.'"'), array('/', ''), $request);
		$request = preg_replace('/SOAP-ENV/', 'soap', $request);
		$request = preg_replace('/<TextKeys/', '<TextKeys xmlns="'.$namespace.'"', $request, 1);
		
		// Handle call specific items
		$request = str_replace('<removeTempAPIKey_Key>', '<removeTempAPIKey_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<removeTempAPIKey>', '<removeTempAPIKey xmlns="'.$namespace.'">', $request);
		$request = str_replace('<createNewCellNumberProxy_Key>', '<createNewCellNumberProxy_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<createNewCellNumberProxy>', '<createNewCellNumberProxy xmlns="'.$namespace.'">', $request);
		$request = str_replace('<getTextKeyRegistration_Key>', '<getTextKeyRegistration_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<getTextKeyRegistration>', '<getTextKeyRegistration xmlns="'.$namespace.'">', $request);
		$request = str_replace('<registerTextKeyUser_Key>', '<registerTextKeyUser_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<registerTextKeyUser>', '<registerTextKeyUser xmlns="'.$namespace.'">', $request);
		$request = str_replace('<registerTextKeyUserCSA_Key>', '<registerTextKeyUserCSA_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<registerTextKeyUserCSA>', '<registerTextKeyUserCSA xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKeyFromUserId_Key>', '<issueTextKeyFromUserId_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKeyFromUserId>', '<issueTextKeyFromUserId xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKeyFromCellNumber_Key>', '<issueTextKeyFromCellNumber_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKeyFromCellNumber>', '<issueTextKeyFromCellNumber xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKey_Key>', '<issueTextKey_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<issueTextKey>', '<issueTextKey xmlns="'.$namespace.'">', $request);
		$request = str_replace('<validateTextKeyFromUserId_Key>', '<validateTextKeyFromUserId_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<validateTextKeyFromUserId>', '<validateTextKeyFromUserId xmlns="'.$namespace.'">', $request);
		$request = str_replace('<validateTextKeyFromCellNumber_Key>', '<validateTextKeyFromCellNumber_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<validateTextKeyFromCellNumber>', '<validateTextKeyFromCellNumber xmlns="'.$namespace.'">', $request);
		$request = str_replace('<getTempAPIKey>', '<getTempAPIKey xmlns="'.$namespace.'">', $request);
		$request = str_replace('<getTempAPIKey_Key>', '<getTempAPIKey_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<doesRegistrationUserIDExist_Key>', '<doesRegistrationUserIDExist xmlns="'.$namespace.'">', $request);
		$request = str_replace('<doesRegistrationUserIDExist>', '<doesRegistrationUserIDExist_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<doesRegistrationCellNumberExist_Key>', '<doesRegistrationCellNumberExist xmlns="'.$namespace.'">', $request);
		$request = str_replace('<doesRegistrationCellNumberExist>', '<doesRegistrationCellNumberExist_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<pollForIncomingTextKey_Key>', '<pollForIncomingTextKey_Key xmlns="'.$namespace.'">', $request);
		$request = str_replace('<pollForIncomingTextKey>', '<pollForIncomingTextKey xmlns="'.$namespace.'">', $request);

		// Show the settings
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			print_xml($request, "API SOAP Request - Length: " . strlen($request));
		};
				
        return parent::__doRequest($request, $location, $action, $version);
    }
}

// Build the elements for the SOAP header

// Setup the textkey header
class textKeyHeader {
	function __construct($uid, $pwd, $campaign, $keyword) {
		$this->UID = $uid;
		$this->PWD = $pwd;
		$this->Campaign = $campaign;
		$this->Keyword = $keyword;
	}
}

// Setup the doesRegistrationUserIDExist_Key API call
class textKeyDoesRegistrationUserIDExistBody {
	function __construct($UserID, $isHashed, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->userID = $UserID;
		$this->isHashed = $isHashed;
	}
}

// Setup the DoesRegistrationCellNumberExist_Key API call
class textKeyDoesRegistrationCellNumberExistBody {
	function __construct($CellNumber, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->cellNumber = $CellNumber;
	}
}

// Setup the ValidateTextKeyFromUserId/ValidateTextKeyFromUserId_Key API call
class textKeyValidateTextKeyFromUserIdBody {
	function __construct($UserID, $TextKey, $ValidationCode, $isHashed, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->textKey = $TextKey;
		$this->userID = $UserID;
		$this->isHashed = $isHashed;
		$this->validationCode = $ValidationCode;
	}
}

// Setup the ValidateTextKeyFromCellNumber/ValidateTextKeyFromCellNumber_Key API call
class textKeyValidateTextKeyFromCellNumberBody {
	function __construct($CellNumber, $TextKey, $ValidationCode, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->textKey = $TextKey;
		$this->CellNumber = $CellNumber;
		$this->validationCode = $ValidationCode;
	}
}

// Setup the IssueTextKeyFromUserId/IssueTextKeyFromUserId_Key API call
class textKeyIssueTextKeyFromUserIdBody {
	function __construct($UserID, $isHashed, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->userID = $UserID;
		$this->isHashed = $isHashed;
	}
}

// Setup the IssueTextKeyFromCellNumber/IssueTextKeyFromCellNumber_Key API call
class textKeyIssueTextKeyFromCellNumberBody {
	function __construct($CellNumber, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->cellNumber = $CellNumber;
	}
}

// Setup the CreateNewCellNumberProxy API call
class textKeyCreateNewCellNumberProxyBody {
	function __construct($CellNumber, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->trueCellNumber = $CellNumber;
	}
}

// Setup the RemoveTempAPIKey API call
class textKeyRemoveTempAPIKey {
	function __construct($MinutesDuration, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->minutesDuration = $MinutesDuration;
	}
}

// Setup the PollForIncomingTextKey/PollForIncomingTextKey_Key API call
class textKeyPollForIncomingTextKeyBody {
	function __construct($TextKey, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->textKey = $TextKey;
	}
}

// Setup the GetTempAPI_Key API call
class textKeyGetTempAPIKey_KeyBody {
	function __construct($MinutesDuration, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->minutesDuration = $MinutesDuration;
	}
}

// Setup the RegisterTextKeyUser/RegisterTextKeyUser_Key API call
class textKeyRegisterUserBody {
	function __construct($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $UserID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->command = $Command;
		$this->cellNumber = $CellNumber;
		$this->ownerFName = $OwnerFName;
		$this->ownerLName = $OwnerLName;
		$this->suppl1 = $Suppl1;
		$this->suppl2 = $Suppl2;
		$this->userID = $UserID;
		$this->isHashed = $isHashed;
		$this->pinCode = $PinCode;
		$this->distressPinCode = $DistressPinCode;
		$this->TextKeyMode = $TextKeyMode;
		$this->ReceiveMode = $ReceiveMode;
	}
}

// Setup the RegisterTextKeyUserCSA/RegisterTextKeyUserCSA_Key API call
class textKeyRegisterUserCSABody {
	function __construct($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $Ownerbirthdate, $OwnerGender, $RegUserID, $isHashed, $PinCode, $DistressPinCode, $q1, $a1, $q2, $a2, $q3, $a3, $TextKeyMode, $ReceiveMode, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->command = $Command;
		$this->cellNumber = $CellNumber;
		$this->ownerFName = $OwnerFName;
		$this->ownerLName = $OwnerLName;
		$this->suppl1 = $Suppl1;
		$this->suppl2 = $Suppl2;
		$this->userID = $RegUserID;
		$this->isHashed = $isHashed;
		$this->pinCode = $PinCode;
		$this->distressPinCode = $DistressPinCode;
		$this->ownerBirthDate = $OwnerBirthDate;
		$this->ownerGender = $OwnerGender;
		$this->q1 = $q1;
		$this->a1 = $a1;
		$this->q2 = $q2;
		$this->a2 = $a2;
		$this->q3 = $q3;
		$this->a3 = $a3;
		$this->TextKeyMode = $TextKeyMode;
		$this->ReceiveMode = $ReceiveMode;
	}
}

// Setup the getTextKeyRegistration API call
class getTextKeyRegistrationBody {
	function __construct($RetrieveBy, $CellNumber, $Suppl1, $Suppl2, $APIKey = "") {
		if ($APIKey != "") {
			$this->apiKey = $APIKey;
		};
		$this->retrieveBy = $RetrieveBy;
		$this->cellNumber = $CellNumber;
		$this->suppl1 = $Suppl1;
		$this->suppl2 = $Suppl2;
	}
}

// Class object for all TextKey Requests
class textKey {

	// Private values
	private $textKeyHeader;
	private $AuthKey;
	private $outputdetail;
	
	// Public values
	public $tk_state;

	// Handle setting up the SOAP header which will be used for all SOAP requests
	public function __construct($uid, $pwd, $campaign, $keyword, $APIKey = "") {

		// Check to see if using the API key or building the textkey header
		if ($APIKey != "") {
			$this->AuthKey = $APIKey;
		}
		else {
			$this->textKeyHeader = new textKeyHeader($uid, $pwd, $campaign, $keyword);
		};
		
		// Show the settings
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			$params = 'AuthKey: ' . $this->AuthKey . TK_NEWLINE;
			$params .='UID: ' . $this->textKeyHeader->UID . TK_NEWLINE;
			$params .='PWD: ' . $this->textKeyHeader->PWD . TK_NEWLINE;
			$params .='Campaign: ' . $this->textKeyHeader->Campaign . TK_NEWLINE;
			$params .='Keyword: ' . $this->textKeyHeader->Keyword . TK_NEWLINE;
			print_r2($params, "API Authentication Settings");
		}
	}
	
	// Handle the SOAP Request
    public function perform_SOAP_Request($tkCommandBase, $tkBody) {
		// Setup
		$error_msg = "";

		if (DEBUGGING) {
			print_r2(TK_WSDL, "TK_WSDL");
			print_r2(TK_NS, "TK_NS");
		}
		
		// Create the SOAP Client
		$tk_client = new MSSoapClient(TK_WSDL, array('trace' => TRUE));
		$tk_client->trace = 4;
		
		// Create the SOAP header
		if ($this->AuthKey == "") {
			$headert = new SoapHeader(TK_NS, 'MessageValidationInfo', $this->textKeyHeader, false);
			$tk_client->__setSoapHeaders(array($headert));
			$tkCommand = $tkCommandBase;

			// Show the header
			if (DEBUGGING) {
				print_r2($this->textKeyHeader, "TextKey HEADER");
				print_r2($headert, "SOAP HEADER");
			};
		}
		else {
			$tkCommand = $tkCommandBase . '_Key';
		}

		/* Build the SOAP Body */
		
		// Show the settings
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			$tkParamsArr = get_object_vars($tkBody);
			foreach($tkParamsArr as $key => $value) { 
				$params .= $key . ': ' . $value . TK_NEWLINE;
			} 			
			print_r2($params, "API Parameters");
		};

		try { 
		
			// Show the body
			if (DEBUGGING) {
				print_r2($tkBody, "BODY");
			};

			// Make the SOAP request
			$ExistResult = $tk_client->__soapCall($tkCommand, array($tkBody));

			// Handle the resulting payload
			if ($ExistResult != "") {
			
				// Format the payload
				$TextKey_Result = new stdclass();
				$TextKey_Result_Method = $tkCommand . "Result";
				$TextKey_Result = $ExistResult->$TextKey_Result_Method;

				// Handle the Error
				if ($TextKey_Result->errorDescr != "") {
					$error_msg = $TextKey_Result->errorDescr;
				};
			}
			else {
				$error_msg = "No payload with SOAP request " . $requestType . " Request"; 
			}
			
		} catch (Exception $e) { 
			$error_msg = $e->getMessage(); 
		}; 

		// Show the SOAP details
		if (OUTPUT_STATE != OUTPUT_PLAYLOAD) {
			if (OUTPUT_STATE == OUTPUT_FULL) {
				print_r2($tk_client->__getLastRequestHeaders(), "API Request Header");
			};
						
			$lastresponse = $tk_client->__getLastResponse();
			print_xml($lastresponse, "API Payload - Length: " . strlen($lastresponse));

			if (OUTPUT_STATE == OUTPUT_FULL) {
				print_r2($tk_client->__getLastResponseHeaders(), "API Response Header");
			};
		};
		
		// Handle the return object
		if (!($TextKey_Result)) {
			$TextKey_Result = new stdclass();
			$TextKey_Result->errorDescr = $error_msg;
		}
		return $TextKey_Result;
	}

	// Handle the doesRegistrationUserIDExist request
    public function perform_DoesRegistrationUserIDExist($UserID, $isHashed) {
		// Create the body
		$tkBody = new textKeyDoesRegistrationUserIDExistBody($UserID, $isHashed, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('doesRegistrationUserIdExist', $tkBody);
	}
	
	// Handle the DoesRegistrationCellNumberExist request
    public function perform_DoesRegistrationCellNumberExist($CellNumber) {
		// Create the body
		$tkBody = new textKeyDoesRegistrationCellNumberExistBody($CellNumber, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('doesRegistrationCellNumberExist', $tkBody);
	}

	// Handle the ValidateTextKeyFromUserId request
    public function perform_ValidateTextKeyFromUserId($UserID, $TextKey, $ValidationCode, $isHashed) {
		// Create the body
		$tkBody = new textKeyValidateTextKeyFromUserIdBody($UserID, $TextKey, $ValidationCode, $isHashed, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('validateTextKeyFromUserID', $tkBody);
	}

	// Handle the ValidateTextKeyFromCellNumber request
    public function perform_ValidateTextKeyFromCellNumber($CellNumber, $TextKey, $ValidationCode) {
		// Create the body
		$tkBody = new textKeyValidateTextKeyFromCellNumberBody($CellNumber, $TextKey, $ValidationCode, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('validateTextKeyFromCellNumber', $tkBody);
	}
	
	// Handle the IssueTextKeyFromUserId request
    public function perform_IssueTextKeyFromUserId($UserID, $isHashed) {
		// Create the body
		$tkBody = new textKeyIssueTextKeyFromUserIdBody($UserID, $isHashed, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('issueTextKeyFromUserId', $tkBody);
	}

	// Handle the IssueTextKeyFromCellNumber request
    public function perform_IssueTextKeyFromCellNumber($CellNumber) {
		// Create the body
		$tkBody = new textKeyIssueTextKeyFromCellNumberBody($CellNumber, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('issueTextKeyFromCellNumber', $tkBody);
	}

	// Handle the RegisterTextKeyUser request
    public function perform_registerTextKeyUser($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $UserID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode) {
		// Create the body
		$tkBody = new textKeyRegisterUserBody($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $UserID, $isHashed, $PinCode, $DistressPinCode, $TextKeyMode, $ReceiveMode, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('registerTextKeyUser', $tkBody);
	}

	// Handle the RegisterTextKeyUserCSA request
    public function perform_registerTextKeyUserCSA($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $Ownerbirthdate, $Gender, $RegUserID, $isHashed, $PinCode, $DistressPinCode, $q1, $a1, $q2, $a2, $q3, $a3, $TextKeyMode, $ReceiveMode) {
		// Create the body
		$tkBody = new textKeyRegisterUserCSABody($Command, $CellNumber, $OwnerFName, $OwnerLName, $Suppl1, $Suppl2, $Ownerbirthdate, $Gender, $RegUserID, $isHashed, $PinCode, $DistressPinCode, $q1, $a1, $q2, $a2, $q3, $a3, $TextKeyMode, $ReceiveMode, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('registerTextKeyUserCSA', $tkBody);
	}

	// Handle the GetTextKeyRegistration request
    public function perform_getTextKeyRegistration($RetrieveBy, $CellNumber, $Suppl1, $Suppl2) {
		// Create the body
		$tkBody = new getTextKeyRegistrationBody($RetrieveBy, $CellNumber, $Suppl1, $Suppl2, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('getTextKeyRegistration', $tkBody);
	}

	// Handle the GetTempAPI_Key request
    public function perform_GetTempAPI_Key($minutesDuration) {
		// Create the body
		$tkBody = new textKeyGetTempAPIKey_KeyBody($minutesDuration, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('getTempAPIKey', $tkBody);
	}

	// Handle the PollForIncomingTextKey  request
    public function perform_PollForIncomingTextKey($TextKey) {
		// Create the body
		$tkBody = new textKeyPollForIncomingTextKeyBody($TextKey, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('pollForIncomingTextKey', $tkBody);
	}

	// Handle the CreateNewCellNumberProxy request
    public function perform_CreateNewCellNumberProxy($CellNumber) {
		// Create the body
		$tkBody = new textKeyCreateNewCellNumberProxyBody($CellNumber, $this->AuthKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('createNewCellNumberProxy', $tkBody);
	}

	// Handle the RemoveTempAPIKey request
    public function perform_RemoveTempAPIKey($tempapiKey, $MinutesDuration) {
		// Create the body
		$tkBody = new textKeyRemoveTempAPIKey($MinutesDuration, $tempapiKey);
		
		// Handle the SOAP Request
		return $this->perform_SOAP_Request('removeTempAPIKey', $tkBody);
	}

}
?>