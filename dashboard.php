<?php require_once('header.php'); ?>

<?php
// Check if the seller is logged in or not
if(!isset($_SESSION['seller'])) {
	header('location: '.BASE_URL.'logout.php');
	exit;
} else {
	// If seller is logged in, but admin make him inactive, then force logout this user.
	$statement = $pdo->prepare("SELECT * FROM tbl_seller WHERE seller_id=? AND seller_access=?");
	$statement->execute(array($_SESSION['seller']['seller_id'],0));
	$total = $statement->rowCount();
	if($total) {
		header('location: '.BASE_URL.'logout.php');
		exit;
	}
}
?>

<!--Dashboard Start-->
<div class="dashboard-area bg-area">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				<div class="option-board">
					<?php require_once('dashboard-menu.php'); ?>
				</div>
			</div>
			<div class="col-md-9 col-sm-12">
				<div class="detail-dashboard">

					<h1>Hi, <?php echo $_SESSION['seller']['seller_name']; ?></h1>

					<h3>Welcome to your dashboard.</h3>

					<?php
					// Getting allowed car numbers for this seller
					$statement = $pdo->prepare("SELECT * 
												FROM tbl_payment 
												WHERE seller_id=? AND payment_status=?
												ORDER BY id DESC
											");
					$statement->execute(array($_SESSION['seller']['seller_id'],'Completed'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$pricing_plan_id = $row['pricing_plan_id'];
						break;
					}
					$statement = $pdo->prepare("SELECT * FROM tbl_pricing_plan WHERE pricing_plan_id=?");
					$statement->execute(array($pricing_plan_id));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$pricing_plan_item_allow = $row['pricing_plan_item_allow'];
					}

					$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE seller_id=?");
					$statement->execute(array($_SESSION['seller']['seller_id']));
					$total_car = $statement->rowCount();
					?>

					<h4 style="margin-top:30px;"><strong>Statistics</strong></h4>
					<div class="row">
						<div class="col-md-4 col-sm-12 col-xs-12">
							<table class="table table-bordered">
								<tr>
									<td>Total Allowed Car:</td>
									<td><?php echo $pricing_plan_item_allow; ?></td>
								</tr>
								<tr>
									<td>Total Cars you Added:</td>
									<td><?php echo $total_car; ?></td>
								</tr>
								<tr>
									<td>Remaining Cars:</td>
									<td><?php echo ($pricing_plan_item_allow-$total_car); ?></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>