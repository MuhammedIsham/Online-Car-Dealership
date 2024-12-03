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
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	} else {
		// Preventing one user deleting another user's data through url
		foreach ($result as $row) {
			$seller_id = $row['seller_id'];
		}
		if($seller_id != $_SESSION['seller']['seller_id']) {
			header('location: logout.php');
			exit;
		}
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
?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;
	if(empty($_POST['title'])) {
		$valid = 0;
		$error_message .= 'Title can not be empty.\n';
	}
	if(empty($_POST['regular_price'])) {
		$valid = 0;
		$error_message .= 'Regular Price can not be empty.\n';
	} else {
		if(!is_numeric($_POST['regular_price'])) {
			$valid = 0;
			$error_message .= 'Regular Price must be numeric.\n';
		}
	}
	if(empty($_POST['sale_price'])) {
		$valid = 0;
		$error_message .= 'Sale Price can not be empty.\n';
	} else {
		if(!is_numeric($_POST['sale_price'])) {
			$valid = 0;
			$error_message .= 'Sale Price must be numeric.\n';
		}
	}
	
	$path = $_FILES['featured_photo']['name'];
    $path_tmp = $_FILES['featured_photo']['tmp_name'];

    $allowed_ext = 'jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF';
    $allowed_ext1 = 'jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF';

    $my_ext = get_ext($pdo,'featured_photo');

    if($path!='') {
    	$ext_check = ext_check($pdo,$allowed_ext,$my_ext);
        if(!$ext_check) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file\n';
        }
    }

    if($valid == 1) {


    	// Featured Photo Change
    	if($path!='') {
    		$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_id=?");
    		$statement->execute(array($_REQUEST['id']));
    		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
    		foreach ($result as $row) {
    			$previous_photo = $row['featured_photo'];
    		}    		
    		unlink('assets/uploads/cars/'.$previous_photo);
			$final_name = $_REQUEST['id'].$my_ext;
        	move_uploaded_file( $path_tmp, 'assets/uploads/cars/'.$final_name );
    	}
    	

        // Other Photos
    	if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) )
        {
            $photos = array();
            $photos = $_FILES['photos']["name"];
            $photos = array_values(array_filter($photos));

            $photos_temp = array();
            $photos_temp = $_FILES['photos']["tmp_name"];
            $photos_temp = array_values(array_filter($photos_temp));

            $final_name1 = array();

            $next_id1 = get_ai_id($pdo,'tbl_car_photo');
            $z = $next_id1;

            $m=0;
            for($i=0;$i<count($photos);$i++)
            {
                $my_ext1 = substr($photos[$i], strripos($photos[$i], '.'));
                $ext_check1 = ext_check($pdo,$allowed_ext1,$my_ext1);
                if($ext_check1)
                {   
                    $final_name1[$m] = $z.$my_ext1;
                    move_uploaded_file($photos_temp[$i],"assets/uploads/other-cars/".$final_name1[$m]);
                    $m++; 
                    $z++;
                }
            }
        }

	    // Updating data
	    if($path!=''):
	    	$statement = $pdo->prepare("UPDATE tbl_car SET 
	    						title=?,
	        					description=?,
	        					address=?,
	        					city=?,
	        					state=?,
	        					zip_code=?,
	        					country=?,
	        					map=?,
	        					car_category_id=?,
	        					brand_id=?,
	        					model_id=?,
	        					body_type_id=?,
	        					fuel_type_id=?,
	        					transmission_type_id=?,
	        					vin=?,
	        					car_condition=?,
	        					engine=?,
	        					engine_size=?,
	        					exterior_color=?,
	        					interior_color=?,
	        					seat=?,
	        					door=?,
	        					top_speed=?,
	        					kilometer=?,
	        					mileage=?,
	        					year=?,
	        					warranty=?,
	        					featured_photo=?,
	        					regular_price=?,
	        					sale_price=?,
	        					status=?

	    						WHERE car_id=?
	    					");

	    	$statement->execute(array(
	    						$_POST['title'],
	        					$_POST['description'],
	        					$_POST['address'],
	        					$_POST['city'],
	        					$_POST['state'],
	        					$_POST['zip_code'],
	        					$_POST['country'],
	        					$_POST['map'],
	        					$_POST['car_category_id'],
	        					$_POST['brand_id'],
	        					$_POST['model_id'],
	        					$_POST['body_type_id'],
	        					$_POST['fuel_type_id'],
	        					$_POST['transmission_type_id'],
	        					$_POST['vin'],
	        					$_POST['car_condition'],
	        					$_POST['engine'],
	        					$_POST['engine_size'],
	        					$_POST['exterior_color'],
	        					$_POST['interior_color'],
	        					$_POST['seat'],
	        					$_POST['door'],
	        					$_POST['top_speed'],
	        					$_POST['kilometer'],
	        					$_POST['mileage'],
	        					$_POST['year'],
	        					$_POST['warranty'],
	        					$final_name,
	        					$_POST['regular_price'],
	        					$_POST['sale_price'],
	        					0,

	        					$_REQUEST['id']
	    					));
		else:
			$statement = $pdo->prepare("UPDATE tbl_car SET 
	    						title=?,
	        					description=?,
	        					address=?,
	        					city=?,
	        					state=?,
	        					zip_code=?,
	        					country=?,
	        					map=?,
	        					car_category_id=?,
	        					brand_id=?,
	        					model_id=?,
	        					body_type_id=?,
	        					fuel_type_id=?,
	        					transmission_type_id=?,
	        					vin=?,
	        					car_condition=?,
	        					engine=?,
	        					engine_size=?,
	        					exterior_color=?,
	        					interior_color=?,
	        					seat=?,
	        					door=?,
	        					top_speed=?,
	        					kilometer=?,
	        					mileage=?,
	        					year=?,
	        					warranty=?,
	        					regular_price=?,
	        					sale_price=?,
	        					status=?

	    						WHERE car_id=?
	    					");

	    	$statement->execute(array(
	    						$_POST['title'],
	        					$_POST['description'],
	        					$_POST['address'],
	        					$_POST['city'],
	        					$_POST['state'],
	        					$_POST['zip_code'],
	        					$_POST['country'],
	        					$_POST['map'],
	        					$_POST['car_category_id'],
	        					$_POST['brand_id'],
	        					$_POST['model_id'],
	        					$_POST['body_type_id'],
	        					$_POST['fuel_type_id'],
	        					$_POST['transmission_type_id'],
	        					$_POST['vin'],
	        					$_POST['car_condition'],
	        					$_POST['engine'],
	        					$_POST['engine_size'],
	        					$_POST['exterior_color'],
	        					$_POST['interior_color'],
	        					$_POST['seat'],
	        					$_POST['door'],
	        					$_POST['top_speed'],
	        					$_POST['kilometer'],
	        					$_POST['mileage'],
	        					$_POST['year'],
	        					$_POST['warranty'],
	        					$_POST['regular_price'],
	        					$_POST['sale_price'],
	        					0,

	        					$_REQUEST['id']
	    					));
		endif;

        // Adding data into the tbl_car_photo table
        if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) ) {
        	for($i=0;$i<count($final_name1);$i++)
	        {
	        	$statement = $pdo->prepare("INSERT INTO tbl_car_photo (photo,car_id) VALUES (?,?)");
	        	$statement->execute(array($final_name1[$i],$_REQUEST['id']));
	        }
        }
        
        $success_message .= "Car is Updated successfully. But our admin will approve this update manually. So please wait for that.";
		
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE car_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$title = $row['title'];
	$description = $row['description'];
	$address = $row['address'];
	$city = $row['city'];
	$state = $row['state'];
	$zip_code = $row['zip_code'];
	$country = $row['country'];
	$map = $row['map'];
	$car_category_id = $row['car_category_id'];
	$brand_id = $row['brand_id'];
	$model_id = $row['model_id'];
	$body_type_id = $row['body_type_id'];
	$fuel_type_id = $row['fuel_type_id'];
	$transmission_type_id = $row['transmission_type_id'];
	$vin = $row['vin'];
	$car_condition = $row['car_condition'];
	$engine = $row['engine'];
	$engine_size = $row['engine_size'];
	$exterior_color = $row['exterior_color'];
	$interior_color = $row['interior_color'];
	$seat = $row['seat'];
	$door = $row['door'];
	$top_speed = $row['top_speed'];
	$kilometer = $row['kilometer'];
	$mileage = $row['mileage'];
	$year = $row['year'];
	$warranty = $row['warranty'];
	$featured_photo = $row['featured_photo'];
	$regular_price = $row['regular_price'];
	$sale_price = $row['sale_price'];
	$seller_id = $row['seller_id'];
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

					<h1>Edit Car</h1>
					<?php
					if($error_message != '') {
						echo "<script>alert('".$error_message."')</script>";
					}
					if($success_message != '') {
						echo "<script>alert('".$success_message."')</script>";
					}
					?>
					<div style="margin-bottom: 20px;">* = Required Fields</div>
					<div class="add-car-area">
						<div class="row">
							<div class="information-form">
								<form action="" method="post" enctype="multipart/form-data">
									<div class="form-row">
										<div class="form-group col-md-12 col-sm-12">
											<label for="">Title *</label>
											<input autocomplete="off" type="text" class="form-control" name="title" placeholder="Enter Car Title Here" value="<?php echo $title; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Brand *</label>
											<select data-placeholder="Choose an item ..." class="form-control brand" name="brand_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_brand ORDER BY brand_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option value="<?php echo $row['brand_id']; ?>" <?php if($row['brand_id'] == $brand_id){echo 'selected';} ?>><?php echo $row['brand_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Model *</label>
											<select data-placeholder="Choose an item ..." class="form-control model" name="model_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_model ORDER BY model_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option value="<?php echo $row['model_id']; ?>" <?php if($row['model_id'] == $model_id){echo 'selected';} ?>><?php echo $row['model_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Car Category</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="car_category_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_car_category ORDER BY car_category_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option></option>
													<option value="<?php echo $row['car_category_id']; ?>" <?php if($row['car_category_id'] == $car_category_id){echo 'selected';} ?>><?php echo $row['car_category_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Body Type</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="body_type_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_body_type ORDER BY body_type_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option></option>
													<option value="<?php echo $row['body_type_id']; ?>" <?php if($row['body_type_id'] == $body_type_id){echo 'selected';} ?>><?php echo $row['body_type_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Fuel Type</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="fuel_type_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_fuel_type ORDER BY fuel_type_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option></option>
													<option value="<?php echo $row['fuel_type_id']; ?>" <?php if($row['fuel_type_id'] == $fuel_type_id){echo 'selected';} ?>><?php echo $row['fuel_type_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Transmission Type</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="transmission_type_id">
												<?php
												$statement = $pdo->prepare("SELECT * FROM tbl_transmission_type ORDER BY transmission_type_name ASC");
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												foreach ($result as $row) {
													?>
													<option></option>
													<option value="<?php echo $row['transmission_type_id']; ?>" <?php if($row['transmission_type_id'] == $transmission_type_id){echo 'selected';} ?>><?php echo $row['transmission_type_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">VIN</label>
											<input autocomplete="off" type="text" class="form-control" name="vin" placeholder="Vehicle Identification Number" value="<?php echo $vin; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Condition *</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="car_condition">
												<option></option>
												<option value="New Car" <?php if($car_condition == 'New Car'){echo 'selected';} ?>>New Car</option>
												<option value="Old Car" <?php if($car_condition == 'Old Car'){echo 'selected';} ?>>Old Car</option>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Engine</label>
											<input autocomplete="off" type="text" class="form-control" name="engine" placeholder="Engine Name" value="<?php echo $engine; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Engine Size</label>
											<input autocomplete="off" type="text" class="form-control" name="engine_size" placeholder="Engine Size in cc" value="<?php echo $engine_size; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Exterior Color</label>
											<input autocomplete="off" type="text" class="form-control" name="exterior_color" placeholder="Exterior Color" value="<?php echo $exterior_color; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Interior Color</label>
											<input autocomplete="off" type="text" class="form-control" name="interior_color" placeholder="Interior Color" value="<?php echo $interior_color; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Number of Seats</label>
											<input autocomplete="off" type="text" class="form-control" name="seat" placeholder="Number of Seats" value="<?php echo $seat; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Number of Doors</label>
											<input autocomplete="off" type="text" class="form-control" name="door" placeholder="Number of Doors" value="<?php echo $door; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Top Speed</label>
											<input autocomplete="off" type="text" class="form-control" name="top_speed" placeholder="Top Speed" value="<?php echo $top_speed; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Kilometers</label>
											<input autocomplete="off" type="text" class="form-control" name="kilometer" placeholder="Kilometers (for old car only)" value="<?php echo $kilometer; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Mileage</label>
											<input autocomplete="off" type="text" class="form-control" name="mileage" placeholder="Mileage per liter" value="<?php echo $mileage; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Make Year</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="year">
												<option></option>
												<?php
												$current_year = date('Y');
												for($i=$current_year;$i>=1900;$i--):
												?>
												<option value="<?php echo $i; ?>" <?php if($i == $year){echo 'selected';} ?>><?php echo $i; ?></option>
												<?php endfor; ?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Warranty</label>
											<input autocomplete="off" type="text" class="form-control" name="warranty" placeholder="Warranty (how many years)" value="<?php echo $warranty; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Country</label>
											<input autocomplete="off" type="text" class="form-control" name="country" placeholder="Country" value="<?php echo $country; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">State</label>
											<input autocomplete="off" type="text" class="form-control" name="state" placeholder="State" value="<?php echo $state; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">City</label>
											<input autocomplete="off" type="text" class="form-control" name="city" placeholder="City" value="<?php echo $city; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Zip Code</label>
											<input autocomplete="off" type="text" class="form-control" name="zip_code" placeholder="Zip Code" value="<?php echo $zip_code; ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Address</label>
											<input type="text" class="form-control" name="address" placeholder="Address" value="<?php echo $address; ?>">
										</div>
										<div class="form-group col-md-12 col-sm-12">
											<label for="">Map</label>
											<textarea class="form-control" name="map" placeholder="Map (iframe code)" style="height: 150px;"><?php echo $map; ?></textarea>
										</div>
									</div>
									<div class="form-group col-md-12">
										<label for="">Existing Featured Photo</label>
										<br><img src="assets/uploads/cars/<?php echo $featured_photo; ?>" alt="" style="width:250px;">
								 	</div>

									<div class="form-group col-md-12">
										<label for="">New Featured Photo</label>
										<input type="file" class="form-control-file" name="featured_photo">
								 	</div>

								 	<div class="form-group col-md-6">
										<label for="">More Photos (Add As Many As You Want)</label>

										<?php echo $_REQUEST['id']; ?>
										
										<?php
										$statement = $pdo->prepare("SELECT * FROM tbl_car_photo WHERE car_id=?");
										$statement->execute(array($_REQUEST['id']));
										$result = $statement->fetchAll(PDO::FETCH_ASSOC);
										foreach ($result as $row) {
											?>
											<div style="width:100%;height:auto;overflow: hidden;margin-bottom:5px;">
											<img src="assets/uploads/other-cars/<?php echo $row['photo']; ?>" alt="" style="width: 200px;float:left;margin-right:10px;"><a onclick="return confirmDelete();" href="car-delete-photo.php?id=<?php echo $row['car_photo_id']; ?>&car_id=<?php echo $_REQUEST['id']; ?>" class="Delete btn btn-danger btn-xs">X</a>
											</div>
											<?php
										}
										?>
										<div style="padding-top:6px;">
											<table id="CarTable" style="width:100%;">
							                    <tbody>
							                        
							                    </tbody>
							                </table>	
										</div>
										<div>
							                <input type="button" id="btnAddNew1" value="Add New Photo" class="btn btn-warning btn-xs" style="padding:3px 10px;font-size:12px;height:30px;">
							            </div>						                
								 	</div>

								 	<div class="clear"></div>
								 	
								 	<div class="form-group col-md-6 col-sm-6">
										<label for="">Regular Price *</label>
										<input autocomplete="off" type="text" class="form-control" name="regular_price" placeholder="Regular Price in USD" value="<?php echo $regular_price; ?>">
									</div>

									<div class="form-group col-md-6 col-sm-6">
										<label for="">Sale Price *</label>
										<input autocomplete="off" type="text" class="form-control" name="sale_price" placeholder="Sale Price in USD" value="<?php echo $sale_price; ?>">
									</div>
									
									<div class="form-group col-md-12">
										<label for="">Description</label>
										<textarea class="form-control" name="description"><?php echo $description; ?></textarea>
										<button type="submit" class="btn btn-primary" name="form1">Update information</button>
									</div>

								</form>

							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>


<?php require_once('footer.php'); ?>