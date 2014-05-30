<?PHP
class textkeysite
{
	/*
	** Init the session information
	*/
    function initSessionInfo()  {
		// Start the login session
        if (!isset($_SESSION)) { 
			session_start(); 
		};

		// User login Info.
		$_SESSION['username'] = NULL;
		$_SESSION['userid'] = NULL;

		// TextKey info.
		$_SESSION['textkeycheckuserid'] = NULL;
		$_SESSION['textkey'] = NULL;
		$_SESSION['textkeyvc'] = NULL;
		$_SESSION['shortcode'] = NULL;

		// 2 pass flags
		$_SESSION['passedcheck1'] = false;
		$_SESSION['passedcheck2'] = false;
    }

	/*
	** Set the session info. after the user passes the first check (i.e. Login/Password)
	*/
    function setPassedLoginCheck($username, $userid)  {
		// Start the login session
        if (!isset($_SESSION)) { 
			session_start(); 
		};

		// User login Info.
        $_SESSION['username'] = $username;
        $_SESSION['userid'] = $userid;
		
		// 2 pass flags
        $_SESSION['passedcheck1'] = true;
    }
	
	/*
	** Set the textkey session info. after the user passes the first check (i.e. Login/Password)
	*/
    function setTextKeyInfo($textkeycheckuserid, $textkey,  $textkeyvc, $shortcode)  {
		// Start the login session
        if (!isset($_SESSION)) { 
			session_start(); 
		};

		// TextKey info.
		$_SESSION['textkeycheckuserid'] = $textkeycheckuserid;
		$_SESSION['textkey'] = $textkey;
		$_SESSION['textkeyvc'] = $textkeyvc;
		$_SESSION['shortcode'] = $shortcode;
    }

	/*
	** Set the session info. after the user passes the second check (i.e. textkey authentication)
	*/
    function setPassedTKCheck($username, $userid)  {
		// Start the login session
        if (!isset($_SESSION)) { 
			session_start(); 
		};
		
		// 2 pass flags
        $_SESSION['passedcheck2'] = true;
    }
	
	/*
	** Check to see if the first check passed (i.e. Login/Password)
	*/
    function getPassedLoginCheck()  {
		// Start the login session
		if (!isset($_SESSION)) { 
			session_start(); 
		}
		
		// If the username/login check has not passed them return false
		if ($_SESSION['passedcheck1'] != true) {
			return false;
		}
		
		return true;
    }

	/*
	** Check to see if the second check passed (i.e. textkey authentication)
	*/
     function getPassedTKCheck()  {
		// Start the login session
		if (!isset($_SESSION)) { 
			session_start(); 
		}
		
		// If the TextKey check has not passed then return false
		if (($_SESSION['passedcheck1'] != true) || ($_SESSION['passedcheck2'] != true)) {
			return false;
		}
		
		return true;
    }

	/*
	** End the entire session
	*/
    function endSession() {
		// Start the login session
		if (!isset($_SESSION)) { 
			session_start(); 
		}
        
		// Clear out all of the session variables
		$_SESSION['username'] = NULL;
		$_SESSION['userid'] = NULL;
		$_SESSION['textkeycheckuserid'] = NULL;
		$_SESSION['textkey'] = NULL;
		$_SESSION['textkeyvc'] = NULL;
		$_SESSION['shortcode'] = NULL;
		$_SESSION['passedcheck1'] = false;
		$_SESSION['passedcheck2'] = false;
    }

	/*
	** Session get function calls
	*/
    function get_userName() {
        return isset($_SESSION['username'])?$_SESSION['username']:'';
    }
    
    function get_userId() {
        return isset($_SESSION['userid'])?$_SESSION['userid']:'';
    }

    function get_tkuserId() {
        return isset($_SESSION['textkeycheckuserid'])?$_SESSION['textkeycheckuserid']:'';
    }

    function get_textkey() {
        return isset($_SESSION['textkey'])?$_SESSION['textkey']:'';
    }

    function get_textkeyvc() {
        return isset($_SESSION['textkeyvc'])?$_SESSION['textkeyvc']:'';
    }

    function get_textkeyshortcode() {
        return isset($_SESSION['shortcode'])?$_SESSION['shortcode']:'';
    }
}
?>