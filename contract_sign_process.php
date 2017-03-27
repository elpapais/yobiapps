<?php
	session_start();
    ob_start();
    require_once('check-login.php');

	try {

		include_once "config.php";
		include_once "resources.php";
		include_once "helperFunctions.php";
		include_once "dbhelper.php";

		$signerAddress = $_SESSION['address'];
		$signerID = $_SESSION['user_name'];

		if (isset($_GET['contractid'])) {
			$contractID = $_GET['contractid'];
		}
		else {
			throw new Exception("Invalid Contract ID", 1);
		}

		$dbHelper = new DBHelper(session_id(), $_SERVER);

		if (!$dbHelper->isValidContract($contractID)) {
			throw new Exception("Invalid Contract ID");
		}
		
		if (!$dbHelper->isAuthorizedToSignContract($_SESSION['address'], $contractID)) {
			throw new Exception("You are not authorized to sign this contract");
		}
		
		if ($dbHelper->hasSignedTheContract($_SESSION['address'], $contractID)) {
			throw new Exception("You have already signed this contract");
		}

		$contractDetails = $dbHelper->getContractDetails($contractID);

		$fileHash = $contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['FILE_HASH']];

		$signature = $dbHelper->signMessage($signerAddress, $fileHash);

		$txID = $dbHelper->signContract($contractID, $signerID, $signerAddress, $signature);

		echo "<h4 style='color:green'>Signed Successfully</h4>";

	} 
	catch (Exception $e)
	{
		echo "<h3 style='color:red'>".$e->getMessage()."</h3>";
	}

?>