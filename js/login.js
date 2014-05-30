/*
** 
** login.js
**
** This is the basic framework for the login page. 
**
** The key here is that the back end login code returns with a payload containing the TextKey to be displayed to the user.
**
*/

var login = {
	message: null,
	textkey: null,
	textkeyVC: null,
	shortcode: null,
	loggedin: false,
	init: function () {
		$('#form-textkey').bind('keypress', function (e) {
			if (e.keyCode == 13) {
				$('#form-textkey .login-send').trigger('click');
			}
		});
		return false;
	},
	handlelogin: function () {
		// validate form
		if (login.validate()) {

			// Submit the form													   
			$.ajax({
				url: 'login.php',
				data: $('form').serialize() + '&action=login',
				type: 'post',
				cache: false,
				dataType: 'html',
				success: function (jsondata) {
					// Convert to an object
					data = eval(jsondata);
					
					if (typeof(console) !== 'undefined' && console != null) {
						console.log("data: " + jsondata);
						console.log(data);
					};

					// Check for a valid login
					if (data.error == "") {
						// Set the flag
						login.loggedin = true;
						
						// Set the textkey values
						login.textkey = data.textkey;
						login.textkeyVC = data.textkeyVC;
						login.shortcode = data.shortcode;

						// Handle the textkey login
						if (login.loggedin) {
							textKeyHandler(login.textkey, login.shortcode);
						};
					}
					else {
						// Set the error message
						login.message = data.error;
						
						// Show the error message
						login.showError();
					};
				},
				error: login.error
			});
		}
		else {
			// Show the error message
			login.showError();
		}
		return false;
	},
	handlelogout: function (refresh) {
		$.ajax({
			url: 'logout.php',
			type: 'post',
			cache: false,
			dataType: 'html',
			success: function (data) {
				if (data == "") {
					if (refresh) {
						location.reload(true);
					}
				}
				else {
					if (typeof(console) !== 'undefined' && console != null) {
						console.log("data: " + data);
					};
				};
			}
		});
		return false;
	},
	error: function (xhr) {
		showModal('Login Error...', xhr.statusText, closeModal);
		return false;
	},
	validate: function () {
		login.message = '';
		if (!$('#form-textkey #login-name').val()) {
			login.message = 'Username is required.';
			return false;
		};

		if (!$('#form-textkey #login-password').val()) {
			login.message = 'Password is required.';
			return false;
		};

		return true;
	},
	showError: function () {
		showModal('Login Error...', login.message, closeModal);
	}
};

login.init();
