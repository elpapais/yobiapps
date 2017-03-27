<?php
	session_start();
    ob_start();
    require_once('check-login.php');

	try {

		include_once "config.php";
		include_once "resources.php";
		include_once "helperFunctions.php";
		include_once "dbhelper.php";

		$contractID = $_GET['contractid'];
		$invitees = $_GET['invitees'];
		$inviteesArr = explode(",", $invitees);
		$failedInvitees = array();
		$succeededInvitees = array();

		$dbHelper = new DBHelper(session_id(),$_SERVER);

		if (!$dbHelper->isValidContract($contractID)) {
			throw new Exception("Invalid Contract ID");
		}

		foreach ($inviteesArr as $inviteeID) 
		{
			$inviteeID = trim($inviteeID);
			if ($dbHelper->userExists($inviteeID))
			{
				if($dbHelper->inviteSignee($contractID, $inviteeID) === false)
				{
					array_push($failedInvitees, $inviteeID);
				}
				else
				{
					array_push($succeededInvitees, $inviteeID);
				}
			}
			else
			{
				array_push($failedInvitees, $inviteeID);
			}
		}
		
		if (count($succeededInvitees)>0)
		{			
			echo "<h4 style='color:green'>Invitations delivered to the following recipients: </h4><p style='color:green'>".implode(', ', $succeededInvitees)."</p>";
		}

		if (count($failedInvitees)>0)
		{			
			echo "<h4 style='color:red'>Invitations to the following users failed. The following users are either invalid or have already been invited to sign this contract: </h4><p style='color:red'>".implode(', ', $failedInvitees)."</p>";
		}
		
	} 
	catch (Exception $e)
	{
		echo "<h3 style='color:red'>".$e->getMessage()."</h3>";
	}
?>