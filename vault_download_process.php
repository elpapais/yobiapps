<?php
	session_start();
    ob_start();
    require_once('check-login.php');
	require_once('MCHelper.php');
	require_once('config.php');
	require_once('resources.php');
	require_once('helperFunctions.php');

	try
	{
		if (isset($_POST['txid']))
		{
			$txId = $_POST['txid'];
			$uploader_address = $_SESSION['address'];
			$dbHelper = new DBHelper(null,null);

			$transaction = $dbHelper->getAddressTransaction($uploader_address, $txId);
			echo "<h3 style='color:#0066cc'><b><u>Transaction Details</u></b></h3>";
			echo printStreamTransactionBasicDetailsVertically($transaction);
			echo "<h3 style='color:#0066cc'><b><u>Data</u></b></h3>";

			$vOut_n = -1;

			$dataHex = $dbHelper->getTransactionData($transaction['data'][0]);

			$dataArr = json_decode(hex2bin($dataHex));

			echo "<p><table class='table table-bordered table-hover'>";

			foreach ($dataArr as $key => $value) {

				if ($key!='file_hex') {
					echo "<tr><th style='border-style: ridge;'>".Literals::VAULT_FIELDS_DESC[$key]."</th><td style='border-style: ridge;'>".$value."</td></tr>";
				}				
			}

			$downloadFormHTML = "<form action='vault_file_download.php' method='post'>"."<input type='hidden' name='txid' value='".$txId."' />";
			$downloadFormHTML .= ($vOut_n != -1) ? "<input type='hidden' name='v_n' value='".$vOut_n."' />" : "";
			$downloadFormHTML .= "<input type='submit' class='btn blue' value='Click here' />";

			$downloadLinkHTML = "<a target='_new' href='vault_file_download.php?";
			$downloadLinkHTML .= "txid=".$txId;
			$downloadLinkHTML .= ($vOut_n != -1) ? "&v_n=".$vOut_n : "";
			$downloadLinkHTML .= "' class='btn blue'>Click here</a>";

			echo "<tr><th style='border-style: ridge;'>"."Download Link"."</th><td style='border-style: ridge;'>".$downloadLinkHTML."</td></tr>";
			echo "</table></p>";

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

