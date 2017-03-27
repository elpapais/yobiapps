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
                    <h2 class="panel-title">Create Contract</h2>
                </header>
                <div class="panel-body">
                    <form>

                    	<div class="row form-group">
                            <label class="col-sm-3 control-label"><strong>Title </strong></label>
                            <div class="col-sm-9">
                                <input id="contract_title" name="contract_title" class="form-control" placeholder="Title" required="true">
                            </div>
                        </div>
                        <br/>

                        <div class="row form-group">
                            <label class="col-sm-3 control-label"><strong>Date of Upload </strong><span class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input class="form-control" type="datetime-local" id="dou" name="dou" value="<?php date_default_timezone_set('Asia/Kolkata'); echo str_replace("/", "T", date('Y-m-d/H:i:s')); ?>" required /> 
                                </div>
                            </div>
                        </div>
                        <br/>

                        <div class="row form-group">
                            <label class="col-sm-3 control-label"><strong>File Upload </strong> <span class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                        <div class="input-append">
                                            <div class="uneditable-input">
                                                <i class="fa fa-file fileupload-exists"></i>
                                                <span class="fileupload-preview"></span>
                                            </div>
                                            <span class="btn btn-default btn-file">
                                                <span class="fileupload-exists">&nbsp;</span>
                                                <span class="fileupload-new">&nbsp;</span>
                                                <input id="file" type="file" name="filename" accept="image/*,application/pdf" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>

                        <div class="row form-group">
                            <label class="col-sm-3 control-label"><strong>Description </strong></label>
                            <div class="col-sm-9">
                                <textarea id="desc" name="desc" rows="5" class="form-control" placeholder="Description" required></textarea>
                            </div>
                        </div>
                        <br/>

                        <div class="row form-group">
                            <div class="col-sm-12 text-left">
                                <input type="reset" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="uploadFile(this, contract_title, dou, file, desc, output);" value="Upload">
                            </div>
                        </div><br/>

                        <div class="row progress light m-md" style="width: 25%" hidden="true">
                            <label id="progressLabel" for="progress"></label>
                            <div id="progress" class="row progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                Upload progress
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-sm-12 text-left">
                                <div id="output">

                                </div>
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