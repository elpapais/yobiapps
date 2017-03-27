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
                    <h2 class="panel-title">View Contract</h2>
                </header>
                <div class="panel-body">

                	<div class="row form-group">
                        <label class="col-sm-3 control-label"><strong>Contract ID </strong></label>
                        <div class="col-sm-9">
                            <input id="contractid" name="contractid" class="form-control" placeholder="Title" required="true">
                        </div>
                    </div>
                    <br/>

                    <div class="row">
                        <div class="col-sm-12 text-left">
                            <button type="button" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="getContractDetails(this, contractid, output); return false;">View</button>
                        </div>
                    </div><br/>

                    <div class="progress light m-md" style="width: 100px" hidden="true">
                        <label id="progressLabel" for="progress"></label>
                        <div id="progress" class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" hidden="true" style="width: 100px;">
                            
                        </div>
                    </div>

                    <div class="row">
                        <div id="output" class="col-md-10">
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