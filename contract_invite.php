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
                    <h2 class="panel-title">Invite Signees</h2>
                </header>
                <div class="panel-body">
                    <form>

                    	<div class="form-group">
                            <label class="col-sm-3 control-label"><strong>Contract ID </strong></label>
                            <div class="col-sm-9">
                                <input id="contractid" name="contractid" class="form-control" placeholder="Contract ID" value="<?php echo (isset($_GET['contractid'])) ? $_GET['contractid'] : "" ?>" required="true">
                            </div>
                        </div>
                        <br/>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><strong>Enter usernames (seperated by commas)</strong></label>
                            <div class="col-sm-9">
                                <textarea id="invitees" name="invitees" rows="5" class="form-control" placeholder="Enter usernames (seperated by commas)" required></textarea>
                            </div>
                        </div>
                        <br/>

                        <div class="row">
                            <div class="col-sm-12 text-left">
                                <input type="reset" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="inviteSignees(this, contractid, invitees, output);" value="Invite">
                            </div>
                        </div><br/>

                        <div class="row">
                            <div id="output" class="col-md-11">
                            </div>
                        </div>


                    </form>
                </div>
            </section>
        </div>

    </div>

</div>

<?php
    include_once 'footer.php';
?>