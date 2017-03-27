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
			$uploader_address = $_SESSION['address'];
			$dbHelper = new DBHelper();

			if (isset($_GET['v_n']))
			{
				$vOut_n = intval($_GET['v_n']);
				$dataHex = $dbHelper->getTransactionMetadata($txId, $vOut_n);
			}
			else
			{
				$transaction = $dbHelper->getAddressTransaction($uploader_address, $txId);
				$dataHex = $dbHelper->getDataFromDataItem($transaction['data'][0]);
			}

			$dataArr = json_decode(hex2bin($dataHex));
			$fileContentHex = $dataArr->file_hex;

			$fileSignature = strtoupper(substr($fileContentHex, 0, 20));

			if(strpos($fileSignature, "FFD8FF") === 0)
			{
				$fileExtension = "jpg";
				$fileDataType = "image/jpeg";
			}
			elseif(strpos($fileSignature, "474946") === 0)
			{
				$fileExtension = "gif";
				$fileDataType = "image/".$fileExtension;
			}
			elseif(strpos($fileSignature, "89504E") === 0)
			{
				$fileExtension = "png";
				$fileDataType = "image/".$fileExtension;
			}
			elseif(strpos($fileSignature, "424D") === 0)
			{
				$fileExtension = "bmp";
				$fileDataType = "image/".$fileExtension;
			}
			elseif(strpos($fileSignature, "492049") === 0)
			{
				$fileExtension = "tif";
				$fileDataType = "image/".$fileExtension;
			}
			elseif(strpos($fileSignature, "25504446") === 0)
			{
				$fileExtension = "pdf";
				$fileDataType = "application/".$fileExtension;
			}
			else
			{
				throw new Exception("File type not supported. Signature - " . $fileContentHex);
			}

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