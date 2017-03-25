<?php
    session_start();
    ob_start();
    require_once('check-login.php');
    include_once 'header-logged-in.php';
?>

<script type="text/javascript" src="js/vault.js"></script>

<script type="text/javascript">

    function timer() {
        var output_recent = document.getElementById('output_recent');
        getRecentTransactions(null, output_recent);
    }

    window.onload = function(){
        timer();
        setInterval(timer,6000);
    };

</script>

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

                    <h2 class="panel-title">Download document</h2>
                </header>
                <div class="panel-body">
                    <!-- <form action="verify-login.php" method="post"> -->

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><strong>Transaction ID </strong><span class="required">*</span></label>
                            <div class="col-sm-9">
                                    <!-- <input type="text" data-plugin-datepicker class="form-control"> -->
                                    <input class="form-control" type="text" id="txid" name="txid" value="<?php echo (isset($_GET['txid'])) ? $_GET['txid'] : '' ?>" required />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 control-label">
                                <div class="col-sm-11 text-left">
                                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="getAssetDetails(this, txid, output)">View</button>
                                    <!-- <button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Sign In</button> -->
                                </div>
                            </div>
                        </div><br/>

                        <div id="output" class="row">

                        </div>

                    <!-- </form> -->
                </div>
            </section>
        </div>

    </div>

    <div class="row">
        <div class="col-md-10">
            <section class="panel panel-primary">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    </div>

                    <h2 class="panel-title">Recent Transactions</h2>
                </header>
                <div class="panel-body">
                        <div id="output_recent" class="row">
                        </div>
                </div>
            </section>
        </div>

    </div>

</div>

<?php
    include_once 'footer.php';
?>