<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_car_category WHERE car_category_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_category_id=?");
	$statement->execute(array($_REQUEST['id']));
	$tot = $statement->rowCount();
	if($tot) {
		header('location: car-category.php?msg=1');
	} else {
		// Delete from tbl_car_category
		$statement = $pdo->prepare("DELETE FROM tbl_car_category WHERE car_category_id=?");
		$statement->execute(array($_REQUEST['id']));

		header('location: car-category.php');	
	}
?>