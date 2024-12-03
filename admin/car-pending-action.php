<?php require_once('header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	
	$statement = $pdo->prepare("UPDATE tbl_car SET status=? WHERE car_id=?");
	$statement->execute(array(1,$_REQUEST['id']));


	// Send email to seller that his item is approved
	
	// getting seller id from tbl_car
	$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$seller_id = $row['seller_id'];
	}

	// getting seller email address from tbl_seller
	$statement = $pdo->prepare("SELECT * FROM tbl_seller WHERE seller_id=?");
	$statement->execute(array($seller_id));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$seller_email = $row['seller_email'];
	}

	$car_page_url = BASE_URL.'car.php?id='.$_REQUEST['id'];

	$subject = 'Your car update is approved and is live now.';
	$message = 'Your update for the selected car information is approved successfully! Please visit the following link to see it live: <br><a href="'.$car_page_url.'">'.$car_page_url.'</a>';

	$headers = 'From: ' . $visitor_email . "\r\n" .
			   'Reply-To: ' . $visitor_email . "\r\n" .
			   'X-Mailer: PHP/' . phpversion() . "\r\n" . 
			   "MIME-Version: 1.0\r\n" . 
			   "Content-Type: text/html; charset=ISO-8859-1\r\n";

	// Sending email to admin				   
    mail($seller_email, $subject, $message, $headers);

	header('location: car-pending.php');
?>