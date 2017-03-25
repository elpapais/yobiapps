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

	                    <h2 class="panel-title">SIGN UP</h2>
	                </header>
					<div class="panel-body">
						
						<!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
						<?php 
                            if(isset($_GET['msg']))
                            {
                                if ($_GET['msg'] == '1') { echo "<div class='alert alert-danger'><strong>Error! Invalid email!!</strong>.</div>";}
                                if ($_GET['msg'] == '2') { echo "<div class='alert alert-danger'><strong>Error! Passwords do not match!!</strong>.</div>";}
                                if ($_GET['msg'] == '3') { echo "<div class='alert alert-danger'><strong>Invalid Username!!</strong>.</div>";}
                                if ($_GET['msg'] == '4') { echo "<div class='alert alert-danger'><strong> Invalid Password!!</strong>.</div>";}
                                if ($_GET['msg'] == '5') { echo "<div class='alert alert-danger'><strong>Username already exists</strong>.</div>";}
                                if ($_GET['msg'] == '6') { echo "<div class='alert alert-danger'><strong>Invalid name!!</strong>.</div>";}
                                if ($_GET['msg'] == '7') { echo "<div class='alert alert-danger'><strong>An user account for this email id already exists in our database!!</strong>.</div>";}
                                if ($_GET['msg'] == '8') { echo "<div class='alert alert-danger'><strong>Registration Error!! Please try again after sometime or contact the site administrator.</strong>.</div>";}

                            }
                        ?>

						<form method="POST" action="verify-register.php">
							<div>
								<label for="name">Name <span class="required">*</span></label>
								<input id="name" name="name" type="text" class="form-control input-lg" required="true" />
							</div><br/>

							<div>
								<label for="username">User Name <span class="required">*</span></label>
								<input id="username" name="username" type="text" class="form-control input-lg" required="true" />
							</div><br/>

							<div>
								<label for="email">E-mail Address <span class="required">*</span></label>
								<input id="email" name="email" type="email" class="form-control input-lg" required="true" />
							</div><br/>


							<div>
								<label for="org">Organization <span class="required">*</span></label>
								<input id="org" name="org" type="text" class="form-control input-lg" required="true" />
							</div><br/>


							<div>
								<label for="country">Country <span class="required">*</span></label>
								<select id="country" name="country" data-plugin-selectTwo class="form-control populate">
									<?php
										foreach (Literals::COUNTRY_DESC as $code => $desc) {
											echo "<option value='".$code."' ".(($code=="IN")?"selected":"").">".$desc."</option>";
										}
									?>
									<option></option>
								</select>
							</div><br/>

							<!-- <div class="form-group mb-none">
								<div class="row">
									<div class="col-sm-6 mb-lg">
										<label>Password</label>
										<input name="pwd" type="password" class="form-control input-lg" />
									</div>
									<div class="col-sm-6 mb-lg">
										<label>Password Confirmation</label>
										<input name="pwd_confirm" type="password" class="form-control input-lg" />
									</div>
								</div>
							</div> -->

							<div class="row">
								<div class="col-sm-8">
									<div class="checkbox-custom checkbox-default">
										<input id="AgreeTerms" name="agreeterms" type="checkbox" required="true" />
										<label for="AgreeTerms">I agree with <a href="Primechain Software.pdf">terms of use</a> <span class="required">*</span></label>
									</div>
								</div>
								<div class="col-sm-4 text-right">
									<button type="submit" class="btn btn-primary hidden-xs">Sign Up</button>
									<!-- <button type="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Sign Up</button> -->
								</div>
							</div><br/>

							<p class="text-center">Already have an account? <a href="login.php">Sign In!</a></p>

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