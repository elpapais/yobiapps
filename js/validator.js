
function validatePasswordMatch() {
	var pass = document.getElementById('password'), confirmPass = document.getElementById('confirm_password');
	if(pass.value != confirmPass.value) {
		confirmPass.setCustomValidity("Passwords do not match");
		return false;
	}
	else {
		confirmPass.setCustomValidity("");
		return true;
	}
}


function validateUsername() {
	var username = document.getElementById('username');
	var re = /^[a-zA-Z0-9]{5,50}$/; 
	var str = username.value;
	var m;
	 
	if ((m = re.exec(str)) !== null) {
		username.setCustomValidity("");
		if (m.index === re.lastIndex) {
			re.lastIndex++;
		}
		//alert('m[0]= ' + m[0] +', m[1] = ' + m[1]);
		// View your result using the m-variable.
		// eg m[0] etc.
	}
	else{
		username.setCustomValidity("Username should be alphanumeric and contain 5 or more characters.");
	}
	return true;
}


function validatePassword() {
	var password = document.getElementById('password');
	var re = /^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,16}$/; 
	var str = password.value;
	var m;
	 
	if ((m = re.exec(str)) !== null) {
		password.setCustomValidity("");
		if (m.index === re.lastIndex) {
			re.lastIndex++;
		}
		//alert('m[0]= ' + m[0] +', m[1] = ' + m[1]);
		// View your result using the m-variable.
		// eg m[0] etc.
	}
	else{
		password.setCustomValidity(
			"Your password\n- May contain letter and numbers.\n- Must contain at least 1 number and 1 letter.\n- May contain any of these characters: !@#$%\n- Must be 8-16 characters.</ol>");
	}
	return true;
}

function validateName() {
	var name = document.getElementById('name');
	var re = /^[a-zA-Z ]*$/; 
	var str = name.value;
	var m;
	 
	if ((m = re.exec(str)) !== null) {
		username.setCustomValidity("");
		if (m.index === re.lastIndex) {
			re.lastIndex++;
		}
		//alert('m[0]= ' + m[0] +', m[1] = ' + m[1]);
		// View your result using the m-variable.
		// eg m[0] etc.
	}
	else{
		name.setCustomValidity("Name should contain only alphabets and whitespaces.");
	}
	return true;
}