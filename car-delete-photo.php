<?php require_once('header.php'); ?>

<?php
// Check if the seller is logged in or not
if(!isset($_SESSION['seller'])) {
	header('location: '.BASE_URL.'logout.php');
	exit;
}
?>

<?php
if( !isset($_REQUEST['id']) || !isset($_REQUEST['car_id']) ) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_car_photo WHERE car_photo_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: '.BASE_URL.'logout.php');
		exit;
	}
}

// If seller is logged in, but admin make him inactive, then force logout this user.
$statement = $pdo->prepare("SELECT * FROM tbl_seller WHERE seller_id=? AND seller_access=?");
$statement->execute(array($_SESSION['seller']['seller_id'],0));
$total = $statement->rowCount();
if($total) {
	header('location: '.BASE_URL.'logout.php');
	exit;
}

// Getting photo ID to unlink from folder							
foreach ($result as $row) {
	$photo = $row['photo'];
}

// Unlink the photo
if($photo!='') {
	unlink('assets/uploads/other-cars/'.$photo);	
}

// Delete from tbl_news
$statement = $pdo->prepare("DELETE FROM tbl_car_photo WHERE car_photo_id=?");
$statement->execute(array($_REQUEST['id']));

header('location: car-edit.php?id='.$_REQUEST['car_id']);
?>