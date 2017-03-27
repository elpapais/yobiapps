<?php
    session_start();
    ob_start();
    require_once('check-login.php');
    require_once('resources.php');
    require_once('config.php');
    require_once('dbhelper.php');
    require_once('helperFunctions.php');

    try
    {
        if (isset($_GET['contractid']))
        {
            $dbHelper = new DBHelper();
            $contractID = $_GET['contractid'];
            $uploader_address = $_SESSION['address'];


    /// -------------------------CONTRACT DETAILS------------------------ ///

            echo "<h3 style='color:#0066cc'><b><u>Contract Details</u></b></h3>";

            $dataArr = $dbHelper->getContractDetails($contractID);

            echo "<p><div class='table-responsive scrollable has-scrollbar scrollable-content ' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";

            echo "<tr><th style='border-style: ridge'>"."Contract ID"."</th><td style='border-style: ridge;'>".$contractID."</td></tr>";

            foreach ($dataArr as $key => $value) {

                if ($key!='file_hex') {
                    echo "<tr><th style='border-style: ridge;'>".Literals::CONTRACT_DETAILS_FIELD_DESC[$key]."</th><td style='border-style: ridge;'>".$value."</td></tr>";
                }
            }


            $contractFileItem = $dbHelper->getContractFileStreamItem($contractID);
            $vOut_n = $contractFileItem['vout'];
            $fileTxId = $contractFileItem['txid'];
            $publisher = $contractFileItem['publishers'][0];

            // $downloadFormHTML = "<form action='vault_file_download.php' method='post'>"."<input type='hidden' name='txid' value='".$txId."' />";
            // $downloadFormHTML .= ($vOut_n != -1) ? "<input type='hidden' name='v_n' value='".$vOut_n."' />" : "";
            // $downloadFormHTML .= "<input type='submit' class='btn blue' value='Click here' />";

            $downloadLinkHTML = "<a target='_new' href='contract_file_download.php?";
            $downloadLinkHTML .= "txid=".$fileTxId;
            $downloadLinkHTML .= ($vOut_n != -1) ? "&v_n=".$vOut_n : "";
            $downloadLinkHTML .= "&publisher=".$publisher;
            $downloadLinkHTML .= "' class='mb-xs mt-xs mr-xs btn btn-success'>Download file</a>";

            echo "<tr>";

            if ($dbHelper->hasCreatedTheContract($contractID, $_SESSION['user_name']))
            {
                echo "<td colspan=2 style='border-style: ridge;'>".$downloadLinkHTML."&nbsp;&nbsp;<a class='mb-xs mt-xs mr-xs btn btn-primary' href='contract_invite.php?contractid=".$contractID."'>Invite Signees</a></td>";
            }
            else
            {
                echo "<td colspan=2 style='border-style: ridge;'>".$downloadLinkHTML."</td>";
            }

            echo "</tr>";
            echo "</table></div></p>";

    /// ----------------------------------------------------------------- ///


    /// -------------------------CONTRACT SIGNERS------------------------ ///

            $contractSignersItems = $dbHelper->getContractSignatures($contractID);
            
            echo "<h3 style='color:#0066cc'><b><u>Signers</u></b></h3>";

            foreach ($contractSignersItems as $contractSignersItem)
            {

                echo "<div class='table-responsive scrollable has-scrollbar scrollable-content' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";

                $dataHex = $dbHelper->getDataFromDataItem($contractSignersItem['data']);

                $dataArr = json_decode(hex2bin($dataHex));

                foreach ($dataArr as $key => $value) {

                    echo "<tr>";
                    echo "<th style='border-style: ridge'>".Literals::CONTRACT_SIGNATURES_FIELD_DESC[$key]."</th>";
                    echo "<td style='border-style: ridge'>".$value."</td>";
                    echo "</tr>";

                    if ($key == Literals::CONTRACT_SIGNATURES_FIELD_NAMES['SIGNER_ADDRESS']) {
                        echo "<tr>";
                        echo "<th style='border-style: ridge'>"."Public Key"."</th>";
                        echo "<td style='border-style: ridge'>".$dbHelper->getUserPublicKeyFromUserAddress($value)."</td>";
                        echo "</tr>";
                    }
                }

                echo "</table></p>";
            }

            

    /// ----------------------------------------------------------------- ///

        }
        else
        {
            throw new Exception("Invalid Contract ID.");
        }
    }
    catch(Exception $e)
    {
        echo "<h3 style='color:red'>".$e->getMessage()."</h3>";
    }

    ob_end_flush();
?>