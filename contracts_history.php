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
        <div class="col-md-12">
            <section class="panel panel-primary">
                <header class="panel-heading">
                    <h2 class="panel-title">Contracts History</h2>
                </header>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <?php
                                include_once 'dbhelper.php';
                                include_once 'resources.php';

                                $dbHelper = new DBHelper(session_id(), $_SERVER);
                                $createdContractsDetails = $dbHelper->getContractsCreatedByUser($_SESSION['user_name']);
                                $signedContractsDetails = $dbHelper->getContractsSignedByUser($_SESSION['user_name']);

                                if (count($createdContractsDetails)>0)
                                {
                                    echo "<p><h3 style='color:blue'>Contracts created by you:</h3>";
                                    echo "<div class='table-responsive scrollable has-scrollbar scrollable-content ' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";
                                    echo "<tr><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['title']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['dou']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['desc']."</th><th></th><th></th></tr>";

                                    foreach ($createdContractsDetails as $contractDetails)
                                    {
                                        echo "<tr>";

                                        foreach ($contractDetails as $key => $value)
                                        {
                                            if ($key != Literals::CONTRACT_DETAILS_FIELD_NAMES['FILE_HASH'] && $key != Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']) {
                                                echo "<td>".$value."</td>";
                                            }
                                        }

                                        echo "<td><a class='mb-xs mt-xs mr-xs btn btn-primary' target='_new' href='contract_upload_details.php?contractid=".$contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']]."'>View</a></td>";

                                        echo "<td><a class='mb-xs mt-xs mr-xs btn btn-success' target='_new' href='contract_invite.php?contractid=".$contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']]."'>Invite Signees</a></td>";
                                        
                                        echo "</tr>";
                                    }

                                    echo "</table></div></p>";
                                }

                                if (count($signedContractsDetails)>0)
                                {

                                    echo "<p><h3 style='color:green'>Contracts signed by you:</h3>";
                                    echo "<div class='table-responsive scrollable has-scrollbar scrollable-content ' data-plugin-scrollable><table class='table table-bordered table-hover table-condensed mb-none'>";
                                    echo "<tr><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['title']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['dou']."</th><th>".Literals::CONTRACT_DETAILS_FIELD_DESC['desc']."</th><th></th><th></th></tr>";

                                    foreach ($signedContractsDetails as $contractDetails)
                                    {
                                        echo "<tr>";

                                        foreach ($contractDetails as $key => $value)
                                        {
                                            if ($key != Literals::CONTRACT_DETAILS_FIELD_NAMES['FILE_HASH'] && $key != Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']) {
                                                echo "<td>".$value."</td>";
                                            }
                                        }

                                        echo "<td><a class='mb-xs mt-xs mr-xs btn btn-primary' target='_new' href='contract_upload_details.php?contractid=".$contractDetails[Literals::CONTRACT_DETAILS_FIELD_NAMES['CONTRACT_ID']]."'>View</a></td>";
                                        
                                        echo "</tr>";
                                    }

                                    echo "</table></div></p>";
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