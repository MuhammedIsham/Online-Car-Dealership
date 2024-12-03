<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$statement = $pdo->prepare("UPDATE tbl_seller SET seller_access=? WHERE seller_id=?");
	$statement->execute(array(0,$_POST['seller_id']));
}
if(isset($_POST['form2'])) {
	$statement = $pdo->prepare("UPDATE tbl_seller SET seller_access=? WHERE seller_id=?");
	$statement->execute(array(1,$_POST['seller_id']));
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View All Sellers</h1>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="50">SL</th>
								<th>Name</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Address</th>
								<th>City</th>
								<th>State</th>
								<th>Country</th>
								<th>Status</th>
								<th width="140">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT * FROM tbl_seller");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td><?php echo $row['seller_name']; ?></td>
									<td><?php echo $row['seller_email']; ?></td>
									<td><?php echo $row['seller_phone']; ?></td>
									<td><?php echo $row['seller_address']; ?></td>
									<td><?php echo $row['seller_city']; ?></td>
									<td><?php echo $row['seller_state']; ?></td>
									<td><?php echo $row['seller_country']; ?></td>
									<td>
										<?php
											if($row['seller_access'] == '1') {
												echo 'Active';
											} else {
												echo 'Inactive';
											}
										?>
									</td>
									<td>
										<form action="" method="post">
											<input type="hidden" name="seller_id" value="<?php echo $row['seller_id']; ?>">
											<?php if($row['seller_access']=='1'): ?>
												<input onclick="return confirmInactive();" type="submit" value="Make Inactive" class="btn btn-danger btn-xs" name="form1">
											<?php else: ?>
												<input onclick="return confirmActive();" type="submit" value="Make Active" class="btn btn-success btn-xs" name="form2">
											<?php endif; ?>
											
										</form>
									</td>
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>