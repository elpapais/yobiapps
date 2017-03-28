<?php
	ob_start();
	error_reporting(E_ALL);
	include_once ("header.php");
	require_once("helperFunctions.php");
	require_once("dbhelper.php");
	include_once "config.php";

	try
	{
		
		if(isset($_POST['username']))
		{
			$name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
			$userName = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
			$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

			$dbHelper = new DBHelper("", $_SERVER);
			
			if ($dbHelper->userExists($userName)) {
				header("location:register.php?msg=5");
				exit;
			}


			$dbHelper->createUser($userName, $password, $name);
			$address = $dbHelper->createUserAddress($userName);


			// Granting send and receive permissions to user

			$permissions = "send".",";
			$permissions .= "receive";
			$dbHelper->grantPermissions($userName, $permissions);


			// Granting write permission for Prime-Vault related stream to user

			$permissions = MultichainParams::VAULT_STREAMS['DATA']."."."write";
			$dbHelper->grantPermissions($userName, $permissions);		


			// Granting write permission for Prime-Contract related stream to user

			$permissions = MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS']."."."write";
			$dbHelper->grantPermissions($userName, $permissions);		
			$permissions = MultichainParams::CONTRACT_STREAMS['CONTRACT_FILES']."."."write";
			$dbHelper->grantPermissions($userName, $permissions);
			$permissions = MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES']."."."write";
			$dbHelper->grantPermissions($userName, $permissions);
			$permissions = MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES']."."."write";
			$dbHelper->grantPermissions($userName, $permissions);
			

			// Sending Yobicoin to user
			$dbHelper->sendInitYobicoins($userName);

			header("location:login.php?msg=7");
			// echo "<p class='lead'><b><font color='blue'>Account registration successful. Please login to continue.</font></b></p>";
		}
		else if(!isset($_POST['username']))
		{
			header("location:register.php?msg=3");
		}
		else if(!isset($_POST['password']))
		{
			header("location:register.php?msg=4");
		}
		else
		{
			header("location:register.php?msg=8");
		}

		
	}
	catch(Exception $ex)
	{
		echo $ex->getMessage();
	}

?>

		</div>
	</div>
</section>

<?php
	ob_end_flush();
	include ("footer.php");
?>