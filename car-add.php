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

<?php
if(isset($_POST['form1'])) {
	$valid = 1;
	if(empty($_POST['title'])) {
		$valid = 0;
		$error_message .= 'Title can not be empty.\n';
	}
	if(empty($_POST['brand_id'])) {
		$valid = 0;
		$error_message .= 'You must have to select a brand.\n';
	}
	if(empty($_POST['model_id'])) {
		$valid = 0;
		$error_message .= 'You must have to select a model.\n';
	}
	if(empty($_POST['car_condition'])) {
		$valid = 0;
		$error_message .= 'You must have to select car condition.\n';
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

    $next_id = get_ai_id($pdo,'tbl_car');
    $allowed_ext = 'jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF';
    $allowed_ext1 = 'jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF';

    $my_ext = get_ext($pdo,'featured_photo');

    if($path!='') {
    	$ext_check = ext_check($pdo,$allowed_ext,$my_ext);
        if(!$ext_check) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file\n';
        }
    } else {
    	$valid = 0;
        $error_message .= 'You must have to select a featured photo\n';
    }


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
	if($total_car >= $pricing_plan_item_allow) {
		$valid = 0;
		$error_message .= 'Your maximum number of allowed car entry is reached.\n';
	}


    if($valid == 1) {

    	$final_name1 = array();        

    	// Upload Featured Photo
    	$final_name = $next_id.$my_ext;
        move_uploaded_file( $path_tmp, 'assets/uploads/cars/'.$final_name );

        // Upload Other Photos
	    if( isset($_FILES['photos']["name"]) && isset($_FILES['photos']["tmp_name"]) ) {

	    	$photos = array();
	        $photos = $_FILES['photos']["name"];
	        $photos = array_values(array_filter($photos));

	        $photos_temp = array();
	        $photos_temp = $_FILES['photos']["tmp_name"];
	        $photos_temp = array_values(array_filter($photos_temp));

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

	    // Adding data into the tbl_car table
        $statement = $pdo->prepare("
        					INSERT INTO tbl_car 
        					(
        					title,
        					description,
        					address,
        					city,
        					state,
        					zip_code,
        					country,
        					map,
        					car_category_id,
        					brand_id,
        					model_id,
        					body_type_id,
        					fuel_type_id,
        					transmission_type_id,
        					vin,
        					car_condition,
        					engine,
        					engine_size,
        					exterior_color,
        					interior_color,
        					seat,
        					door,
        					top_speed,
        					kilometer,
        					mileage,
        					year,
        					warranty,
        					featured_photo,
        					regular_price,
        					sale_price,
        					seller_id,
        					status
        					) 

        					VALUES 
        					(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
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
        					$_SESSION['seller']['seller_id'],
        					0
        				));

        // Adding data into the tbl_car_photo table
        for($i=0;$i<count($final_name1);$i++)
        {
        	$statement = $pdo->prepare("INSERT INTO tbl_car_photo (photo,car_id) VALUES (?,?)");
        	$statement->execute(array($final_name1[$i],$next_id));
        }
        $success_message .= "Car is added successfully. But it will only become live after getting approved by admin.";

        unset($_POST['title']);
        unset($_POST['description']);
        unset($_POST['address']);
        unset($_POST['city']);
        unset($_POST['state']);
        unset($_POST['zip_code']);
        unset($_POST['country']);
        unset($_POST['map']);
        unset($_POST['vin']);
        unset($_POST['engine']);
        unset($_POST['engine_size']);
        unset($_POST['exterior_color']);
        unset($_POST['interior_color']);
        unset($_POST['seat']);
        unset($_POST['door']);
        unset($_POST['top_speed']);
        unset($_POST['kilometer']);
        unset($_POST['mileage']);
        unset($_POST['warranty']);
        unset($_POST['regular_price']);
        unset($_POST['sale_price']);
		
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

					<h1>Add Car</h1>

					
					<!-- Checking if this use has made payment -->
					<?php
					$allowed = 0;
					$today = date('Y-m-d');
					$statement = $pdo->prepare("SELECT * 
												FROM tbl_payment 
												WHERE seller_id=? AND payment_status=?
											");
					$statement->execute(array($_SESSION['seller']['seller_id'],'Completed'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						if(($today >= $row['payment_date'])&&($today <= $row['expire_date'])) {
							$allowed = 1;
						}
					}
					?>

					<?php
					if($error_message != '') {
						echo "<script>alert('".$error_message."')</script>";
					}
					if($success_message != '') {
						echo "<script>alert('".$success_message."')</script>";
					}
					?>
					<?php if($allowed == 0): ?>
					<div class="error">You can only add car after making a payment. <a href="<?php echo BASE_URL; ?>payment.php" style="color:red;text-decoration:underline;">Go here</a> to make a payment.</div>
					<?php else: ?>
					<div style="margin-bottom: 20px;">* = Required Fields</div>
					<div class="add-car-area">
						<div class="row">
							<div class="information-form">
								<form action="" method="post" enctype="multipart/form-data">
									<div class="form-row">
										<div class="form-group col-md-12 col-sm-12">
											<label for="">Title *</label>
											<input autocomplete="off" type="text" class="form-control" name="title" placeholder="Enter Car Title Here" value="<?php if(isset($_POST['title'])) {echo $_POST['title'];} ?>">
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
													<option></option>
													<option value="<?php echo $row['brand_id']; ?>"><?php echo $row['brand_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Model *</label>
											<select data-placeholder="Choose an item ..." class="form-control model" name="model_id">
												<option value="">--Select Model--</option>
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
													<option value="<?php echo $row['car_category_id']; ?>"><?php echo $row['car_category_name']; ?></option>
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
													<option value="<?php echo $row['body_type_id']; ?>"><?php echo $row['body_type_name']; ?></option>
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
													<option value="<?php echo $row['fuel_type_id']; ?>"><?php echo $row['fuel_type_name']; ?></option>
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
													<option value="<?php echo $row['transmission_type_id']; ?>"><?php echo $row['transmission_type_name']; ?></option>
													<?php
												}
												?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">VIN</label>
											<input autocomplete="off" type="text" class="form-control" name="vin" placeholder="Vehicle Identification Number" value="<?php if(isset($_POST['vin'])) {echo $_POST['vin'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Condition *</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="car_condition">
												<option></option>
												<option value="New Car">New Car</option>
												<option value="Old Car">Old Car</option>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Engine</label>
											<input autocomplete="off" type="text" class="form-control" name="engine" placeholder="Engine Name" value="<?php if(isset($_POST['engine'])) {echo $_POST['engine'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Engine Size</label>
											<input autocomplete="off" type="text" class="form-control" name="engine_size" placeholder="Engine Size in cc" value="<?php if(isset($_POST['engine_size'])) {echo $_POST['engine_size'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Exterior Color</label>
											<input autocomplete="off" type="text" class="form-control" name="exterior_color" placeholder="Exterior Color" value="<?php if(isset($_POST['exterior_color'])) {echo $_POST['exterior_color'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Interior Color</label>
											<input autocomplete="off" type="text" class="form-control" name="interior_color" placeholder="Interior Color" value="<?php if(isset($_POST['interior_color'])) {echo $_POST['interior_color'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Number of Seats</label>
											<input autocomplete="off" type="text" class="form-control" name="seat" placeholder="Number of Seats" value="<?php if(isset($_POST['seat'])) {echo $_POST['seat'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Number of Doors</label>
											<input autocomplete="off" type="text" class="form-control" name="door" placeholder="Number of Doors" value="<?php if(isset($_POST['door'])) {echo $_POST['door'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Top Speed</label>
											<input autocomplete="off" type="text" class="form-control" name="top_speed" placeholder="Top Speed" value="<?php if(isset($_POST['top_speed'])) {echo $_POST['top_speed'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Kilometers</label>
											<input autocomplete="off" type="text" class="form-control" name="kilometer" placeholder="Kilometers (for old car only)" value="<?php if(isset($_POST['kilometer'])) {echo $_POST['kilometer'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Mileage</label>
											<input autocomplete="off" type="text" class="form-control" name="mileage" placeholder="Mileage per liter" value="<?php if(isset($_POST['mileage'])) {echo $_POST['mileage'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6 option-item">
											<label for="">Make Year</label>
											<select data-placeholder="Choose an item ..." class="form-control chosen-select" name="year">
												<option></option>
												<?php
												$current_year = date('Y');
												for($i=$current_year;$i>=1900;$i--):
												?>
												<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
												<?php endfor; ?>
											</select>
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Warranty</label>
											<input autocomplete="off" type="text" class="form-control" name="warranty" placeholder="Warranty (how many years)" value="<?php if(isset($_POST['warranty'])) {echo $_POST['warranty'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Country</label>
											<input autocomplete="off" type="text" class="form-control" name="country" placeholder="Country" value="<?php if(isset($_POST['country'])) {echo $_POST['country'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">State</label>
											<input autocomplete="off" type="text" class="form-control" name="state" placeholder="State" value="<?php if(isset($_POST['state'])) {echo $_POST['state'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">City</label>
											<input autocomplete="off" type="text" class="form-control" name="city" placeholder="City" style="<?php if(isset($_POST['city'])) {echo $_POST['city'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Zip Code</label>
											<input autocomplete="off" type="text" class="form-control" name="zip_code" placeholder="Zip Code" value="<?php if(isset($_POST['zip_code'])) {echo $_POST['zip_code'];} ?>">
										</div>
										<div class="form-group col-md-6 col-sm-6">
											<label for="">Address</label>
											<input type="text" class="form-control" name="address" placeholder="Address" value="<?php if(isset($_POST['address'])) {echo $_POST['address'];} ?>">
										</div>
										<div class="form-group col-md-12 col-sm-12">
											<label for="">Map</label>
											<textarea class="form-control" name="map" placeholder="Map (iframe code)" style="height: 150px;"><?php if(isset($_POST['map'])) {echo $_POST['map'];} ?></textarea>
										</div>
									</div>
									
									<div class="form-group col-md-12">
										<label for="">Featured Photo *</label>
										<input type="file" class="form-control-file" name="featured_photo">
								 	</div>

								 	<div class="form-group col-md-6">
										<label for="">More Photos (Add As Many As You Want)</label>
										
										<div style="padding-top:6px;">
											<table id="CarTable" style="width:100%;">
							                    <tbody>
							                        <tr>
							                            <td>
							                                <div class="upload-btn">
							                                    <input type="file" name="photos[]">
							                                </div>
							                            </td>
							                            <td style="width:28px;"><a href="javascript:void()" class="Delete btn btn-danger btn-xs">X</a></td>
							                        </tr>
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
										<input autocomplete="off" type="text" class="form-control" name="regular_price" placeholder="Regular Price in USD" value="<?php if(isset($_POST['regular_price'])) {echo $_POST['regular_price'];} ?>">
									</div>

									<div class="form-group col-md-6 col-sm-6">
										<label for="">Sale Price *</label>
										<input autocomplete="off" type="text" class="form-control" name="sale_price" placeholder="Sale Price in USD" value="<?php if(isset($_POST['sale_price'])) {echo $_POST['sale_price'];} ?>">
									</div>
									
									<div class="form-group col-md-12">
										<label for="">Description</label>
										<textarea class="form-control" name="description"><?php if(isset($_POST['description'])) {echo $_POST['description'];} ?></textarea>
										<button type="submit" class="btn btn-primary" name="form1">Add Your Car</button>
									</div>

								</form>

							</div>
						</div>
					</div>
					<?php endif; ?>





				</div>
			</div>
		</div>
	</div>
</div>


<?php require_once('footer.php'); ?>