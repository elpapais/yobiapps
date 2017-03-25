<?php
	session_start();
    ob_start();
    require_once('check-login.php');
	include_once "config.php";
	include_once "resources.php";
	include_once "MCHelper.php";

	$mcHelper = new MCHelper();
	$mcHelper->setUp(MultichainParams::HOST_NAME, MultichainParams::RPC_PORT, MultichainParams::RPC_USER, MultichainParams::RPC_PASSWORD);
	$items = $mcHelper->ListStreamPublisherItems(MultichainParams::VAULT_STREAMS['DATA'], $_SESSION['address'], true, 20 , -20);
	$items = array_reverse($items);

	echo "<table class='table table-bordered table-hover'>";
	echo "<tr><th style='border-style: ridge;'>Transaction ID</th><th style='border-style: ridge;'>Details</th></tr>";

	foreach ($items as $item) {
		echo "<tr class='appear-animation fadeInDown appear-animation-visible'><td style='border-style: ridge;'>".$item['txid']."</td><td style='border-style: ridge;'><a href='vault_upload_transaction_details.php?txid=".$item['txid']."' target='_new'>View details</a></td></tr>";
	}

	echo "</table>";
?>