<?php

	include_once 'MCHelper.php';
	include_once 'config.php';
	include_once 'resources.php';
	include_once 'helperFunctions.php';


	/**
	* 
	*/
	class DBHelper
	{
		
		protected $mcHelper;
		protected $sessionId;
		protected $server;
		protected $adminAddress;

		public function __construct($sessionId, $server)
		{
			$this->sessionId = $sessionId;
			$this->server = $server;
			$this->mcHelper = new MCHelper();
			$this->mcHelper->setUp(MultichainParams::HOST_NAME, MultichainParams::RPC_PORT, MultichainParams::RPC_USER, MultichainParams::RPC_PASSWORD);
			$this->adminAddress = $this->getAdminAddress();
		}

		/**
		 *  Get admin address
		 */
		public function getAdminAddress()
		{
			$permissionsInfo = $this->mcHelper->ListPermissions("admin");

			foreach ($permissionsInfo as $permissionItem) {
				$validationInfo = $this->mcHelper->ValidateAddress($permissionItem['address']);
				if ($validationInfo['ismine']) {
					return $permissionItem['address'];
				}
			}

			throw new Exception("There is no address with admin privileges in your node!");
		}

		/**
		 *  Check if address is valid
		 */
		public function isAddressValid($address)
		{
			
			$addressInfo = $this->mcHelper->ValidateAddress($address);

			return $addressInfo['isvalid'];
		}

		/**
		 *  Check if username exists
		 */
		public function userExists($userName)
		{
			
			$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, true, 1, -1, true);

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

			$txId = $this->mcHelper->PublishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_CREDENTIALS'], $userName, bin2hex(json_encode($userCredentialsArray)));
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

			$txId = $this->mcHelper->PublishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, bin2hex(json_encode($userDetailsArray)));
		}


		/**
		 *  Creates user's details
		 */
		public function createUserAddress($userName)
		{
			if ($this->userExists($userName))
			{
				$address = $this->mcHelper->GetNewAddress();

				$userDetailsArray = array(
						Literals::USER_ADDRESS_FIELD_NAMES['USER_NAME'] => $userName,
						Literals::USER_ADDRESS_FIELD_NAMES['ADDRESS'] => $address
					);		

				$txId = $this->mcHelper->PublishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_ADDRESSES'], $userName, bin2hex(json_encode($userDetailsArray)));
				
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

				$txId = $this->mcHelper->PublishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_SESSION'], $userName, bin2hex(json_encode($userSessionArray)));
				
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

					$txId = $this->mcHelper->GrantFrom($this->getAdminAddress(), $address, $permissions);

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
		 *  Sends Indiacoins to user
		 */
		public function sendInitIndiacoins($userName, $qty=IndiacoinParams::INIT_QTY)
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

					$txId = $this->mcHelper->SendFromAddress($this->getAdminAddress(), $address, $qty, IndiacoinParams::ASSET_NAME);
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

				$txId = $this->mcHelper->PublishFrom($this->getAdminAddress(), MultichainParams::USER_STREAMS['USERS_ACCOUNTS_STATUSES'], $userName, bin2hex(json_encode($userActivationDetailsArray)));

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

				$txId1 = $this->mcHelper->PublishFrom($_SESSION['address'], MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $contractID.Literals::STREAM_KEY_DELIMITER.$inviteeAddress, bin2hex(json_encode($inviteeDetailsArray1)));

				$txId2 = $this->mcHelper->PublishFrom($_SESSION['address'], MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $inviteeAddress, bin2hex(json_encode($inviteeDetailsArray2)));

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
				
				$contractDetailsStreamItems = $this->mcHelper->ListStreamPublisherItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $userAddress, true, 500, -500, true);

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
		 *  Contracts pending signature from user
		 */
		public function getContractsHistoryForUser($userID)
		{
			
			try
			{
				$userAddress = $this->getUserAddress($userID);
				$contractsDetails = array();

				$contractDetailsStreamItems = $this->mcHelper->ListStreamPublisherItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $userAddress, true, 500, -500, true);

				foreach ($contractDetailsStreamItems as $contractDetailsStreamItem)
				{
					$contractID = $contractDetailsStreamItem['key'];

					if (is_string($contractDetailsStreamItem['data'])) {
						$dataHex = $contractDetailsStreamItem['data'];
					}
					else {
						$vOut = $contractDetailsStreamItem['vout'];
						$txId = $contractDetailsStreamItem['txid'];
						$dataHex = $this->mcHelper->GetTxOutData($txId, $vOut);
					}

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
		 *  Contracts pending signature from user
		 */
		public function getContractsPendingSignature($userAddress)
		{
			
			try
			{
				//$userAddress = $this->getUserAddress($userID);
				$pendingContractsDetails = array();

				$invitedContractsStreamItems = $this->mcHelper->ListStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $userAddress, true, 500, -500, true);

				foreach ($invitedContractsStreamItems as $invitedContractsStreamItem)
				{

					if (is_string($invitedContractsStreamItem['data'])) {
						$dataHex = $invitedContractsStreamItem['data'];
					}
					else {
						$vOut = $invitedContractsStreamItem['vout'];
						$txId = $invitedContractsStreamItem['txid'];
						$dataHex = $this->mcHelper->GetTxOutData($txId, $vOut);
					}

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
				$contracts = $this->mcHelper->ListStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $contractID, true, 1, -1, true);

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
				$invitedSignees = $this->mcHelper->ListStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_INVITED_SIGNEES'], $contractID.Literals::STREAM_KEY_DELIMITER.$userAddress, true, 1, -1, true);

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
				$signees = $this->mcHelper->ListStreamKeys(MultichainParams::CONTRACT_STREAMS['CONTRACT_SIGNATURES'], $contractID.Literals::STREAM_KEY_DELIMITER.$userAddress, true, 1, -1, true);

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
				$contracts = $this->mcHelper->ListStreamKeyItems(MultichainParams::CONTRACT_STREAMS['CONTRACT_DETAILS'], $contractID, true, 1, -1, true);

				$contract = $contracts[0];

				if (is_string($contract['data'])) {
					$dataHex = $contract['data'];
				}
				else {
					$vOut = $contract['vout'];
					$txId = $contract['txid'];
					$dataHex = $this->mcHelper->GetTxOutData($txId, $vOut);
				}

				$contractDetails = json_decode(hex2bin($dataHex), true);

				return $contractDetails;
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
			$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_DETAILS'], $userName, true, 1, -1, true);

			if(count($userRecords)>0)
	        {
	            if (is_string($userRecords[0]['data'])) {
	                $contentHex = $userRecords[0]['data'];
	            }
	            else{
	                $contentHex = $mcHelper->GetTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
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
		 */
		public function getUserPublicKeyFromUserName($userID)
		{
			$userAddress = $this->getUserAddress($userID);
			$validateAddressResponse = $this->mcHelper->ValidateAddress($userAddress);
			return $validateAddressResponse['pubkey'];
		}


		/**
		 * Gets user's Public Key from Blockchain using public address
		 */
		public function getUserPublicKeyFromUserAddress($userAddress)
		{
			$validateAddressResponse = $this->mcHelper->ValidateAddress($userAddress);
			return $validateAddressResponse['pubkey'];
		}


		/**
		 * Gets user's credentials from Blockchain
		 */
		public function getUserCredentials($userName)
		{
			try
			{
				$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_CREDENTIALS'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $mcHelper->GetTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
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
				$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_AUTH_CODES'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $mcHelper->GetTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
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
				$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_ADDRESSES'], $userName, true, 1, -1, true);

				if(count($userRecords)>0)
		        {
		            if (is_string($userRecords[0]['data'])) {
		                $contentHex = $userRecords[0]['data'];
		            }
		            else{
		                $contentHex = $mcHelper->GetTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
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
			$userRecords = $this->mcHelper->ListStreamKeyItems(MultichainParams::USER_STREAMS['USERS_SESSION'], $userName, true, 1, -1, true);

			if(count($userRecords)>0)
	        {
	            if (is_string($userRecords[0]['data'])) {
	                $contentHex = $userRecords[0]['data'];
	            }
	            else{
	                $contentHex = $mcHelper->GetTxOutData($userRecords[0]['data']['txid'], $userRecords[0]['data']['vout']);
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