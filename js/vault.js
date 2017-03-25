
function uploadFile(date, file, desc, button, output) {
	
	var progressBar = document.getElementById("progress");
	var progressLabel = document.getElementById("progressLabel");

	button.disabled = true;

	var xmlhttp = new XMLHttpRequest();
	try {

		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4) {
				button.disabled = false;
				if (xmlhttp.status == 200) {
					output.innerHTML = xmlhttp.responseText;
				}				
			}
		};

		xmlhttp.onprogress = function (e) {
			if (e.lengthComputable) {
				progressBar.max = e.total;
				progressBar.value = e.loaded;
			}
		}
		xmlhttp.onloadstart = function (e) {
			progressBar.value = 0;
			progressBar.hidden = false;
			progressLabel.innerHTML = "Loading...";
		}
		xmlhttp.onloadend = function (e) {
			progressBar.value = e.loaded;
			progressLabel.innerHTML = "Your file is ready!";
		}

		xmlhttp.open("POST", "vault_upload_process.php", true);
//    	xmlhttp.setRequestHeader("Content-type", "multipart/form-data");
		var formData = new FormData();
		debugger
		formData.append("upload", "true");
		formData.append("dou", date.value);
		formData.append("desc", desc.value);
		formData.append("filename", document.getElementById('file').files[0]);
		xmlhttp.send(formData);


	}
	catch (e) {
		button.disabled = false;
		alert("Error: " + xmlhttp.statusText + e.description);
	}

}

function getAssetDetails(button, input, output)
{	
	var progressBar = document.getElementById("progress");
	var progressLabel = document.getElementById("progressLabel");
	var xmlhttp = new XMLHttpRequest();

	try {
		var txId = input.value;
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
				progressBar.max = e.total;
				progressBar.value = e.loaded;
			}
		}
		xmlhttp.onloadstart = function (e) {
			progressBar.value = 0;
			progressBar.hidden = false;
			progressLabel.innerHTML = "Loading...";
		}
		xmlhttp.onloadend = function (e) {
			progressBar.value = e.loaded;
			progressLabel.innerHTML = "Your file is ready!";
		}
		
		xmlhttp.open("POST", "vault_download_process.php");
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("txid=" + txId);
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