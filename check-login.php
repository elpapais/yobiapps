<?php

	include_once("dbhelper.php");
	include_once("resources.php");

	try
	{
		if(isset($_SESSION['loggedin'])) 
		{
			if($_SESSION['loggedin'] == false){ header("location:login.php?msg=2"); exit(); }
		}
		else
		{
			header("location:login.php?msg=2");
		}

		if(!isset($_SESSION['user_name']))
		{
			header("location:login.php?msg=2");
		}

		$userName = isset($_SESSION['user_name'])?$_SESSION['user_name']:"";

	}
	catch(Exception $ex)
	{
		echo "Check-login Exception: " . $ex->getMessage();
	}
	
?>