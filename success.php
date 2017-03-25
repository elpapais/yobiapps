<?php

	session_start();
	error_reporting(E_ALL);
	require_once("config.php");
	require_once("dbhelper.php");
	
	if(isset($_SESSION['loggedin']) && isset($_SESSION['user_name']))
	{
		if($_SESSION['loggedin'] == true)
		{
			$userName = $_SESSION['user_name'];
			$ipAddr = $_SERVER['REMOTE_ADDR'];
			$sessionId = session_id();

			$dbHelper = new DBHelper($sessionId, $_SERVER);
			$dbHelper->createUserSession($userName);

			header("location:vault_upload.php");
		}
		else
		{
			header("location:login.php?msg=2");
		}
	}
	else
	{
		header("location:login.php?msg=2");
	}

?>