<?php
	session_start();
    ob_start();
    require_once('check-login.php');
	include_once "config.php";
	include_once "resources.php";
	include_once "dbhelper.php";
	include_once "helperFunctions.php";

	try
	{
		if(isset($_POST["dou"]))
		{
			$dateOfUpload = DateTime::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", $_POST["dou"]));
		}
		else
		{
			$dateOfUpload = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
		}
		
		$uploaderAddress = $_SESSION['address'];
		$uploaderID = $_SESSION['user_name'];

		$dateOfUploadStr = $dateOfUpload->format('d-M-Y H:i:s');
		$title = isset($_POST["title"])?$_POST["title"]:"";
		$desc = isset($_POST["desc"])?$_POST["desc"]:"";
		$file = $_FILES['filename'];
		$target_file = $_FILES['filename']['tmp_name'];

	/// Reading file contents
		$handle = fopen($target_file, "rb");
		$fileContentHex = bin2hex(fread($handle, filesize($target_file)));
		fclose($handle);
		
		$fileHash = hash_file('sha256', $target_file);
		unlink($target_file);

		$dbHelper = new DBHelper();		
		$contractID = generateGUID();
		$txID = $dbHelper->uploadContract($contractID, $uploaderAddress, $title, $dateOfUploadStr, $desc, $fileHash);
		$signature = $dbHelper->signMessage($uploaderAddress, $fileHash);
		$txIDSign = $dbHelper->signContract($contractID, $uploaderID, $uploaderAddress, $signature);


		echo "<b><font color='green'>Transaction Successful.<br/>"."Your Contract ID is </font></b>"."<a target='_new' href='contract_upload_details.php?contractid=".$contractID."'>".$contractID."</a>";
	}
	catch (exception $ex)
	{
		echo "<font color='red'><b>".$ex->getMessage()."</b></font>";
	}

?>