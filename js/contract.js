
function uploadFile(button, contract_title, date, file, desc, output) {
	
	var progressBar = document.getElementById("progress");
	var progressLabel = document.getElementById("progressLabel");

	var xmlhttp = new XMLHttpRequest();
	try {

		button.disabled = true;
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;
				output.innerHTML = xmlhttp.responseText;
			}
		};

		xmlhttp.onprogress = function (e) {
			if (e.lengthComputable) {
				progressBar.setAttribute("aria-valuemax", e.total);
				progressBar.setAttribute("aria-valuenow", e.loaded);
			}
		}
		xmlhttp.onloadstart = function (e) {
			progressBar.setAttribute("aria-valuenow", 0);
			progressBar.hidden = false;
			progressLabel.innerHTML = "Loading...";
		}
		xmlhttp.onloadend = function (e) {
			progressBar.setAttribute("aria-valuenow", progressBar.getAttribute("aria-valuemax"));
			progressLabel.innerHTML = "Complete!";
		}

		xmlhttp.open("POST", "contract_upload_process.php", true);
//    	xmlhttp.setRequestHeader("Content-type", "multipart/form-data");
		var formData = new FormData();
		debugger
		formData.append("upload", "true");
		formData.append("title", contract_title.value);
		formData.append("dou", date.value);
		formData.append("desc", desc.value);
		formData.append("filename", document.getElementById('file').files[0]);
		xmlhttp.send(formData);


	}
	catch (e) {
		alert("Error: " + xmlhttp.statusText + e.description);
	}

}

function getContractDetails(button, input, output)
{	
	var progressBar = document.getElementById("progress");
	var progressLabel = document.getElementById("progressLabel");
	var xmlhttp = new XMLHttpRequest();

	try {
		var contractId = input.value;
		button.disabled = true;
		//output.innerHTML = "<h4 style='color:green'>Processing...</h4>";
		xmlhttp.onreadystatechange = function () {
			 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;
				output.innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.onprogress = function (e) {
			if (e.lengthComputable) {				
				progressBar.setAttribute("aria-valuemax", e.total);
				progressBar.setAttribute("aria-valuenow", e.loaded);
			}
		}
		xmlhttp.onloadstart = function (e) {
			progressBar.setAttribute("aria-valuenow", 0);
			progressBar.hidden = false;
			progressLabel.innerHTML = "Loading...";
		}
		xmlhttp.onloadend = function (e) {
			progressBar.setAttribute("aria-valuenow", progressBar.getAttribute("aria-valuemax"));
			progressLabel.innerHTML = "Complete!";
		}
		
		xmlhttp.open("GET", "contract_details.php?contractid=" + contractId);
		//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send();
	}
	catch(ex){
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + ex.description);
	}
}

function verifyAndSignContract(button, input, output)
{	
	var progressBar = document.getElementById("progress");
	var progressLabel = document.getElementById("progressLabel");
	var divSignConfirm = document.getElementById("sign_confirm");
	var xmlhttp = new XMLHttpRequest();

	try {
		var contractId = input.value;
		button.disabled = true;
		//output.innerHTML = "<h4 style='color:green'>Processing...</h4>";
		xmlhttp.onreadystatechange = function () {
			 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;
				output.innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.onprogress = function (e) {
			if (e.lengthComputable) {				
				progressBar.setAttribute("aria-valuemax", e.total);
				progressBar.setAttribute("aria-valuenow", e.loaded);
			}
		}
		xmlhttp.onloadstart = function (e) {
			progressBar.setAttribute("aria-valuenow", 0);
			progressBar.hidden = false;
			divSignConfirm.hidden = true;
			progressLabel.innerHTML = "Loading...";
		}
		xmlhttp.onloadend = function (e) {
			progressBar.setAttribute("aria-valuenow", progressBar.getAttribute("aria-valuemax"));
			progressLabel.innerHTML = "Complete!";
			divSignConfirm.hidden = false;
		}
		
		xmlhttp.open("GET", "contract_details.php?contractid=" + contractId);
		//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send();
	}
	catch(ex){
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + ex.description);
	}
}

function signContract(button, input, output)
{
	var xmlhttp = new XMLHttpRequest();

	try {
		var contractId = input.value;
		button.disabled = true;
		//output.innerHTML = "<h4 style='color:green'>Processing...</h4>";
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4) {
				button.disabled = false;
			}

			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				window.location = "contract_sign.php?msg=" + "1";
				//output.innerHTML = xmlhttp.responseText;
			}

			if (xmlhttp.status == 500) {
				output.innerHTML = "<h3 style='color:red'>Signing failed.</h3>";
			}
		};

		xmlhttp.open("GET", "contract_sign_process.php?contractid=" + contractId);
		//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send();
	}
	catch(ex){
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + ex.description);
	}
}

function inviteSignees(button, contractIdElement, inviteesElement, output)
{
	var xmlhttp = new XMLHttpRequest();

	try {
		var contractId = contractIdElement.value;
		var invitees = inviteesElement.value;
		button.disabled = true;

		xmlhttp.onreadystatechange = function () {
			 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;
				output.innerHTML = xmlhttp.responseText;
			}
		};

		xmlhttp.open("GET", "contract_invite_process.php?contractid=" + contractId + "&invitees=" + invitees);
		//xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send();
	}
	catch(ex){
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + ex.description);
	}
}

function getRecentTransactions(button, output)
{
	var xmlhttp = new XMLHttpRequest();

	try {

		if (button != null) {
			button.disabled = true;
		}
		xmlhttp.onreadystatechange = function () {
			 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				if (button != null) {
					button.disabled = false;
				}
				output.innerHTML = xmlhttp.responseText;
			}
		};
		
		xmlhttp.open("GET", "vault_recent_transactions.php");
		xmlhttp.send();
	}
	catch(ex){
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + ex.description);
	}
}