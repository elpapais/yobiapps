<?php
    session_start();
    ob_start();
    //require_once('check-login.php');
    include_once 'header-logged-in.php';
?>

<script type="text/javascript" src="js/vault.js"></script>

<div class="container theme-showcase" role="main">

    <h2>PrimeVault</h2>
    <br>
    <div class="row">
        <div class="col-md-10">
            <section class="panel panel-primary">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
                    </div>

                    <h2 class="panel-title">Upload document</h2>
                </header>
                <div class="panel-body">
                    <form>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="filebutton">Date of Upload</label>
                            <div class="col-md-8">
                                <div class="input-group col-md-6">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <input class="form-control" type="datetime-local" id="dou" name="dou" value="<?php date_default_timezone_set('Asia/Kolkata'); echo str_replace("/", "T", date('Y-m-d/H:i:s')); ?>" required /> 
                                </div>
                            </div>
                        </div>
                        <br/><br/><br/>

                        <div class="form-group">
                            <label class="col-md-4 control-label" for="file">File</label>
                            <div class="col-md-8">
                                <input id="file" name="filename" class="input-file" type="file" accept="image/*,application/pdf">
                            </div>
                        </div>
                        <br/><br/>
                        
                        <div class="form-group">
                            <label class="col-md-4 control-label" for="desc">Description</label>
                            <div class="col-md-8">
                                <textarea id="desc" name="desc" rows="5" class="form-control" placeholder="Description" required></textarea>
                            </div><br/><br/><br/>
                        </div>
                        <br/><br/><br/>

                        <div class="form-group">
                            <div class="col-md-4">
                                <!-- <button id="singlebutton" name="singlebutton" class="btn btn-primary">Upload</button> -->
                                <input type="reset" class="mb-xs mt-xs mr-xs btn btn-primary" onclick="uploadFile(dou, file, desc, this, output);" value="Upload">
                            </div>
                        </div>

                        <div class="progress light m-md" style="width: 50%" hidden="true">
                            <label id="progressLabel" for="progress"></label>
                            <div id="progress" class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 50px;">
                                Upload progress
                            </div>
                        </div>

                        <div class="form-group">
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