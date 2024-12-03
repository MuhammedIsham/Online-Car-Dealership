<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$banner_registration = $row['banner_registration'];
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['seller_name'])) {
        $valid = 0;
        $error_message .= "Name can not be empty.\\n";
    }

    if(empty($_POST['seller_email'])) {
        $valid = 0;
        $error_message .= "Email can not be empty.\\n";
    } else {
    	if (filter_var($_POST['seller_email'], FILTER_VALIDATE_EMAIL) === false) {
	        $valid = 0;
	        $error_message .= 'Email address must be valid.\\n';
	    } else {
	    	$statement = $pdo->prepare("SELECT * FROM tbl_seller WHERE seller_email=?");
	    	$statement->execute(array($_POST['seller_email']));
	    	$total = $statement->rowCount();							
	    	if($total) {
	    		$valid = 0;
	        	$error_message .= 'Email address already exists.\\n';
	    	}
	    }
    }

    if(empty($_POST['seller_address'])) {
        $valid = 0;
        $error_message .= "Address can not be empty.\\n";
    }

    if(empty($_POST['seller_city'])) {
        $valid = 0;
        $error_message .= "City can not be empty.\\n";
    }

    if(empty($_POST['seller_country'])) {
        $valid = 0;
        $error_message .= "Country can not be empty.\\n";
    }

    if( empty($_POST['seller_password']) || empty($_POST['seller_re_password']) ) {
        $valid = 0;
        $error_message .= "Password can not be empty.\\n";
    }

    if( !empty($_POST['seller_password']) && !empty($_POST['seller_re_password']) ) {
    	if($_POST['seller_password'] != $_POST['seller_re_password']) {
	    	$valid = 0;
	        $error_message .= "Passwords do not match.\\n";	
    	}        
    }

    if($valid == 1) {

    	$token = md5(uniqid(rand(), true));
    	$now = time();

		// saving into the database
		$statement = $pdo->prepare("INSERT INTO tbl_seller (seller_name,seller_email,seller_phone,seller_address, seller_city,seller_state,seller_country,seller_password,seller_token,seller_time,seller_access) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
		$statement->execute(array($_POST['seller_name'],$_POST['seller_email'],$_POST['seller_phone'],$_POST['seller_address'],$_POST['seller_city'],$_POST['seller_state'],$_POST['seller_country'],md5($_POST['seller_password']),$token,$now,0));

		// Send email for confirmation of the account
        $to = $_POST['seller_email'];
        
        $subject = 'Registration Email Confirmation for ' . BASE_URL;
        $verify_link = BASE_URL.'verify.php?email='.$to.'&token='.$token;
        $message = '
Thank you for signing up!
Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.<br><br>

Please click this link to activate your account:
<a href="'.$verify_link.'">'.$verify_link.'</a>';

		$headers = "From: noreply@" . BASE_URL . "\r\n" .
				   "Reply-To: noreply@" . BASE_URL . "\r\n" .
				   "X-Mailer: PHP/" . phpversion() . "\r\n" . 
				   "MIME-Version: 1.0\r\n" . 
				   "Content-Type: text/html; charset=ISO-8859-1\r\n";
				   
        mail($to, $subject, $message, $headers); // Send the email

    	unset($_POST['seller_name']);
    	unset($_POST['seller_email']);
    	unset($_POST['seller_phone']);
    	unset($_POST['seller_address']);
    	unset($_POST['seller_city']);
    	unset($_POST['seller_state']);
    	unset($_POST['seller_country']);

    	$success_message = 'Your registration is completed. Please check your email address to follow the process to confirm your registration.';
    }
}
?>

<div class="banner-slider" style="background-image: url(<?php echo BASE_URL.'assets/uploads/'.$banner_registration; ?>)">
	<div class="bg"></div>
	<div class="bannder-table">
		<div class="banner-text">
			<h1>Seller Registration</h1>
		</div>
	</div>
</div>

<div class="login-area bg-area">
	<div class="container">
		<div class="row">
		
			<div class="col-md-offset-4 col-md-5">
				
				<?php
				if($error_message != '') {
					echo "<script>alert('".$error_message."')</script>";
				}
				if($success_message != '') {
					echo "<script>alert('".$success_message."')</script>";
				}
				?>
				<div class="login-form">
					
					<form action="" method="post">

						<div class="form-row">
							
							<div class="form-group">
								<label for="">Full Name *</label>
								<input type="text" class="form-control" name="seller_name" placeholder="Full Name" value="<?php if(isset($_POST['seller_name'])){echo $_POST['seller_name'];} ?>">
							</div>

							<div class="form-group">
								<label for="">Email Address *</label>
								<input type="email" class="form-control" name="seller_email" placeholder="Email Address" value="<?php if(isset($_POST['seller_email'])){echo $_POST['seller_email'];} ?>">
							</div>

							<div class="form-group">
								<label for="">Phone </label>
								<input type="text" class="form-control" name="seller_phone" placeholder="Phone Number" value="<?php if(isset($_POST['seller_phone'])){echo $_POST['seller_phone'];} ?>">
							</div>

							<div class="form-group">
								<label for="">Address *</label>
								<textarea name="seller_address" class="form-control" cols="30" rows="10" placeholder="Address" style="height:120px;"><?php if(isset($_POST['seller_address'])){echo $_POST['seller_address'];} ?></textarea>
							</div>

							<div class="form-group">
								<label for="">City *</label>
								<input type="text" class="form-control" name="seller_city" placeholder="City" value="<?php if(isset($_POST['seller_city'])){echo $_POST['seller_city'];} ?>">
							</div>

							<div class="form-group">
								<label for="">State</label>
								<input type="text" class="form-control" name="seller_state" placeholder="State" value="<?php if(isset($_POST['seller_state'])){echo $_POST['seller_state'];} ?>">
							</div>

							<div class="form-group">
								<label for="">Country *</label>
								<input type="text" class="form-control" name="seller_country" placeholder="Country" value="<?php if(isset($_POST['seller_country'])){echo $_POST['seller_country'];} ?>">
							</div>
							
							<div class="form-group">
								<label for="">Password *</label>
								<input type="password" class="form-control" name="seller_password" placeholder="Password">
							</div>

							<div class="form-group">
								<label for="">Retype Password *</label>
								<input type="password" class="form-control" name="seller_re_password" placeholder="Retype Password">
							</div>
							
							<button type="submit" class="btn btn-primary" name="form1">Sign Up</button>
							
						</div>

					</form>

				</div>
			</div>

			<div class="login-here">
				<h3><i class="fa fa-user-circle-o"></i> Already a Member? <a href="login.php">Login Here</a></h3>
			</div>			
				
		</div>
	</div>
</div>
	
<?php require_once('footer.php'); ?>