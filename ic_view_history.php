<?php
    session_start();
    ob_start();
    include("check-login.php");
    include_once 'header-logged-in.php';
?>

<script type="text/javascript" src="js/indiacoin.js"></script>

<script type="text/javascript">

    function timer() {
        getTransactionsHistory('hWalletAddress', 'divoutput');
    }

    window.onload = function(){ timer(); setInterval(timer, 10000); };

</script>

<div class="container theme-showcase" role="main">

    <h2>Yobi-Wallet</h2>
    <br>

    <div class="row">
        <div class="col-md-12">
            <section class="panel panel-primary">
                <header class="panel-heading">
                    <h2 class="panel-title">Recent Transactions</h2>
                </header>
                <div class="panel-body">
                        <?php
                            $address = isset($_SESSION['address'])?$_SESSION['address']:"";
                            echo "<input type='hidden' id='hWalletAddress' value='".$address."' />";
                        ?>

                        <div id="divloader" class="row appear-animation fadeIn appear-animation-visible"></div>
                        
                        <div class="row">
                            <!-- <div class="col-md-1"></div> -->
                            <div id="divoutput" class="col-md-12 appear-animation fadeIn appear-animation-visible"></div>
                            <!-- <div class="col-md-1"></div> -->
                        </div>
                </div>
            </section>
        </div>

    </div>

</div>

<?php
    include_once 'footer.php';
?>