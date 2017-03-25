
/*
function resetFields() {
	var toAddrElement = document.getElementById('txtToAddrSWM');
	var msgElement = document.getElementById('txtMessageSWM');
	var qtyElement = document.getElementById('txtUnitsSWM');
}
*/

function checkEnterpress(e) {
	var code = (e.keyCode ? e.keyCode : e.which);
	
	if(code == 13) {
	 	return true;
	}

	return false;	
}


function sendMessageToBot(input, outputTable) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var msg = input.value.replace(/\r?\n|\r/g, "");;
		input.value = "";
		if (msg != "") 
		{
			var tr = document.createElement("TR");
			var td = document.createElement("TD");
			td.innerHTML = "<b><i>You:</i></b><br/>" + msg + "<br/><br/>";
			tr.appendChild(td);
			outputTable.appendChild(tr);

			xmlhttp.onreadystatechange = function () {
			 	//alert(xmlhttp.readyState);
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var tr1 = document.createElement("TR");
					var td1 = document.createElement("TD");
					td1.innerHTML = "<b><i>Bot:</i></b><br/>" + xmlhttp.responseText + "<br/>";
					tr1.appendChild(td1);
					outputTable.appendChild(tr1);
				}					
			};
			xmlhttp.open("POST", "msgprocessor.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send("msg=" + msg);
		}
	}
	catch(ex){
		xmlhttp = null;
		console.log("Error: " + xmlhttp.statusText + " " + ex.description);
	}
}


function sendToAddress(fromAddrId, toAddrId, unitsId, button, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		button.disabled = true;
		var cmd = "sendtoaddr";
		var fromAddrElement = document.getElementById(fromAddrId);
		var fromAddr = fromAddrElement.value;
		var toAddr = document.getElementById(toAddrId).value;
		var units = document.getElementById(unitsId).value;
		
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;			
				document.getElementById(outputId).innerHTML = xmlhttp.responseText;
			}					
		};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + fromAddr + "&toaddr=" + toAddr + "&units=" + units);
	}
	catch(ex){
		button.disabled = false;
		document.getElementById(outputId).innerHTML = ex.description;
		console.log("Error: " + xmlhttp.statusText + " " + ex.description);
	}
}


function sendMetadataToAddress(fromAddrId, toAddrId, dataId, unitsId, button, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		button.disabled = true;
		var cmd = "sendwithmetadata";
		var fromAddrElement = document.getElementById(fromAddrId);
		var fromAddr = fromAddrElement.value;
		var toAddr = document.getElementById(toAddrId).value;
		var metadata = document.getElementById(dataId).value;
		var units = document.getElementById(unitsId).value;
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				button.disabled = false;			
				document.getElementById(outputId).innerHTML = xmlhttp.responseText;
			}					
		};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + fromAddr + "&toaddr=" + toAddr + "&metadata=" + metadata + "&units=" + units);
	}
	catch(ex){
		button.disabled = false;
		document.getElementById(outputId).innerHTML = ex.description;
		console.log("Error: " + xmlhttp.statusText + " " + ex.description);
	}
}


function getNativeCurrencyBalance(addrId, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "nativecurrencybalance";
		//document.getElementById(outputId).innerHTML = "Loading...";
		addrElement = document.getElementById(addrId);
		var addr = addrElement.value;
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById(outputId).innerHTML = xmlhttp.responseText;
			}					
		};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + addr);
	}
	catch(ex){
		console.log("Error: " + ex.description);
		console.log(xmlhttp.statusText);
	}
}


function getTransactionsHistory(addrId, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "gettransactionshistory";
		//document.getElementById(outputId).innerHTML = "Loading...";
		addrElement = document.getElementById(addrId);
		var addr = addrElement.value;
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById(outputId).innerHTML = xmlhttp.responseText;
			}					
		};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + addr);
	}
	catch(ex){
		console.log("Error: " + ex.description);
		console.log(xmlhttp.statusText);
	}
}


function fetchTransactionDetails(txid, amt, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "gettransactiondetails";

		xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById(outputId).innerHTML = xmlhttp.responseText;
				}
			};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&txid=" + txid + "&amt=" + amt);
	}
	catch(ex){
		console.log("Error: " + ex.description);
		console.log(xmlhttp.statusText);
	}
}


function fetchMessagesCount(addrId, outputId) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "fetchmessagescount";
		addrElement = document.getElementById(addrId);
		var addr = addrElement.value;
		xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					document.getElementById(outputId).innerHTML = 'Unread(' + xmlhttp.responseText + ')';
				}
			};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + addr);
	}
	catch(ex){
		console.log("Error: " + ex.description);
		console.log(xmlhttp.statusText);
	}
}


function fetchMessages(addrElement, output) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "fetchmessages";
		var addr = addrElement.value;
		output.innerHTML = "Loading...";
		xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					output.innerHTML = xmlhttp.responseText;
				}
			};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + addr);
	}
	catch(ex){
		console.log("Error: " + xmlhttp.statusText + " " + ex.description);
	}
}


function markRead(addrElement, output) {
	var xmlhttp = new XMLHttpRequest();
	try {
		var cmd = "mark_read";
		var addr = addrElement.value;
		xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					output.innerHTML = 'Unread(0)';
				}
			};
		xmlhttp.open("POST", "ic_processor.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("cmd=" + cmd + "&fromaddr=" + addr);
	}
	catch(ex){
		console.log("Error: " + xmlhttp.statusText + " " + ex.description);
	}
}