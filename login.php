<?php
	include_once 'header.php';
?>
		<section class="body-sign">
			<div class="col-md-4"></div>

			<div class="center-sign col-md-4">

				<section class="panel panel-primary">
	                <header class="panel-heading">
	                    <div class="panel-actions">
	                        <a href="#" class="panel-action panel-action-toggle" data-panel-toggle></a>
	                        <!-- <a href="#" class="panel-action panel-action-dismiss" data-panel-dismiss></a> -->
	                    </div>

	                    <h2 class="panel-title">SIGN IN</h2>
	                </header>
					<div class="panel-body">

						<?php 
                            if(isset($_GET['msg']))
                            {

                                if ($_GET['msg'] == '1') { echo "<div class='alert alert-danger'><strong>Error! Invalid username / password</strong>.</div>";}
                                if ($_GET['msg'] == '2') { echo "<div class='alert alert-danger'><strong>Error! Please login</strong>.</div>";}
                                if ($_GET['msg'] == '3') { echo "<div class='alert alert-danger'><strong>Incorrect OTP</strong>.</div>";}
                                if ($_GET['msg'] == '4') { echo "<div class='alert alert-danger'><strong>Your account is inactive/expired !!</strong>.</div>";}
                                if ($_GET['msg'] == '5') { echo "<div class='alert alert-info'><strong>Logged out successfully</strong>.</div>";}
                                if ($_GET['msg'] == '6') { echo "<div class='alert alert-success'><strong>Your user account has been activated successfully.</strong></div>";}

                            }
                        ?>
                        
						<form action="verify-login.php" method="post">
							<div class="form-group mb-lg">
								<label>Username</label>
								<div class="input-group input-group-icon">
									<input id="username" name="username" type="text" class="form-control input-lg" required="true" />
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-user"></i>
										</span>
									</span>
								</div>
							</div>

							<div class="form-group mb-lg">
								<div class="clearfix">
									<label class="pull-left">Password</label>
									<!--<a href="recover-password.php" class="pull-right">Lost Password?</a>-->
								</div>
								<div class="input-group input-group-icon">
									<input id="password" name="password" type="password" class="form-control input-lg" required="true" />
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-lock"></i>
										</span>
									</span>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12 text-center">
									<button type="submit" class="btn btn-primary hidden-xs">Sign In</button>
									<!-- <button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Sign In</button> -->
								</div>
							</div><br/>

							<p class="text-center">Don't have an account yet? <a href="register.php">Sign Up!</a></p>

						</form>
					</div>
				</section>

				<p class="text-center text-muted mt-md mb-md">&copy; Copyright 2017 Primechain Technologies Pvt. Ltd. All Rights Reserved.</p>
			</div>

			<div class="col-md-4"></div>
		</section>

<?php
	include_once 'footer.php';
?>