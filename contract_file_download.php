<?php
	session_start();
    ob_start();
    require_once('check-login.php');
	require_once('dbhelper.php');
	require_once('resources.php');
	require_once('helperFunctions.php');
    require_once('config.php');

	try
	{
		if (isset($_GET['txid']))
		{
			
			$txId = $_GET['txid'];
			$uploader_address = $_GET['publisher'];
			$dbHelper = new DBHelper();

			if (isset($_GET['v_n']))
			{
				$vOut_n = intval($_GET['v_n']);
				$fileContentHex = $dbHelper->getTransactionMetadata($txId, $vOut_n);
			}
			else
			{
				throw new Exception("Invalid Request. Missing parameter(s)!!");
				//$transaction = $dbHelper->getAddressTransaction($uploader_address, $txId);
				//$fileContentHex = $dbHelper->getDataFromDataItem($transaction['data'][1]);
			}

			$fileSignature = strtoupper(substr($fileContentHex, 0, 20));

			$fileDataType = getFileDataType($fileSignature);

			$downloadURL = "data:".$fileDataType.";base64,".base64_encode(pack('H*', $fileContentHex));
			echo "<script>window.onload=function(){window.open('".$downloadURL."', '_self')}</script>";
			
		}
		else
		{
			throw new Exception("No Transaction ID found.");
		}
	}
	catch(Exception $e)
	{
		echo "<h3 style='color:red'>".$e->getMessage()."</h3>";
	}

?>