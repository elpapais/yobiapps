<?php
	session_start();
    ob_start();
    require_once('check-login.php');
	include_once "config.php";
	include_once "resources.php";
	include_once "MCHelper.php";

	try
	{
		if(isset($_POST["dou"]))
		{
			$uploader_address = $_SESSION['address'];
			$dateOfUpload = DateTime::createFromFormat('Y-m-d H:i:s',str_replace("T", " ", $_POST["dou"]));
			$dateOfUploadStr = $dateOfUpload->format('d-M-Y H:i:s');

			$desc = isset($_POST["desc"])?$_POST["desc"]:"";
			$file = $_FILES['filename'];
			$target_file = $_FILES['filename']['tmp_name'];

		/// Reading file contents
			$handle = fopen($target_file, "rb");
			$fileContentHex = bin2hex(fread($handle, filesize($target_file)));
			fclose($handle);

			$contentArr = array(
				Literals::VAULT_FIELDS_CODES['date_of_upload'] => $dateOfUploadStr,
				Literals::VAULT_FIELDS_CODES['description'] => $desc,
				Literals::VAULT_FIELDS_CODES['file_hex'] => $fileContentHex
				);

			$contentJSON = json_encode($contentArr);
			$contentHex = bin2hex($contentJSON);		/// Hex encoding the metadata
			
			$streamKey = hash_file('sha256', $target_file);
			unlink($target_file);
			$mcHelper = new MCHelper();
			$mcHelper->setUp(MultichainParams::HOST_NAME, MultichainParams::RPC_PORT, MultichainParams::RPC_USER, MultichainParams::RPC_PASSWORD);
			$txId = $mcHelper->testPublishFrom($uploader_address, MultichainParams::VAULT_STREAMS['DATA'], $streamKey, $contentHex);	/// Publisher address and stream name to be modified

			echo "<b><font color='green'>Transaction Successful.<br/>"."Your Transaction ID is </font></b>"."<a href='vault_upload_transaction_details.php?txid=".$txId."'>".$txId."</a>";
		}
		else
		{
			throw new Exception("Error Processing Request");			
		}
	}
	catch (exception $ex)
	{
		echo "<font color='red'><b>".$ex->getMessage()."</b></font>";
	}

?>