<?php
    session_start();
    ob_start();
    ini_set('error_reporting', E_ALL);
    error_reporting(E_ALL);

    try
    {
        include_once 'check-login.php';
        include_once 'helperFunctions.php';
        include_once 'config.php';
        include_once 'resources.php';
        include_once 'dbhelper.php';

        date_default_timezone_set("Asia/Kolkata");

        if(isset($_POST['fromaddr'])){
            if($_POST['fromaddr'] != $_SESSION['address']){ 
                throw new Exception("This address (".$_POST['fromaddr'].") does not belong to you."); 
            }
            $userAddress = $_POST['fromaddr']; 
        } 
        else if(isset($_SESSION['address'])){ 
            $userAddress = $_SESSION['address'];
        } 
        else {
            $userAddress = "";
        };

        $toAddress = isset($_POST['toaddr'])?$_POST['toaddr']:"";
        $cmd = isset($_POST['cmd'])?$_POST['cmd']:"";
        $units = isset($_POST['units'])?floatval($_POST['units']):floatval(0);

        if (floatval($units)<0) { throw new Exception("Units can't be negative"); }

        $metadata = isset($_POST['metadata']) ? htmlspecialchars($_POST['metadata'], ENT_QUOTES) : "";
        $dbHelper = new DBHelper();

        if(!$dbHelper->isAddressValid($userAddress))
        {
            header("location:login.php?msg=1");
        }

        if($cmd == Literals::MULTICHAIN_COMMANDS_CODES['NCB'])
        {
                $nativeCurrencyBalance = $dbHelper->getAssetBalanceForAddressByAssetName($userAddress, AssetParams::ASSET_NAME);
                echo "<strong><font color='blue'>".strval(number_format($nativeCurrencyBalance, 2))."</font></strong>";
        }
        else if($cmd == Literals::MULTICHAIN_COMMANDS_CODES['GET_TX_DETAILS'])
        {
            $txId = isset($_POST['txid']) ? $_POST['txid'] : "";
            $amount = isset($_POST['amt']) ? $_POST['amt'] : 0;
            $transaction = $dbHelper->getAddressTransaction($userAddress, $txId);

            echo "<h3>Transaction details</h3>";
            echo printTransactionBasicDetailsVertically($transaction, $userAddress, AssetParams::ASSET_NAME);

            if (isset($transaction['blockhash']) && $transaction['blockhash'] != "")
            {
                $blockDetails = $dbHelper->getBlockDetails($transaction['blockhash']);

                if (is_array($blockDetails))
                {
                    echo "<br/><br/>";
                    echo "<h3>Block details</h3>";
                    echo printBlockDetailsVertically($blockDetails);
                }
                
            }
        }
        else if($cmd == Literals::MULTICHAIN_COMMANDS_CODES['GET_TRANSACTIONS_HISTORY'])
        {
            $transactions = $dbHelper->listAddressTransactions($userAddress, 30);
            $transactions = array_reverse($transactions);
            echo printTransactionsHistory($transactions, $userAddress, AssetParams::ASSET_NAME);
        }
        else
        {

            if (!$dbHelper->isAddressValid($toAddress)) {
                $toAddress = $dbHelper->getUserAddress($toAddress);
            }

            if($cmd == Literals::MULTICHAIN_COMMANDS_CODES['SWM'])
            {
                if ($units<=0.0) {
                    throw new Exception("Amount cannot be zero or negative.", 1);                        
                }

                $txId = $dbHelper->sendAssetWithMessage($userAddress, $toAddress, AssetParams::ASSET_NAME, $units, bin2hex($metadata));

                echo "<strong><font color='green'>"."Transaction successful.</font> <br/><font color='green'>Transaction ID : </font>"."<a href='".ExplorerParams::$TX_URL_PREFIX.$txId."' target='_new'>$txId</a>"."<br/><font color='green'>Message Sent : </font>".$metadata."<br/><font color='green'>".strval(number_format($units, 2))."</font> Yobicoins transferred"."</strong>";
            }
            else 
            {
                throw new Exception("Error Processing Request", 1);                    
            }
        }

    }
    catch(Exception $ex)
    {     
        echo "<h4><font color='red'>Error: ".$ex->getMessage()."</font></h4>";
    }
	
	ob_end_flush();
?>