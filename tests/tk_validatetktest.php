<?php
	function phpErrorHandler($errnum,$errmsg,$file,$lineno){
		if($errnum==E_USER_WARNING){
			$alertmsg = 'Error: '.$errmsg.' File: '.$file.' Line: '.$lineno;
			echo $alertmsg;
			exit;	
		}
	}
	
	// define error handling function
	set_error_handler('phpErrorHandler');
	
	// Shared code
	include_once("../config.php");
	include_once("../textkey_rest_debug.php");
	
	// Setup
	define('OUTPUT_STATE', OUTPUT_FULL);
	
	// Create a TK object
	$tk = new textKey(TK_API);
		
	// Test Values
	$UserID = "BobSmith";
	$textkey = "5945606";
	$textkeyvc = "12DED";
	$isHashed = TK_ISHASHED;

	// Handle the operation
	$textkey_result = $tk->perform_ValidateTextKeyFromUserId($UserID, $textkey, $textkeyvc, $isHashed);
	if ($textkey_result->errorDescr == "") {
		$tkResultsArr = get_object_vars($textkey_result);
		$results = "";
		foreach($tkResultsArr as $key => $value) { 
			if (is_array($value)) {
				$results .= $key . ': ' . print_r($value, true) . "<BR />";
			}
			else if (is_object($value)) {
				$results .= $key . ': ' . print_r($value, true);
			}
			else {
				$results .= $key . ': ' . $value . "<BR />";
			}
		} 			
		print_r2($results, "API Returned Values", "#D6FCFF");
	}
	else {
		$results = 'Error: ' . $textkey_result->errorDescr . TK_NEWLINE;
		print_r2($results, "API Error", "#D6FCFF");
	}
?>