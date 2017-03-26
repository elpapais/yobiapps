<?php
	include_once('config.php');
	include_once('resources.php');
	include_once 'dbhelper.php';

	function randomNDigitNumber($digits)
	{
		$number = rand(pow(10, $digits-1), pow(10, $digits)-1);
		return $number;
	}

	function generatePassword()
	{
	    $range = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ01234567129!@#$%^&*()_+-=?";
	    $pwd = array(); 
	    $rangelength = strlen($range) - 1; 
	    for ($i = 0; $i < 12; $i++) {
	        $n = rand(0, $rangelength);
	        $pwd[] = $range[$n];
	    }
	    return implode($pwd); 
	}

	function generateGUID()
	{
		if (function_exists('com_create_guid'))
	    {
	        return com_create_guid();
	    }
	    else
	    {
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	            .substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid,12, 4).$hyphen
	            .substr($charid,16, 4).$hyphen
	            .substr($charid,20,12)
	            .chr(125);// "}"
	        return trim($uuid,'{}');
	    }
	}

	function validateName($name)
	{
		return preg_match('/^[a-zA-Z ]*$/', $name);
	}
	
	function validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	function validateUsername($username)
	{
		return preg_match('/^[a-zA-Z0-9]{5,50}$/', $username);
	}

	function validatePassword($password)
	{
		return preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,16}$/', $password);
	}


	function getFileDataType($fileSignature)
	{
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
			throw new Exception("File type not supported.");
		}

		return $fileDataType;
	}

	/**
	*** Get the recipient address
	*/
	function getRecipientsFromTransaction($transaction)
	{
		$transferred = 2;

		foreach($transaction as $param_name=>$param_value)
		{
			if($param_name=="balance")
			{
				$amount = $param_value["amount"];
				$assets = $param_value["assets"];

				if($amount<0)
					$transferred = 1;
				else if($amount>0)
					$transferred = 2;
				else
					$transferred = 1;			//change: Shd be modified to zero later.

				foreach($param_value["assets"] as $index=>$value)
				{
					$asset_name = $value["name"];
					$asset_qty = $value["qty"];

					if($value["qty"]<0)
					{
						$transferred = 1;
						array_push($assets, $value);
					}
					else if($value["qty"]>0)
					{
						$transferred = 2;
						array_push($assets, $value);
					}
					else
					{
						//$transferred = 0;
					}

				}
			}
			else if($param_name=="myaddresses")
			{
				if($transferred==1)
					$sender = $param_value;
				else if($transferred==2)
					$recipient = $param_value;
			}
			else if($param_name=="addresses")
			{
				if($transferred==1)
					$recipient = $param_value;
				else
					$sender = $param_value;
			}
			
		}

		return $recipient;
	}



	/**
	*** Get the recipient address
	*/
	function getAssestsAmountFromTransaction($transaction)
	{
		$transferred = 2;

		foreach($transaction as $param_name=>$param_value)
		{
			if($param_name=="balance")
			{
				$amount = $param_value["amount"];
				$assets = $param_value["assets"];
				$assetsAmount = array();

				if($amount<0)
					$transferred = 1;
				else if($amount>0)
					$transferred = 2;
				else
					$transferred = 1;			//change: Shd be modified to zero later.

				foreach($param_value["assets"] as $index=>$value)
				{
					$asset_name = $value["name"];
					$asset_qty = $value["qty"];

					$assetsAmount[$asset_name] = abs($asset_qty);

				}
			}
			
		}

		return $assetsAmount;
	}



	/**
	*** Prints the basic details of a transaction.
	*/
	function printTransactionBasicDetailsVertically($transaction, $userAddress, $assetRef="")
	{
		
		$printDetails = "<div class='table-responsive'><br/><table class='table'>";

		foreach($transaction as $param_name=>$param_value)
			{		
				if($param_name=="txid")
				{
					$txId = $param_value;
				}
				else if($param_name=="balance")
				{					
					$assets = array();

					foreach($param_value["assets"] as $index=>$value)
					{

						if ($value["assetref"] == $assetRef) {
							$asset_name = $value["name"];
							$amount = $value["qty"];

							if($value["qty"]<0)
							{
								$senderAddress = $transaction['myaddresses'][0];
								$recipientAddress = $transaction['addresses'][0];
								array_push($assets, $value);
							}
							else if($value["qty"]>0)
							{
								$senderAddress = $transaction['addresses'][0];
								$recipientAddress = $transaction['myaddresses'][0];
								array_push($assets, $value);
							}
						}

					}					
				}
				else if($param_name=="blockhash")
				{
					$blockHash = $param_value;
				}
				else if($param_name=="blockindex")
				{
					$blockIndex = $param_value;
				}
				else if($param_name=="time")
				{
					$time = $param_value;
				}
				else if($param_name=="comment")
				{
					$comment = $param_value;
				}
				else if($param_name=="data")
				{
					if (!empty($param_value))
					{
						if (is_string($param_value[0]))
						{
			                $data = $param_value[0];
			            }
			            else
			            {
			            	$dbHelper = new DBHelper();
			                $data = $dbHelper->getTransactionMetadata($param_value['txid'], $param_value['vout']);
			            }
					}
				}
				else if($param_name=="confirmations")
				{
					$confirmations = $param_value;
				}

			}

		$printDetails .= "<tr><th>Transaction Id</th><td>".$txId."</td></tr>";
		$printDetails .= "<tr><th>Sender</th><td>".(($senderAddress==$userAddress) ? "You (".$senderAddress.")" : $senderAddress)."</td></tr>";
		$printDetails .= "<tr><th>Recipient</th><td>".(($recipientAddress==$userAddress) ? "You (".$recipientAddress.")" : $recipientAddress)."</td></tr>";
		$printDetails .= "<tr><th>Amount</th><td>".abs(floatval($amount))."</td></tr>";
		$printDetails .= "<tr><th>Confirmations</th><td>".$confirmations."</td></tr>";
		$printDetails .= "<tr><th>Block Hash</th><td>".$blockHash."</td></tr>";
		$printDetails .= "<tr><th>Block Index</th><td>".$blockIndex."</td></tr>";
		$printDetails .= "<tr><th>Time</th><td>".date('m-d-Y'.',  '.'h:i:s a', $time)."</td></tr>";
		
		$printDetails .= "</table><br/></div>";
		return $printDetails;
	}


	/**
	*** Prints the History of transactions for an address.
	*/
	function printTransactionsHistory($transactions, $userAddress, $assetName="")
	{
		date_default_timezone_set("Asia/Kolkata");

		$senderAddress = "";
		$recipientAddress = "";
		$data = "";
		$transferred = 2;
		$amount = 0;
		$printDetails = "<div class='table-responsive'><br/><table class='table table-bordered table-hover'>";
		$printDetails .= "<tr>";
		$printDetails .= "<th>S.No.</th>";
		$printDetails .= "<th>Sender</th>";
		$printDetails .= "<th>Recipient</th>";
		$printDetails .= "<th>Amount<br/>(in Indiacoins)</th>";
		$printDetails .= "<th>Message</th>";
		$printDetails .= "<th>Confirmations</th>";
		$printDetails .= "<th>Time</th>";
		$printDetails .= "</tr>";

		$transactions_filtered = array();

		foreach ($transactions as $tran) {
			if (count($tran['balance']['assets'])>0) {
				array_push($transactions_filtered, $tran);
			}
		}

		foreach ($transactions_filtered as $txIndex=>$transaction) {

			foreach($transaction as $param_name=>$param_value)
			{		
				if($param_name=="txid")
				{
					$txId = $param_value;
				}
				else if($param_name=="balance")
				{
					
					$assets = array();

					foreach($param_value["assets"] as $index=>$value)
					{

						if ($value["name"] == $assetName) {
							$asset_name = $value["name"];
							$amount = $value["qty"];

							if($value["qty"]<0)
							{
								$senderAddress = $transaction['myaddresses'][0];
								$recipientAddress = $transaction['addresses'][0];
								array_push($assets, $value);
							}
							else if($value["qty"]>0)
							{
								$senderAddress = $transaction['addresses'][0];
								$recipientAddress = $transaction['myaddresses'][0];
								array_push($assets, $value);
							}
						}

					}
					
				}
				else if($param_name=="blockhash")
				{
					$blockHash = $param_value;
				}
				else if($param_name=="blockindex")
				{
					$blockIndex = $param_value;
				}
				else if($param_name=="time")
				{
					$time = $param_value;
				}
				else if($param_name=="comment")
				{
					$comment = $param_value;
				}
				else if($param_name=="data")
				{
					if (!empty($param_value))
					{
						if (is_string($param_value[0]))
						{
			                $data = $param_value[0];
			            }
			            else
			            {
			            	if (count($param_value[0])>0)
			            	{
				                $dbHelper = new DBHelper();
			                	$data = $dbHelper->getTransactionMetadata($param_value['txid'], $param_value['vout']);
			            	}
			            	else {
			            		$data="";
			            	}
			            }
					}
					else
					{
						$data = "";
					}
				}
				else if($param_name=="confirmations")
				{
					$confirmations = $param_value;
				}

			}
			/*
			$sender = getUserNameFromAddress($senderAddress);
			$sender = ($sender=="") ? "Private Address" : $sender;

			$recipient = getUserNameFromAddress($recipientAddress);
			$recipient = ($recipient=="") ? "Private Address" : $recipient;
			*/

			$printDetails .= "<tr>";
			$printDetails .= "<td>".($txIndex + 1)."</td>";
			$printDetails .= "<td>".(($senderAddress==$userAddress) ? "You" : $senderAddress)."</td>";
			$printDetails .= "<td>".(($recipientAddress==$userAddress) ? "You" : $recipientAddress)."</td>";
			$printDetails .= "<td>".abs(floatval($amount))."</td>";
			$printDetails .= "<td>".(hex2bin($data)=="" ? "-" : hex2bin($data))."</td>";
			$printDetails .= "<td>".$confirmations."</td>";
			$printDetails .= "<td>".date('d-m-Y'.', '.'h:i:s a', $time)."</td>";
			$printDetails .= "</tr>";
		}
			
		$printDetails .= "</table><br/></div>";
		return $printDetails;
	}


	/**
	*** Prints the basic details of a transaction.
	*/
	function printStreamTransactionBasicDetailsVertically($transaction)
	{
		//global $explorer_tx_url,$explorer_address_url,$explorer_block_url;
	
		$printDetails = "<div><br/><table class='table table-bordered table-hover'>";

		foreach($transaction as $param_name=>$param_value)
		{		
			if($param_name=="txid")
			{
				$txId = $param_value;
			}
			else if($param_name=="myaddresses" || $param_name=="publishers")
			{
				$publisherAddress = $param_value;
			}			
			else if($param_name=="blockhash")
			{
				$blockHash = $param_value;
			}
			else if($param_name=="blockindex")
			{
				$blockIndex = $param_value;
			}
			else if($param_name=="time")
			{
				$time = $param_value;
			}
			else if($param_name=="comment")
			{
				$comment = $param_value;
			}
			else if($param_name=="data")
			{
				$data = $param_value;
			}
			else if($param_name=="confirmations")
			{
				$confirmations = $param_value;
			}

		}

		$printDetails .= "<tr height=25><th width=150 style='border-style: ridge;'>Transaction Id</th><td align='left' style='border-style: ridge;'>"."<a href='".ExplorerParams::$TX_URL_PREFIX."$txId' target='_new'>".$txId."</a>"."</td></tr>";
		
		$printDetails .= "<tr height=25><th style='border-style: ridge;'>Uploader</th><td align='left' style='border-style: ridge;'>"."<a href='".ExplorerParams::$ADDRESS_URL_PREFIX.$publisherAddress[0]."' target='_new'>".$publisherAddress[0]."</a>"."</td></tr>";
		
		$printDetails .= "<tr height=25><th width=150 style='border-style: ridge;'>Block Hash</th><td align='left' style='border-style: ridge;'>".(isset($blockHash) ? "<a href='".ExplorerParams::$BLOCK_URL_PREFIX."$blockHash' target='_new'>".$blockHash."</a>" : "")."</td></tr>";
		
		$printDetails .= "<tr height=25><th width=150 style='border-style: ridge;'>Confirmations</th><td align='left' style='border-style: ridge;'>".$confirmations."</td></tr>";

		// $printDetails .= (is_string($data[0])) ? "<tr height=25><th width=150 style='border-style: ridge;'>Data</th><td align='left' style='border-style: ridge;'>".json_encode(json_decode(hex2bin($data[0])), JSON_PRETTY_PRINT)."</td></tr>" : "";
		$printDetails .= "</table><br/></div>";
		return $printDetails;
	}


	/**
	*** Prints elements of an array in vertical format
	*/
	function printArray($arr, $lvl=0)
	{
		$ret_str = "";

		foreach($arr as $item=>$value)
		{
			$str = "";		

			for ($i = 0; $i <= $lvl; $i++)
			{
					$str= $str."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}

			$item_name = (is_numeric($item) || ($item==""))?"":$item.":";

			//echo gettype($value);
			if(gettype($value)=="array")
			{
					$ret_str .= "<br/>".$str.$item_name."<br/>";
					$ret_str .= printArray($value, $lvl+1);
					$ret_str .= "<br/>";
			}
			else
			{
					$ret_str .= $str.$item_name."&nbsp;&nbsp;".$value."<br/>";
			}

		}

		return $ret_str;

	}
?>