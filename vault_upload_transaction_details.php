<?php 
    session_start();
    ob_start();
    require_once('check-login.php');
    include ("header-logged-in.php"); 
?>
                        

<script type="text/javascript" src="js/vault.js"></script>

<div class="container theme-showcase" role="main">

    <header class="page-header">
        <h2>PrimeVault</h2>
    </header>
    <div class="row">
        <div class="col-md-10">
            <section class="panel panel-primary">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                        <!-- <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a> -->
                    </div>

                    <h2 class="panel-title">Transaction Details</h2>
                </header>
                <div class="panel-body">
                    <!-- <form action="verify-login.php" method="post"> -->

                        <div id="output" class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-10">

                                <?php

                                    require_once('dbhelper.php');
                                    require_once('resources.php');
                                    require_once('config.php');
                                    require_once('helperFunctions.php');

                                    try
                                    {
                                        if (isset($_GET['txid']))
                                        {
                                            $txId = $_GET['txid'];
                                            $uploader_address = $_SESSION['address'];

                                            $dbHelper = new DBHelper();

                                            $transaction = $dbHelper->getAddressTransaction($uploader_address, $txId);
                                            echo "<h3 style='color:#0066cc'><b><u>Transaction Details</u></b></h3>";
                                            echo printStreamTransactionBasicDetailsVertically($transaction);
                                            echo "<h3 style='color:#0066cc'><b><u>Data</u></b></h3>";

                                            /*foreach ($transaction["vout"] as $value) {
                                                if ($value["type"] == "nulldata") {
                                                    $vOut_n = $value["n"];
                                                }
                                            }*/

                                            $vOut_n = -1;

                                            $dataHex = $dbHelper->getDataFromDataItem($transaction['data'][0]);

                                            $dataArr = json_decode(hex2bin($dataHex));

                                            echo "<p><div class='table-responsive scrollable has-scrollbar scrollable-content ' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";

                                            foreach ($dataArr as $key => $value) {

                                                if ($key!='file_hex') {
                                                    echo "<tr><th style='border-style: ridge;'>".Literals::VAULT_FIELDS_DESC[$key]."</th><td style='border-style: ridge;'>".$value."</td></tr>";
                                                }
                                            }

                                            $downloadFormHTML = "<form action='vault_file_download.php' method='post'>"."<input type='hidden' name='txid' value='".$txId."' />";
                                            $downloadFormHTML .= ($vOut_n != -1) ? "<input type='hidden' name='v_n' value='".$vOut_n."' />" : "";
                                            $downloadFormHTML .= "<input type='submit' class='btn blue' value='Click here' />";

                                            $downloadLinkHTML = "<a target='_new' href='vault_file_download.php?";
                                            $downloadLinkHTML .= "txid=".$txId;
                                            $downloadLinkHTML .= ($vOut_n != -1) ? "&v_n=".$vOut_n : "";
                                            $downloadLinkHTML .= "' class='btn blue'>Click here</a>";

                                            echo "<tr><th style='border-style: ridge;'>"."Download Link"."</th><td style='border-style: ridge;'>".$downloadLinkHTML."</td></tr>";
                                            echo "</table></div></p>";

                                        }
                                        else
                                        {
                                            throw new Exception("No Transaction ID found.");
                                        }
                                    }
                                    catch(Exception $e)
                                    {
                                        echo "<h3 style='color:red'>".$e->getMessage()."</h3>";
                                    }

                                ?>
                            </div>
                            <div class="col-md-1"></div>
                        </div>

                    <!-- </form> -->
                </div>
            </section>
        </div>

    </div>
</section>

<?php include ("footer.php");?>