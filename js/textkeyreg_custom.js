/*
**
** textkeyreg_custom.js
**
** These functions handle the Demo registration page.
** 
*/

// Custom modal close handling for demo
function closeModalDemo() {
	closeModal();
	window.open("index.php", '_blank');
};

// Custom modal dialog handling for demo
function showDemoModal(title, msg, md_closeProc, md_button, md_height, md_width) {
	if (typeof(md_height) === "undefined") { md_height = 160; };
	if (typeof(md_width) === "undefined") { md_width = 625; };
	
	// Build the hmtl to display
	tkModalHTML = ('<div id="basic-modal-content"><h3>'+title+'</h3><p>'+msg+'</p><div class="modal-buttons-custom"><span><a class="modal-button" onclick="javascript:$.modal.close();">Close</a></span></div></div><!-- preload the images --><div style="display:none"><img src="./images/x.png" alt="" /></div>');

	$.modal(
		tkModalHTML, 
		{
			onClose: md_closeProc,
			containerCss:{
					height:md_height, 
					width:md_width
		},
	});
	
	// Set the close button name
	if (!(typeof(md_button) === "undefined")) { 
		$('.modal-button').html(md_button);
	};

	return true;
};

// Call to handle the successful registration
function registerSuccess() {
	var name = $('#register-name').val();
	var mobile = $('#register-mobile').val();
	
	showDemoModal('Your TextKey Sample registration is complete...', 'The user name <Strong>' + name + '</Strong> and mobile number <Strong>' + mobile + '</Strong> have been saved for use in this Sample Site.  When you click the button below it will take you to a Sample Login page where you will experience how TextKey works.', closeModalDemo, "Click and Go To Sample Site", 220, 625);

};

// Call to handle the failed registration
function registerFailed(tkTextKeyStatus) {
	// Show the issue
	showModal('Registration Error...', tkTextKeyStatus, closeModal);
};

function validatePhone(phone) {
	if (phone.length != 10) {
		return false;
	};
	if (!(phone.match(/^\d{10}/))) {
		return true;
	};
	return true;		
};

function formatPhone(mobile) {
	$('#register-mobile').val(mobile.substr(0, 3) + '-' + mobile.substr(3, 3) + '-' + mobile.substr(6,4));
};

// Register the user with TextKey
function registerTKUser()  {
	
	// Check for valid info.
	if ($('#register-name').val() == "") {
		showModal('Registration Error...', 'Please enter a name before submitting the registration information.', closeModal);
		return false;
	};

	var mobile = $('#register-mobile').val();
	if (!mobile) {
		showModal('Registration Error...', 'Please enter a valid mobile number before submitting the registration information.', closeModal);
		return false;
	}
	else {
		mobile = mobile.replace( /[^\d]/g, '' );
		if (!validatePhone(mobile)) {
			showModal('Registration Error...', 'The mobile number is invalid. It must contain 10 digits.', closeModal);
			return false;
		}
	};
	formatPhone(mobile);
	
	// Handle the registration
	$.ajax({
		url: 'register.php',
		data: $('form').serialize() + '&action=register',
		type: 'post',
		cache: false,
		dataType: 'html',
		success: function (data) {
			if ((data == " ") || (data == "")) {
				registerSuccess();
			}
			else {
				registerFailed(data);
			};
		}
	});
}
