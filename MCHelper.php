<?php
use src\MultichainClient;
use src\MultichainHelper;
include_once 'src/MultichainHelper.php';
include_once 'src/MultichainClient.php';

class MCHelper
{

	/** @var MultichainClient */
	protected $multichain;

	/** @var  MultichainHelper */
	protected $helper;

	public function setUp($hostName, $rpcPort, $rpcUser, $rpcPassword)
	{
		$this->multichain = new MultichainClient("http://".$hostName.":".$rpcPort, $rpcUser, $rpcPassword, 30);
		$this->helper = new MultichainHelper($this->multichain);
	}

	/**
	 * 	Returns general information about this node and blockchain. MultiChain adds some fields to Bitcoin Core’s
	 * response, giving the blockchain’s chainname, description, protocol, peer-to-peer port. The setupblocks field
	 * gives the length in blocks of the setup phase in which some consensus constraints are not applied. The
	 * nodeaddress can be passed to other nodes for connecting.
	 *
	 * @group info
	 */
	public function GetInfo()
	{
		$info = $this->multichain->setDebug(true)->getInfo();
	}

	/**
	 * Returns information about the other nodes to which this node is connected. If this is a MultiChain blockchain,
	 * includes handshake and handshakelocal fields showing the remote and local address used during the handshaking
	 * for that connection.
	 *
	 * @group info
	 */
	public function GetPeerInfo()
	{
		$peers = $this->multichain->setDebug(true)->getPeerInfo();
		return $peers;
	}

	/**
	 * Returns a new address for receiving payments. Omit the account parameter for the default account – see note
	 * about accounts.
	 *
	 * @group address
	 */
	public function GetNewAddress()
	{
		$address = $this->multichain->setDebug(true)->getNewAddress();
		return $address;
	}

	/**
	 * Outputs a list of available API commands, including MultiChain-specific commands.
	 *
	 * @group info
	 */
	public function Help()
	{
		$help = $this->multichain->setDebug(true)->help();
	}

	/**
	 * Sends one or more assets to address, returning the txid. In Bitcoin Core, the amount field is the quantity of
	 * the bitcoin currency. For MultiChain, an {"asset":qty, ...} object can be used for amount, in which each asset
	 * is an asset name, ref or issuance txid, and each qty is the quantity of that asset to send (see native assets).
	 * Use "" as the asset inside this object to specify a quantity of the native blockchain currency. See also
	 * sendassettoaddress for sending a single asset and sendfromaddress to control the address whose funds are used.
	 *
	 * @group transaction
	 */
	public function SendToAddress($toAddress, $qty, $assetName=""){

		return $this->multichain->setDebug(true)->sendToAddress($address2, array($assetName => $qty));
	}

	/**
	 * Sends one or more assets to address, returning the txid. In Bitcoin Core, the amount field is the quantity of
	 * the bitcoin currency. For MultiChain, an {"asset":qty, ...} object can be used for amount, in which each asset
	 * is an asset name, ref or issuance txid, and each qty is the quantity of that asset to send (see native assets).
	 * Use "" as the asset inside this object to specify a quantity of the native blockchain currency.
	 *
	 * @group transaction
	 */
	public function SendFromAddress($fromAddress, $toAddress, $qty, $assetName="", $comment = '', $commentTo = ''){

		return $this->multichain->setDebug(true)->sendFromAddress($fromAddress, $toAddress, array($assetName => $qty), $comment, $commentTo);
	}

	/**
	 * @param $address
	 * @return mixed
	 */
	private function createTestAsset($address){
		$name = uniqid("asset");
		$issueQty = 1000000;
		$units = 0.01;
		$assetTxId = $this->multichain->issue($address, $name, $issueQty, $units);
		// before the asset is usable, we need to wait a while
		$this->helper->waitForAssetAvailability($assetTxId);
		return $this->helper->getAssetInfoFromTxId($assetTxId);
	}

	/**
	 * Returns information about address including a check for its validity.
	 *
	 * @group address
	 */
	public function ValidateAddress($address)
	{
		return $this->multichain->setDebug(true)->validateAddress($address);
	}


	/**
	 * Returns a list of permissions for the specified/all addresses in the blockchain.
	 *
	 * @group permission
	 */
	public function ListPermissions($permissions="all", $addresses="*", $verbose="true")
	{
		return $this->multichain->setDebug(true)->listPermissions($permissions, $addresses, $verbose);
	}


	/**
	 * Adds to the atomic exchange transaction in hexstring given by a previous call to createrawexchange or
	 * appendrawexchange. This adds an offer to exchange the asset/s in output vout of transaction txid for qty units
	 * of asset, where asset is an asset name, ref or issuance txid. The txid and vout should generally be taken from
	 * the response to preparelockunspent or preparelockunspentfrom. Multiple items can be specified within the fourth
	 * parameter to request multiple assets. Returns a raw hexadecimal transaction in the hex field alongside a
	 * complete field stating whether the exchange is complete (i.e. balanced) or not. If complete, the transaction
	 * can be transmitted to the network using sendrawtransaction. If not, it can be passed to a further counterparty,
	 * who can call decoderawexchange and appendrawexchange as appropriate.
	 *
	 * @group exchange
	 */
	public function AppendRawExchange(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo = $this->createTestAsset($address1);
		$lock = $this->multichain->prepareLockUnspent(array($assetInfo["name"] => 123));
		$hexString = $this->multichain->createRawExchange($lock["txid"], $lock["vout"], array($assetInfo["name"] => 100));
		$this->multichain->setDebug(true)->appendRawExchange($hexString, $lock["txid"], $lock["vout"], array($assetInfo["name"] => 10));
	}

	/**
	 * Creates a new atomic exchange transaction which offers to exchange the asset/s in output vout of transaction
	 * txid for qty units of asset, where asset is an asset name, ref or issuance txid. The txid and vout should
	 * generally be taken from the response to preparelockunspent or preparelockunspentfrom. Multiple items can be
	 * specified within the third parameter to request multiple assets. Returns a raw partial transaction in
	 * hexadecimal which can be passed to the counterparty, who can call decoderawexchange and appendrawexchange
	 * as appropriate.
	 *
	 * @group exchange
	 */
	public function CreateRawExchange(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo1 = $this->createTestAsset($address1);
		$assetInfo2 = $this->createTestAsset($address1);
		$lock = $this->multichain->prepareLockUnspent(array($assetInfo1["name"] => 123));
		$this->multichain->setDebug(true)->createRawExchange($lock["txid"], $lock["vout"], array($assetInfo2["name"] => 100));
	}

	/**
	 * Decodes the raw exchange transaction in hexstring, given by a previous call to createrawexchange or
	 * appendrawexchange. Returns details on the offer represented by the exchange and its present state. The offer
	 * field in the response lists the quantity of native currency and/or assets which are being offered for exchange.
	 * The ask field lists the native currency and/or assets which are being asked for. The candisable field specifies
	 * whether this wallet can disable the exchange transaction by double-spending against one of its inputs. The
	 * cancomplete field specifies whether this wallet has the assets required to complete the exchange. The
	 * complete field specifies whether the exchange is already complete (i.e. balanced) and ready for sending. If
	 * verbose is true then all of the individual stages in the exchange are listed. Other fields relating to fees are
	 * only relevant for blockchains which use a native currency.
	 *
	 * @group exchange
	 */
	public function DecodeRawExchange(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo1 = $this->createTestAsset($address1);
		$assetInfo2 = $this->createTestAsset($address1);
		$lock = $this->multichain->prepareLockUnspent(array($assetInfo1["name"] => 123));
		$hexString = $this->multichain->createRawExchange($lock["txid"], $lock["vout"], array($assetInfo2["name"] => 100));
		$this->multichain->setDebug(true)->decodeRawExchange($hexString, true);

	}

	/**
	 * Sends a transaction to disable the offer of exchange in hexstring, returning the txid. This is achieved by
	 * spending one of the exchange transaction’s inputs and sending it back to the wallet. To check whether this can
	 * be used on an exchange transaction, check the candisable field of the output of decoderawexchange.
	 *
	 * @group exchange
	 */
	public function DisableRawTransaction(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo1 = $this->createTestAsset($address1);
		$assetInfo2 = $this->createTestAsset($address1);
		$lock = $this->multichain->prepareLockUnspent(array($assetInfo1["name"] => 123));
		$hexString = $this->multichain->createRawExchange($lock["txid"], $lock["vout"], array($assetInfo2["name"] => 100));
		$this->multichain->setDebug(true)->disableRawTransaction($hexString);
	}

	/**
	 * Prepares an unspent transaction output (useful for building atomic exchange transactions) containing qty units
	 * of asset, where asset is an asset name, ref or issuance txid. Multiple items can be specified within the first
	 * parameter to include several assets within the output. The output will be locked against automatic selection for
	 * spending unless the optional lock parameter is set to false. Returns the txid and vout of the prepared output.
	 *
	 * @group exchange
	 */
	public function PrepareLockUnspent(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo = $this->createTestAsset($address1);
		$this->multichain->setDebug(true)->prepareLockUnspent(array($assetInfo["name"] => 123));
	}

	/**
	 * Adds a metadata output to the raw transaction in tx-hex given by a previous call to createrawtransaction. The
	 * metadata is specified in data-hex in hexadecimal form and added in a new OP_RETURN transaction output. The
	 * transaction can then be signed and transmitted to the network using signrawtransaction and sendrawtransaction.
	 *
	 * @group metadata
	 */
	public function AppendRawMetadata(){
		$address1 = $this->multichain->getNewAddress();
		$assetInfo1 = $this->createTestAsset($address1);
		$assetInfo2 = $this->createTestAsset($address1);
		$lock = $this->multichain->prepareLockUnspent(array($assetInfo1["name"] => 123));
		$txHex = $this->multichain->createRawExchange($lock["txid"], $lock["vout"], array($assetInfo2["name"] => 100));
		$this->multichain->setDebug(true)->appendRawMetadata($txHex, "fakemetadata");
	}



	/**
	 * Sends transactions to combine large groups of unspent outputs (UTXOs) belonging to the same address into a
	 * single unspent output, returning a list of txids. This can improve wallet performance, especially for miners in
	 * a chain with short block times and non-zero block rewards. Set addresses to a comma-separated list of addresses
	 * to combine outputs for, or * for all addresses in the wallet. Only combine outputs with at least minconf
	 * confirmations, and use between mininputs and maxinputs per transaction. A single call to combineunspent can
	 * create up to maxcombines transactions over up to maxtime seconds. See also the autocombine runtime parameters.
	 *
	 * @group operations
	 */
	public function CombineUnspent(){
		//$this->multichain->setDebug(true)->combineUnspent();
	}

	/**
	 * Returns a list of all the asset balances for address in this node’s wallet, with at least minconf confirmations.
	 * Use includeLocked to include unspent outputs which have been locked, e.g. by a call to preparelockunspent.
	 *
	 * @group address
	 */
	public function GetAddressBalances($address) {
		//$address1 = $this->multichain->getNewAddress();
		//$this->createTestAsset($address1);
		return $this->multichain->setDebug(true)->getAddressBalances($address);
	}

	/**
	* Returns information about address including a check for its validity.
	*
	* @group address
	*/
	public function GetNativeCurrencyBalanceForAddress($address) {
		$assetsBalances = $this->GetAddressBalances($address);
		foreach($assetsBalances as $assetBalance)
		{
			if($assetBalance["assetref"] == "")
				return $assetBalance["qty"];
		}
	}

	/**
	* Gets asset balances for address, by asset reference.
	*
	*/
	public function GetAssetBalanceForAddressByAssetRef($address, $assetRef) {
		$assetsBalances = $this->GetAddressBalances($address);
		foreach($assetsBalances as $assetBalance)
		{
			if($assetBalance["assetref"] == $assetRef)
				return $assetBalance["qty"];
		}

		return 0;
	}

	/**
	* Gets asset balances for address, by asset reference.
	*
	*/
	public function GetAssetBalanceForAddressByAssetName($address, $assetName) {
		$assetsBalances = $this->GetAddressBalances($address);
		foreach($assetsBalances as $assetBalance)
		{
			if($assetBalance["name"] == $assetName)
				return $assetBalance["qty"];
		}

		return 0;
	}


	/**
	 * Returns a list of addresses in this node’s wallet. Set verbose to true to get more information about each
	 * address, formatted like the output of the validateaddress command.
	 *
	 * @group address
	 */
	public function GetAddresses()
	{
		return $this->multichain->setDebug(true)->getAddresses(true);
	}

    /**
     * Imports an address from another wallet for WatchOnly purpose.
     * set rescan=true if you want to scan the blockchain to get the past transactions for the address.
     *
     * @param $address
     * @param int $label
     * @param bool $rescan
     * @return mixed
     */
    public function ImportAddress($address, $label = "", $rescan = true)
    {
        return $this->multichain->setDebug(true)->importAddress($address, $label, $rescan);
    }

	/**
	 * Provides information about transaction txid related to address in this node’s wallet, including how it affected
	 * that address’s balance. Use verbose to provide details of transaction inputs and outputs.
	 *
	 * @group address
	 * @group transaction
	 */
	public function GetAddressTransaction($address, $txId){
		return $this->multichain->setDebug(true)->getAddressTransaction($address, $txId, true);
	}

	 public function ListAddressTransactions($address, $count = 100, $skip = 0, $verbose = false){
		return $this->multichain->setDebug(true)->listAddressTransactions($address, $count = 100, $skip = 0, $verbose = false);
	}
   

	/**
	 * Returns a list of all the asset balances for account in this node’s wallet, with at least minconf confirmations.
	 * Omit the account parameter or use "" for the default account – see note about accounts. Use includeWatchOnly to
	 * include the balance of watch-only addresses and includeLocked to include unspent outputs which have been locked,
	 * e.g. by a call to preparelockunspent.
	 *
	 * @group 
	 */
	public function GetAssetBalances(){
		$this->multichain->setDebug(true)->getAssetBalances();
	}

	/**
	 * Returns a list of all the parameters of this blockchain, reflecting the content of its params.dat file.
	 *
	 * @group info
	 */
	public function GetBlockchainParams()
	{
		$this->multichain->setDebug(true)->getBlockchainParams();
	}
 
	/**
	 * Creates a new asset name on the blockchain, sending the initial qty units to address. The smallest transactable
	 * unit is given by units, e.g. 0.01. If the chain uses a native currency, you can send some with the new asset
	 * using the native-amount parameter. Returns the txid of the issuance transaction. For more information, see
	 * native assets.
	 *
	 * @group asset
	 */
	public function Issue()
	{
		$address = $this->multichain->getNewAddress();
		$name = uniqid("asset");
		$issueQty = 1000000;
		$units = 0.01;
		$this->multichain->issue($address, $name, $issueQty, $units);
	}

	/**
	 * Returns information about all assets issued on the blockchain. If an asset name, ref or issuance txid (see
	 * native assets) is provided in asset, then information is only returned about that one asset.
	 *
	 * @group asset
	 */
	public function ListAssets()
	{
		$this->multichain->listAssets();
	}


    /**
     * Provides information about transaction txid in this node’s wallet, including how it affected the node’s total
     * balance. Use includeWatchOnly to consider watch-only addresses as if they belong to this wallet and verbose to
     * provide details of transaction inputs and outputs.
     *
     * @param $txId
     * @param bool $includeWatchOnly
     * @param bool $verbose
     * @return mixed
     */
	public function GetWalletTransaction($txId, $includeWatchOnly = false, $verbose = false)
    {
		return $this->multichain->getWalletTransaction($txId, $includeWatchOnly, $verbose);
	}


	/**
	 * Lists information about the count most recent transactions in this node’s wallet, including how they affected
	 * the node’s total balance. Use skip to go back further in history and includeWatchOnly to consider watch-only
	 * addresses as if they belong to this wallet. Use verbose to provide the details of transaction inputs and
	 * outputs. Note that unlike Bitcoin Core’s listtransactions command, the response contains one element per
	 * transaction, rather than one per transaction output.
	 *
	 * @group transaction
	 */
	public function ListWalletTransactions()
	{
		return $this->multichain->listWalletTransactions(50, 0, false, false);
	}

	public function ListWalletTransactionsByCount($count)
	{
		return $this->multichain->listWalletTransactions($count, 0, false, false);
	}


	/**
	 * This works like sendtoaddress (listed above), but includes the data-hex hexadecimal metadata in an additional
	 * OP_RETURN transaction output.
	 *
	 * @param $address
	 * @param $amount
	 * @param $dataHex
	 * @return mixed
	 */
	public function SendWithMetadata($dataHex, $toAddress, $amount=1)
	{
		 return $this->multichain->setDebug(true)->sendWithMetadata($toAddress, $amount, $dataHex);
	}


		/**
	 * This works like sendtoaddress (listed above), but includes the data-hex hexadecimal metadata in an additional
	 * OP_RETURN transaction output.
	 *
	 * @param $address
	 * @param $amount
	 * @param $dataHex
	 * @return mixed
	 */
	public function SendWithMetadataFrom($dataHex, $fromAddress, $toAddress, $qty=0, $assetRef="")
	{
		 return $this->multichain->setDebug(true)->sendWithMetadataFrom($fromAddress, $toAddress, array($assetRef => $qty), $dataHex);
	}


	/**
	*** Searches for transactions by metadata in an existing array of transactions.
	*/
	public function listTransactionsByMetadata($metadata)
	{
		$walletTransactions = $this->ListWalletTransactionsByCount(1000);
		$arr_fin = array();
		foreach ($walletTransactions as $item=>$arr1)
		{

			foreach ($arr1 as $item1=>$value1)
			{
				
				if($item1=="data" && in_array($metadata, $value1))
				{
					array_push($arr_fin, $arr1);
				}
			}
		}

		return $arr_fin;

	}

	
	public function GetTxOutData($txId, $vOut)
    {
        return $this->multichain->setDebug(true)->getTxOutData($txId, $vOut);
    }


	public function SignMessage($address, $message)
    {
        return $this->multichain->setDebug(true)->signMessage($address, $message);
    }


	public function VerifyMessage($address, $signature, $message)
    {
        return $this->multichain->setDebug(true)->verifyMessage($address, $signature, $message);
    }


	public function Grant($addresses, $permissions, $nativeAmount = 0, $comment = '', $commentTo = '', $startBlock = 0, $endBlock = null)
    {
    	return $this->multichain->setDebug(true)->grant($addresses, $permissions, $nativeAmount, $comment, $commentTo, $startBlock, $endBlock);
    }


	public function GrantFrom($fromAddress, $addresses, $permissions, $nativeAmount = 0, $startBlock = 0, $endBlock = -1, $comment = '', $commentTo = '')
    {
    	return $this->multichain->setDebug(true)->grantFrom($fromAddress, $addresses, $permissions, $nativeAmount, $startBlock, $endBlock, $comment, $commentTo);
    }


    // -------------
    // Streams
    // -------------


    public function Publish($streamID, $key, $dataHex)
    {
        return $this->multichain->setDebug(true)->publish($streamID, $key, $dataHex);
    }


    public function PublishFrom($fromAddress, $streamID, $key, $dataHex)
    {
        return $this->multichain->setDebug(true)->publishFrom($fromAddress, $streamID, $key, $dataHex);
    }


    public function ListStreamItems($streamID, $verbose = true, $count = 10, $start = -10, $localOrdering = false)
    {
        return $this->multichain->setDebug(true)->listStreamItems($streamID, $verbose, $count, $start, $localOrdering);
    }


    public function ListStreamKeys($streamID, $key = "*", $verbose = true, $count = 10, $start = -10, $localOrdering = false)
    {
        return $this->multichain->setDebug(true)->listStreamKeys($streamID, $key, $verbose, $count, $start, $localOrdering);
    }


    public function ListStreamKeyItems($streamID, $key, $verbose = true, $count = 10, $start = -10, $localOrdering = false)
    {
        return $this->multichain->setDebug(true)->listStreamKeyItems($streamID, $key, $verbose, $count, $start, $localOrdering);
    }


    public function ListStreamPublishers($streamID, $address = "*", $verbose = true, $count = 10, $start = -10, $localOrdering = false)
    {
        return $this->multichain->setDebug(true)->listStreamPublishers($streamID, $address, $verbose, $count, $start, $localOrdering);
    }


    public function ListStreamPublisherItems($streamID, $address, $verbose = true, $count = 10, $start = -10, $localOrdering = false)
    {
        return $this->multichain->setDebug(true)->listStreamPublisherItems($streamID, $address, $verbose, $count, $start, $localOrdering);
    }


}
?>