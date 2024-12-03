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

					<h1>View Your Cars</h1>

					<table id="example" class="display" width="100%" cellspacing="0">
						<thead>
							<tr>
								<th>Serial</th>
								<th>Car Name</th>
								<th>Brand</th>
								<th>Model</th>
								<th>Main Photo</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 0;
							$statement = $pdo->prepare("SELECT
														
														t1.car_id,
														t1.title,
														t1.description,
														t1.address,
														t1.zip_code,
														t1.country,
														t1.map,
														t1.car_category_id,
														t1.brand_id,
														t1.model_id,
														t1.body_type_id,
														t1.fuel_type_id,
														t1.transmission_type_id,
														t1.vin,
														t1.car_condition,
														t1.engine,
														t1.engine_size,
														t1.exterior_color,
														t1.interior_color,
														t1.seat,
														t1.door,
														t1.top_speed,
														t1.kilometer,
														t1.mileage,
														t1.year,
														t1.warranty,
														t1.featured_photo,
														t1.regular_price,
														t1.sale_price,
														t1.seller_id,
														t1.status,

														t2.brand_id,
														t2.brand_name,

														t3.model_id,
														t3.model_name


														FROM tbl_car t1

														JOIN tbl_brand t2
														ON t1.brand_id = t2.brand_id

														JOIN tbl_model t3
														ON t1.model_id = t3.model_id


														WHERE t1.seller_id=?");
							$statement->execute(array($_SESSION['seller']['seller_id']));
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);			
							foreach ($result as $row) {
								$i++;
								?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $row['title']; ?></td>
										<td><?php echo $row['brand_name']; ?></td>
										<td><?php echo $row['model_name']; ?></td>
										<td>
											<img src="assets/uploads/cars/<?php echo $row['featured_photo']; ?>" alt="" style="width:150px;">
										</td>
										<td>
											<?php if($row['status'] == 0): ?>
											Pending
											<?php else: ?>
											Active
											<?php endif; ?>
										</td>
										<td>
											<a href="" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModalDetail<?php echo $i; ?>" style="width:100%;margin-bottom:3px;">Details</a><br>
											<a href="car-edit.php?id=<?php echo $row['car_id']; ?>" class="btn btn-warning btn-xs" style="width:100%;margin-bottom:3px;">Edit</a><br>
											<a onclick="return confirmDelete();" href="car-delete.php?id=<?php echo $row['car_id']; ?>" class="btn btn-danger btn-xs" style="width:100%;margin-bottom:3px;">Delete</a>
		

		<!-- Modal Start -->
		<div class="modal fade" id="myModalDetail<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                <h4 class="modal-title" id="myModalLabel">
		                  Detail Information
		                </h4>
		            </div>
		            <div class="modal-body">
		                <div class="rTable">
		                	<div class="rTableRow">
								<div class="rTableHead">Featured Photo</div>
								<div class="rTableCell">
									<img src="<?php echo BASE_URL; ?>assets/uploads/cars/<?php echo $row['featured_photo']; ?>" alt="" style="width:250px;">
								</div>
							</div>				
							<div class="rTableRow">
								<div class="rTableHead">Title</div>
								<div class="rTableCell"><?php echo $row['title']; ?></div>
							</div>
							<div class="rTableRow">
								<div class="rTableHead">Brand</div>
								<div class="rTableCell"><?php echo $row['brand_name']; ?></div>
							</div>
							<div class="rTableRow">
								<div class="rTableHead">Model</div>
								<div class="rTableCell"><?php echo $row['model_name']; ?></div>
							</div>


							<?php
							$statement1 = $pdo->prepare("SELECT * FROM tbl_car_category WHERE car_category_id=?");
							$statement1->execute(array($row['car_category_id']));
							$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);	
							foreach ($result1 as $row1) {
								$car_category_name = $row1['car_category_name'];
							}
							$statement1 = $pdo->prepare("SELECT * FROM tbl_body_type WHERE body_type_id=?");
							$statement1->execute(array($row['body_type_id']));
							$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);	
							foreach ($result1 as $row1) {
								$body_type_name = $row1['body_type_name'];
							}
							$statement1 = $pdo->prepare("SELECT * FROM tbl_fuel_type WHERE fuel_type_id=?");
							$statement1->execute(array($row['fuel_type_id']));
							$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);	
							foreach ($result1 as $row1) {
								$fuel_type_name = $row1['fuel_type_name'];
							}
							$statement1 = $pdo->prepare("SELECT * FROM tbl_transmission_type WHERE transmission_type_id=?");
							$statement1->execute(array($row['transmission_type_id']));
							$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);	
							foreach ($result1 as $row1) {
								$transmission_type_name = $row1['transmission_type_name'];
							}
							?>

							<?php if(!empty($car_category_name)): ?>
							<div class="rTableRow">
								<div class="rTableHead">Car Category Name</div>
								<div class="rTableCell">
									<?php echo $car_category_name; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($body_type_name)): ?>
							<div class="rTableRow">
								<div class="rTableHead">Body Type</div>
								<div class="rTableCell">
									<?php echo $body_type_name; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($fuel_type_name)): ?>
							<div class="rTableRow">
								<div class="rTableHead">Fuel Type</div>
								<div class="rTableCell">
									<?php echo $fuel_type_name; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($transmission_type_name)): ?>
							<div class="rTableRow">
								<div class="rTableHead">Transmission Type</div>
								<div class="rTableCell">
									<?php echo $transmission_type_name; ?>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if(!empty($row['vin'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">VIN</div>
								<div class="rTableCell">
									<?php echo $row['vin']; ?>
								</div>
							</div>
							<?php endif; ?>

							
							<div class="rTableRow">
								<div class="rTableHead">Condition</div>
								<div class="rTableCell">
									<?php echo $row['car_condition']; ?>
								</div>
							</div>

							<?php if(!empty($row['engine'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Engine</div>
								<div class="rTableCell">
									<?php echo $row['engine']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['engine_size'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Engine Size</div>
								<div class="rTableCell">
									<?php echo $row['engine_size']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['exterior_color'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Exterior Color</div>
								<div class="rTableCell">
									<?php echo $row['exterior_color']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['interior_color'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Interior Color</div>
								<div class="rTableCell">
									<?php echo $row['interior_color']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['seat'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Number of Seats</div>
								<div class="rTableCell">
									<?php echo $row['seat']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['door'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Number of Doors</div>
								<div class="rTableCell">
									<?php echo $row['door']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['top_speed'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Top Speed</div>
								<div class="rTableCell">
									<?php echo $row['top_speed']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['kilometer'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Kilometers</div>
								<div class="rTableCell">
									<?php echo $row['kilometer']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['mileage'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Mileage</div>
								<div class="rTableCell">
									<?php echo $row['mileage']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['year'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Make Year</div>
								<div class="rTableCell">
									<?php echo $row['year']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['warranty'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Warranty</div>
								<div class="rTableCell">
									<?php echo $row['warranty']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['country'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Country</div>
								<div class="rTableCell">
									<?php echo $row['country']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['state'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">State</div>
								<div class="rTableCell">
									<?php echo $row['state']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['city'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">City</div>
								<div class="rTableCell">
									<?php echo $row['city']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['zip_code'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Zip Code</div>
								<div class="rTableCell">
									<?php echo $row['zip_code']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['address'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Address</div>
								<div class="rTableCell">
									<?php echo $row['address']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['map'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Map</div>
								<div class="rTableCell">
									<?php echo $row['map']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['regular_price'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Regular Price</div>
								<div class="rTableCell">
									<?php echo $row['regular_price']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['sale_price'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Sale Price</div>
								<div class="rTableCell">
									<?php echo $row['sale_price']; ?>
								</div>
							</div>
							<?php endif; ?>

							<?php if(!empty($row['description'])): ?>
							<div class="rTableRow">
								<div class="rTableHead">Seller Description</div>
								<div class="rTableCell">
									<?php echo nl2br($row['description']); ?>
								</div>
							</div>
							<?php endif; ?>

							<div class="rTableRow">
								<div class="rTableHead">Photos</div>
								<div class="rTableCell">
									<?php
									$statement1 = $pdo->prepare("SELECT * FROM tbl_car_photo WHERE car_id=?");
									$statement1->execute(array($row['car_id']));
									$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
									foreach ($result1 as $row1) {
										?>
										<img src="<?php echo BASE_URL; ?>assets/uploads/other-cars/<?php echo $row1['photo']; ?>" alt="" style="width:250px;margin-bottom:10px;"><br>
										<?php
									}
									?>
								</div>
							</div>
							
						</div>
		            </div>
		        </div>                                    
		    </div>                                
		</div>
		<!-- Modal End -->


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
</div>

<?php require_once('footer.php'); ?>