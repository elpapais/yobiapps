<?php
	use src\multichain\MultichainClient as MultichainClient;
	use src\multichain\MultichainHelper as MultichainHelper;
	include_once 'src/MultichainHelper.php';
	include_once 'src/MultichainClient.php';
	include_once 'config.php';
	include_once 'resources.php';
	include_once 'helperFunctions.php';


	/**
	* 
	*/
	class DBHelper
	{		
		protected $mcObj;
		protected $mcHelper;
		protected $sessionId;
		protected $server;
		protected $adminAddress;


		public function __construct($sessionId=null, $server=null)
		{
			$this->sessionId = $sessionId;
			$this->server = $server;

			$this->mcObj = new MultichainClient("http://".MultichainParams::HOST_NAME.":".MultichainParams::RPC_PORT, MultichainParams::RPC_USER, MultichainParams::RPC_PASSWORD, 30);
			$this->mcHelper = new MultichainHelper($this->mcObj);
			$this->adminAddress = $this->getAdminAddress();
		}

		/**
		 *  Get admin address
		 */
		public function getAdminAddress()
		{
			$permissionsInfo = $this->mcObj->setDebug(true)->listPermissions("admin");

			foreach ($permissionsInfo as $permissionItem) {
				$validationInfo = $this->mcObj->setDebug(true)->validateAddress($permissionItem['address']);
				if ($validationInfo['ismine']) {
					return $permissionItem['address'];
				}
			}

			throw new Exception("There is no address with admin privileges in your node!");
		}


		/**
		 *  Get data from the data object of a transaction
		 */
		public function getDataFromDataItem($dataItem)
		{
			if (is_string($dataItem)) {
				$dataHex = $dataItem;
			}
			else{
				$vOut_n = $dataItem['vout'];
				$txId = $dataItem['txid'];
				$dataHex = $this->mcObj->setDebug(true)->getTxOutData($txId, $vOut_n);
			}

			return $dataHex;
		}


		/**
		 *  Get metadata for transaction
		 */
		public function getTransactionMetadata($txId, $vOut)
		{
			return $this->mcObj->setDebug(true)->getTxOutData($txId, $vOut);
		}


		/**
		 *  Get transaction details for an address
		 */
		public function getAddressTransaction($address, $txId)
		{
			return $this->mcObj->setDebug(true)->getAddressTransaction($address, $txId);
		}


		/**
		 *  Get list of transactions for an address
		 */
		public function listAddressTransactions($address, $count = 100, $skip = 0, $verbose = true)
		{
			return $this->mcObj->setDebug(true)->listAddressTransactions($address, $count, $skip, $verbose);
		}


		/**
		 *  Get list of transactions for an address
		 */
		public function signMessage($signerAddress, $fileHash)
		{
			return $this->mcObj->setDebug(true)->signMessage($signerAddress, $fileHash);
		}


	    /**
	     * Get Block details.
	     */
	    public function getBlockDetails($hash, $format = true)
	    {
	        return $this->mcObj->setDebug(true)->getBlock($hash, $format);
	    }


		/**
		* Gets asset balances for address, by asset reference.
		*
		*/
		public function getAssetBalanceForAddressByAssetName($address, $assetName) {
			$assetsBalances = $this->mcObj->setDebug(true)->getAddressBalances($address);
			foreach($assetsBalances as $assetBalance)
			{
				if($assetBalance["name"] == $assetName)
					return $assetBalance["qty"];
			}

			return 0;
		}


		/**
		 *  Get recent vault items for user
		 */
		public function getRecentVaultItemsForUser($address, $count = 10)
		{
			$start = -($count);
			return $this->mcObj->setDebug(true)->listStreamPublisherItems(MultichainParams::VAULT_STREAMS['DATA'], $address, true, $count, $start, true);
		}


		/**
		 *  Check if address is valid
		 */
		public function isAddressValid($address)
		{
			
			$addressInfo = $this->mcObj->setDebug(true)->validateAddress($address);

			return $addressInfo['isvalid'];
		}

		/**
		 *  Check if username exists
		 */
		public function userExists($userName)
		{
			
			$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, true, 1, -1, true);

			if(count($userRecords)>0)
	        {
	            return true;
	        }
	        else
	        {
	            return false;
	        }
		}

		/**
		 *  Creates user
		 */
		public function createUser($userName, $password, $name, $email, $org, $country)
		{
			try 
			{
				$this->createUserCredentials($userName, $password);
				$this->createUserDetails($userName, $name);
				
			}
			catch (Exception $e)
			{
				return false;
			}
		}


		/**
		 *  Creates user's credentials
		 */
		public function createUserCredentials($userName, $password)
		{
			$userCredentialsArray = array(
					Literals::USER_CREDENTIALS_FIELD_NAMES['USER_NAME'] => $userName,
					Literals::USER_CREDENTIALS_FIELD_NAMES['PASSWORD_HASH'] => password_hash($password, PASSWORD_BCRYPT)
				);			

			$txId = $this->mcObj->setDebug(true)->publishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_CREDENTIALS'], $userName, bin2hex(json_encode($userCredentialsArray)));
		}


		/**
		 *  Creates user's details
		 */
		public function createUserDetails($userName, $name, $email, $org, $country)
		{
			$userDetailsArray = array(
					Literals::USER_DETAILS_FIELD_NAMES['USER_NAME'] => $userName,
					Literals::USER_DETAILS_FIELD_NAMES['NAME'] => $name,
					Literals::USER_DETAILS_FIELD_NAMES['EMAIL'] => $email,
					Literals::USER_DETAILS_FIELD_NAMES['ORGANIZATION'] => $org,
					Literals::USER_DETAILS_FIELD_NAMES['COUNTRY'] => $country
				);		

			$txId = $this->mcObj->setDebug(true)->publishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, bin2hex(json_encode($userDetailsArray)));
		}


		/**
		 *  Creates user's details
		 */
		public function createUserAddress($userName)
		{
			if ($this->userExists($userName))
			{
				$address = $this->mcObj->setDebug(true)->getNewAddress();

				$userDetailsArray = array(
						Literals::USER_ADDRESS_FIELD_NAMES['USER_NAME'] => $userName,
						Literals::USER_ADDRESS_FIELD_NAMES['ADDRESS'] => $address
					);		

				$txId = $this->mcObj->setDebug(true)->publishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_ADDRESSES'], $userName, bin2hex(json_encode($userDetailsArray)));
				
				return $address;
			}
			else
			{
				throw new Exception("Invalid user name", 1);				
			}
				
		}


		/**
		 *  Creates session for the user
		 */
		public function createUserSession($userName)
		{
			if ($this->userExists($userName))
			{
				$userSessionArray = array(
						Literals::USER_SESSION_FIELD_NAMES['USER_NAME'] => $userName,
						Literals::USER_SESSION_FIELD_NAMES['SESSION_ID'] => $this->sessionId,
						Literals::USER_SESSION_FIELD_NAMES['SESSION_IP'] => $this->server['REMOTE_ADDR']
					);		

				$txId = $this->mcObj->setDebug(true)->publishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_SESSION'], $userName, bin2hex(json_encode($userSessionArray)));
				
				return $txId;
			}
			else
			{
				throw new Exception("Invalid user name", 1);				
			}
				
		}


		/**
		 *  Grants permissions to user
		 */
		public function hasPermissions($userName ,$permissions)
		{
			try
			{
				if ($this->userExists($userName))
				{
					$address = $this->getUserAddress($userName);

					$permissionsInfo = $this->mcObj->setDebug(true)->listPermissions($permissions, $address);
					
					if (count($permissionsInfo) > 0)
					{
						return true;
					}
					else
					{
						return false;
					}

				}
				else
				{
					throw new Exception("Invalid user name", 1);				
				}
			}
			catch (Exception $e) {
				throw $e;
			}
				
		}


		/**
		 *  Grants permissions to user
		 */
		public function grantPermissions($userName ,$permissions)
		{
			try
			{
				if ($this->userExists($userName))
				{
					$address = $this->getUserAddress($userName);
					
					if ($address===false)
					{
						throw new Exception("Invalid Address!!");
					}

					$txId = $this->mcObj->setDebug(true)->grantFrom($this->getAdminAddress(), $address, $permissions);

				}
				else
				{
					throw new Exception("Invalid user name", 1);				
				}
			}
			catch (Exception $e) {
				throw $e;
			}
				
				
		}

		/**
		 *  Uploads the hex format of a document to blockchain 
		 */
		public function uploadDocumentToVault($address, $streamKey, $contentHex)
		{
			try 
			{
				return $this->mcObj->setDebug(true)->publishFrom($address, MultichainParams::VAULT_STREAMS['DATA'], $streamKey, $contentHex);
				
			}
			catch (Exception $e)
			{
				return false;
			}
		}


		/**
		 *  Send Yobicoins to user
		 */
		public function sendInitYobicoins($userName, $qty=AssetParams::INIT_QTY)
		{
			try
			{
				if ($this->userExists($userName))
				{
					$address = $this->getUserAddress($userName);
					
					if ($address===false)
					{
						throw new Exception("Invalid Address!!");
					}

					$txId = $this->mcObj->setDebug(true)->sendFromAddress($this->getAdminAddress(), $address,  array(AssetParams::ASSET_NAME => $qty));
				}
				else
				{
					throw new Exception("Invalid user name", 1);				
				}
			}
			catch (Exception $e) {
				throw $e;
			}				
				
		}
		

		/**
		 *  Send asset with message to user 
		 */
		public function sendAssetWithMessage($fromAddress, $toAddress, $assetName, $units, $metadata)
		{
			try 
			{
				return $this->mcObj->setDebug(true)->sendWithMetadataFrom($fromAddress, $toAddress, array($assetName => $units), $metadata);
				
			}
			catch (Exception $e)
			{
				return false;
			}
		}


		/**
		 *  Activate User Account
		 */
		public function activateUser($userName)
		{
			try
			{
				$activationCode = strval(randomNDigitNumber(8));

				$userActivationDetailsArray = array(
						Literals::USER_ACCOUNT_STATUS_FIELD_NAMES['USER_NAME'] => $userName,
						Literals::USER_ACCOUNT_STATUS_FIELD_NAMES['ACCOUNT_STATUS'] => 1,
						Literals::USER_ACCOUNT_STATUS_FIELD_NAMES['ACTIVATION_CODE'] => $activationCode
					);		

				$txId = $this->mcObj->setDebug(true)->publishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_ACCOUNTS_STATUSES'], $userName, bin2hex(json_encode($userActivationDetailsArray)));

				return true;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Invite signee for contract
		 */
		public function inviteSignee($contractID, $inviteeID)
		{
			try
			{
				$inviteeAddress = $this->getUserAddress($inviteeID);

				if ($this->isAuthorizedToSignContract($inviteeAddress, $contractID)) {
					return false;
				}				

				$inviteeDetailsArray1 = array(
						Literals::CONTRACT_INVITED_SIGNEES_FIELD_NAMES['INVITEE_ID'] => $inviteeID,
						Literals::CONTRACT_INVITED_SIGNEES_FIELD_NAMES['INVITEE_ADDRESS'] => $inviteeAddress
					);

				$inviteeDetailsArray2 = array(
						Literals::CONTRACT_INVITED_SIGNEES_FIELD_NAMES['INVITEE_ID'] => $inviteeID,
						Literals::CONTRACT_INVITED_SIGNEES_FIELD_NAMES['CONTRACT_ID'] => $contractID
					);		

				$txId1 = $this->mcObj->setDebug(true)->publishFrom($_SESSION['address'], MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $contractID.Literals::STREAM_KEY_DELIMITER.$inviteeAddress, bin2hex(json_encode($inviteeDetailsArray1)));

				$txId2 = $this->mcObj->setDebug(true)->publishFrom($_SESSION['address'], MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $inviteeAddress, bin2hex(json_encode($inviteeDetailsArray2)));

				return true;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Contracts pending signature from user
		 */
		public function hasCreatedTheContract($contractID, $userID)
		{
			
			try
			{
				$userAddress = $this->getUserAddress($userID);
				
				$contractDetailsStreamItems = $this->mcObj->setDebug(true)->listStreamPublisherItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $userAddress, true, 500, -500, true);

				foreach ($contractDetailsStreamItems as $contractDetailsStreamItem)
				{
					if ($contractID == $contractDetailsStreamItem['key']) {
						return true;
					}
					
				}

				return false;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Contracts created by the user
		 */
		public function getContractsCreatedByUser($userID)
		{
			
			try
			{
				$userAddress = $this->getUserAddress($userID);
				$contractsDetails = array();

				$contractDetailsStreamItems = $this->mcObj->setDebug(true)->listStreamPublisherItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $userAddress, true, 500, -500, true);

				foreach ($contractDetailsStreamItems as $contractDetailsStreamItem)
				{
					$contractID = $contractDetailsStreamItem['key'];
					$dataHex = $this->getDataFromDataItem($contractDetailsStreamItem['data']);
					$contractDetailsStreamItemDataArr = json_decode(hex2bin($dataHex), true);

					array_push($contractsDetails, array_merge(array(Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']=>$contractID),$contractDetailsStreamItemDataArr));
				}

				$contractsDetails = array_reverse($contractsDetails);
				return $contractsDetails;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Contracts created by the user
		 */
		public function getContractsSignedByUser($userID)
		{
			
			try
			{
				$userAddress = $this->getUserAddress($userID);
				$contractsDetails = array();

				$contractsSignedStreamItems = $this->mcObj->setDebug(true)->listStreamPublisherItems(MultichainParams::CONTRACT_STREAMS['CONTRACTS_SIGNED'], $userAddress, true, 500, -500, true);

				foreach ($contractsSignedStreamItems as $contractsSignedStreamItem)
				{
					$dataHex = $this->getDataFromDataItem($contractsSignedStreamItem['data']);
					$contractsSignedStreamItemDataArr = json_decode(hex2bin($dataHex), true);
					$contractID = $contractsSignedStreamItemDataArr[Literals::CONTRACTS_SIGNED_FIELD_NAMES['CONTRACT_ID']];
					$contractDetails = $this->getContractDetails($contractID);
					array_push($contractsDetails, $contractDetails);
				}

				$contractsDetails = array_reverse($contractsDetails);
				return $contractsDetails;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Contracts pending signature from user
		 */
		public function getContractsPendingSignature($userAddress)
		{			
			try
			{
				//$userAddress = $this->getUserAddress($userID);
				$pendingContractsDetails = array();

				$invitedContractsStreamItems = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $userAddress, true, 500, -500, true);

				foreach ($invitedContractsStreamItems as $invitedContractsStreamItem)
				{
					$dataHex = $this->getDataFromDataItem($invitedContractsStreamItem['data']);
					$invitedContractsStreamItemDataArr = json_decode(hex2bin($dataHex), true);
					$contractID = $invitedContractsStreamItemDataArr[Literals::CONTRACT_INVITED_SIGNEES_FIELD_NAMES['CONTRACT_ID']];

					if (!$this->hasSignedTheContract($userAddress, $contractID))
					{
						array_push($pendingContractsDetails, array_merge(array(Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']=>$contractID),$this->getContractDetails($contractID)));
					}
				}

				return $pendingContractsDetails;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		
		
		/**
		 *  Validate Contract
		 */
		public function isValidContract($contractID)
		{
			try
			{	
				$contracts = $this->mcObj->setDebug(true)->listStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $contractID, true, 1, -1, true);

				if ($contracts[0]['items'] > 0) {
					return true;
				}

				return false;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Check if the specified user is authorized to sign the contract
		 */
		public function isAuthorizedToSignContract($userAddress, $contractID)
		{
			try
			{
				$invitedSignees = $this->mcObj->setDebug(true)->listStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $contractID.Literals::STREAM_KEY_DELIMITER.$userAddress, true, 1, -1, true);

				if ($invitedSignees[0]['items'] > 0) {
					return true;
				}

				return false;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Check if the specified user has signed the contract already
		 */
		public function hasSignedTheContract($userAddress, $contractID)
		{
			try
			{
				$signees = $this->mcObj->setDebug(true)->listStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES'], $contractID.Literals::STREAM_KEY_DELIMITER.$userAddress, true, 1, -1, true);

				if ($signees[0]['items'] > 0) {
					return true;
				}

				return false;
			}
			catch (Exception $e)
			{
				return false;
			}
		}
		

		/**
		 *  Get contract details
		 */
		public function getContractDetails($contractID)
		{
			try
			{
				$contracts = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $contractID, true, 1, -1, true);

				$contract = $contracts[0];
				$dataHex = $this->getDataFromDataItem($contract['data']);
				$contractDetails = json_decode(hex2bin($dataHex), true);
				return $contractDetails;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		

		/**
		 *  Get signatures for a contract
		 *
		 * @returns Array representing a list of stream items
		 */
		public function getContractSignatures($contractID, $count=100)
		{
			try
			{
				return $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES'], $contractID, true, $count, -($count), true);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		

		/**
		 *  Get contract File stream item
		 *
		 * @returns Array representing a list of stream items
		 */
		public function getContractFileStreamItem($contractID)
		{
			try
			{
				$contractFileItems = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_FILES'], $contractID, true, 1, -1);

            	return $contractFileItems[0];
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		

		/**
		 *  Upload a contract
		 *
		 * @return Transaction ID for the transaction
		 */
		public function uploadContract($contractID, $uploaderAddress, $title, $dateOfUploadStr, $desc, $fileHash, $fileContentHex)
		{
			try
			{
				$contractDetailsArr = array(
					Literals::CONTRACT_DETAILS_FIELD_NAMES['TITLE'] => $title,
					Literals::CONTRACT_DETAILS_FIELD_NAMES['DATE_OF_UPLOAD'] => $dateOfUploadStr,
					Literals::CONTRACT_DETAILS_FIELD_NAMES['DESCRIPTION'] => $desc,
					Literals::CONTRACT_DETAILS_FIELD_NAMES['FILE_HASH'] => $fileHash
				);

				$contractDetailsHex = bin2hex(json_encode($contractDetailsArr));		/// Hex encoding the metadata

				/*$rawTransactionContent = array(
					array("for"=>MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], "key"=>$contractID, "data"=>$contractDetailsHex),
					array("for"=>MultichainParams::CONTRACT_STREAMS['CONTRACT_FILES'], "key"=>$contractID, "data"=>$fileContentHex)
					);*/


				$this->mcObj->setDebug(true)->publishFrom($uploaderAddress, MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $contractID, $contractDetailsHex);	/// Publisher address and stream name to be modified
				$this->mcObj->setDebug(true)->publishFrom($uploaderAddress, MultichainParams::CONTRACT_STREAMS['CONTRACT_FILES'], $contractID, $fileContentHex);

				// $this->mcObj->setDebug(true)->createRawSendFrom($uploaderAddress, (new stdClass()), $rawTransactionContent, "send");

				return true;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		

		/**
		 *  Sign a contract
		 *
		 * @return Transaction ID for the transaction
		 */
		public function signContract($contractID, $signerID, $signerAddress, $signature)
		{
			try
			{
				// To grant permissions to the users created before contracts_signed stream was created.
				if (!$this->hasPermission(MultichainParams::CONTRACT_STREAMS['CONTRACTS_SIGNED']."."."write", $signerID)) {
					$this->grantPermissions($signerID, MultichainParams::CONTRACT_STREAMS['CONTRACTS_SIGNED']."."."write");
				}

				$contractSignaturesStreamKey1 = $contractID;
				$contractSignaturesStreamKey2 = $contractID.Literals::STREAM_KEY_DELIMITER.$signerAddress;
				$contractSignedStreamKey = $signerAddress;

				$contractSignatureArr = array(
					Literals::CONTRACT_SIGNATURES_FIELD_NAMES['SIGNER_ID'] => $signerID,
					Literals::CONTRACT_SIGNATURES_FIELD_NAMES['SIGNER_ADDRESS'] => $signerAddress,
					Literals::CONTRACT_SIGNATURES_FIELD_NAMES['SIGNATURE'] => $signature,
					Literals::CONTRACT_SIGNATURES_FIELD_NAMES['TIMESTAMP'] => date('d-M-Y H:i:s')
				);

				$contractSignatureJSON = json_encode($contractSignatureArr);	/// JSON encoding the array
				$contractSignatureHex = bin2hex($contractSignatureJSON);		/// Hex encoding the JSON data

				$txId = $this->mcObj->setDebug(true)->publishFrom($signerAddress, MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES'], $contractSignaturesStreamKey1, $contractSignatureHex);

				$this->mcObj->setDebug(true)->publishFrom($signerAddress, MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES'], $contractSignaturesStreamKey2, $contractSignatureHex);

				$this->mcObj->setDebug(true)->publishFrom($signerAddress, MultichainParams::CONTRACT_STREAMS['CONTRACTS_SIGNED'], $contractSignedStreamKey, bin2hex(json_encode(array(Literals::CONTRACTS_SIGNED_FIELD_NAMES['CONTRACT_ID'] => $contractID))));

				return $txId;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}


		/**
		 * Gets user's details from Blockchain
		 */
		public function getUserDetails($userName)
		{
			$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, true, 1, -1, true);

			if(count($userRecords)>0)
	        {
	            if (is_string($userRecords[0]['data'])) {
	                $contentHex = $userRecords[0]['data'];
	            }
	            else{
	                $contentHex = $mcObj->setDebug(true)->getTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
	            }

	            $contentArr = json_decode(hex2bin($contentHex), true);
	            return $contentArr;
	        }
	        else
	        {
	            throw new Exception("Cannot find details for this user!", 1);	            
	        }
		}


		/**
		 * Gets user's Public Key from Blockchain using User Name
		 *
		 * @param User ID
		 * @return Public key of the user
		 */
		public function getUserPublicKeyFromUserName($userID)
		{
			$userAddress = $this->getUserAddress($userID);
			$validateAddressResponse = $this->mcObj->setDebug(true)->validateAddress($userAddress);
			return $validateAddressResponse['pubkey'];
		}


		/**
		 * Gets user's Public Key from Blockchain using public address
		 */
		public function getUserPublicKeyFromUserAddress($userAddress)
		{
			$validateAddressResponse = $this->mcObj->setDebug(true)->validateAddress($userAddress);
			return $validateAddressResponse['pubkey'];
		}


		/**
		 * Gets user's credentials from Blockchain
		 */
		public function getUserCredentials($userName)
		{
			try
			{
				$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_CREDENTIALS'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $this->mcObj->setDebug(true)->getTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
		            }

		            $contentArr = json_decode(hex2bin($contentHex), true);
		            return $contentArr;
		        }
		        else
		        {
		            return false;
		        }
			}
			catch (Exception $e)
			{
				throw $e;
			}
				
		}


		/**
		 * Gets user's auth code from Blockchain
		 */
		public function getUserAuthCode($userName)
		{
			try
			{
				$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_AUTH_CODES'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $this->mcObj->setDebug(true)->getTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
		            }

		            $contentArr = json_decode(hex2bin($contentHex), true);
		            return $contentArr[Literals::USER_AUTH_CODE_FIELD_NAMES['AUTH_CODE']];
		        }
		        else
		        {
		            throw new Exception("Unable to verify OTP!", 1);
		        }
			}
			catch (Exception $e)
			{
				throw $e;
			}
				
		}


		/**
		 * Gets user's details from Blockchain
		 */
		public function getUserAddress($userName)
		{
			try
			{
				$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_ADDRESSES'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $this->mcObj->setDebug(true)->getTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
		            }

		            $contentArr = json_decode(hex2bin($contentHex), true);
		            return $contentArr[Literals::USER_ADDRESS_FIELD_NAMES['ADDRESS']];
		        }
		        else
		        {
		            throw new Exception("No address(es) found for this user!", 1);
		        }
			}
			catch (Exception $e)
			{
				throw $e;
			}
				
		}


		/**
		 * Gets user's session details from Blockchain
		 */
		public function getUserSessionDetails($userName)
		{
			$userRecords = $this->mcObj->setDebug(true)->listStreamKeyItems(MultichainParams::USER_STREAMS['USERS_SESSION'], $userName, true, 1, -1, true);

			if(count($userRecords)>0)
	        {
	            if (is_string($userRecords[0]['data'])) {
	                $contentHex = $userRecords[0]['data'];
	            }
	            else{
	                $contentHex = $this->mcObj->setDebug(true)->getTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
	            }

	            $contentArr = json_decode(hex2bin($contentHex), true);
	            return $contentArr;
	        }
	        else
	        {
	            return false;
	        }
		}

	}

?>