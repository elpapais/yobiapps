<?php
    session_start();
    ob_start();
    require_once('check-login.php');
	include_once 'header-logged-in.php';
?>

<script type="text/javascript" src="js/contract.js"></script>

<div class="container theme-showcase" role="main">

    <header class="page-header">
        <h2>PrimeContract</h2>
    </header>

    <div class="row">
        <div class="col-md-10">
            <section class="panel panel-primary">
            
                <header class="panel-heading">
                    <h2 class="panel-title">Sign Contract</h2>
                </header>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-11">
                            
                            <input type="hidden" id="contract_id">

                            <div id="output_sign1">
                                 <?php
                                    if (isset($_GET['msg']))
                                    {
                                        if ($_GET['msg'] == 1) { echo "<h3 style='color:green'>Signing successful!</h3>"; }
                                    }
                                 ?>
                            </div>

                            <?php
                                include_once 'dbhelper.php';
                                include_once 'resources.php';

                                $dbHelper = new DBHelper(session_id(), $_SERVER);
                                $pendingContractsDetails = $dbHelper->getContractsPendingSignature($_SESSION['address']);

                                if (count($pendingContractsDetails)>0)
                                {

                                    echo "<h3 style='color:#0066cc'>Contracts pending signature:</h3>";

                                    echo "<p><div class='table-responsive scrollable has-scrollbar scrollable-content ' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";
                                    echo "<tr><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['title']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['desc']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['dou']."</th><th></th><th></th></tr>";

                                    foreach ($pendingContractsDetails as $contractDetails)
                                    {
                                        echo "<tr>";

                                        foreach ($contractDetails as $key => $value)
                                        {
                                            if ($key != Literals::CONTRACT_DETAILS_FIELD_NAMES['FILE_HASH'] && $key != Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']) {
                                                echo "<td>".$value."</td>";
                                            }
                                        }

                                        echo "<td><a class='mb-xs mt-xs mr-xs btn btn-primary' target='_new' href='contract_upload_details.php?contractid=".$contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']]."'>View</a></td>";

                                        echo "<td><button class='mb-xs mt-xs mr-xs btn btn-primary' onclick='contract_id.value=\"".$contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']]."\";signContract(this, contract_id, output_sign1); return false;'>Sign</button></td>";

                                        echo "</tr>";
                                    }

                                    echo "</table></div></p>";
                                }
                                else
                                {
                                    echo "<h3 style='color:green'><b>No contracts pending signature</b></h3>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>

    </div>

</div>

<?php
    include_once 'footer.php';
?>