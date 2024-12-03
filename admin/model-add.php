<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['model_name'])) {
        $valid = 0;
        $error_message .= "Model Name can not be empty<br>";
    } else {
    	// Duplicate Category checking
    	$statement = $pdo->prepare("SELECT * FROM tbl_model WHERE model_name=?");
    	$statement->execute(array($_POST['model_name']));
    	$total = $statement->rowCount();
    	if($total) {
    		$valid = 0;
        	$error_message .= "Model Name already exists<br>";
    	}
    }

    if(empty($_POST['brand_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a brand<br>";
    }


    if($valid == 1) {

		// saving into the database
		$statement = $pdo->prepare("INSERT INTO tbl_model (model_name,brand_id) VALUES (?,?)");
		$statement->execute(array($_POST['model_name'],$_POST['brand_id']));

    	$success_message = 'Model is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Model</h1>
	</div>
	<div class="content-header-right">
		<a href="model.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
			
			<p>
			<?php echo $error_message; ?>
			</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
			
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post">

				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Model Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="model_name" autocomplete="off">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Select Brand <span>*</span></label>
							<div class="col-sm-4">
								<select class="form-control select2" name="brand_id">
									<option value="">Select a brand</option>
									<?php
									$statement = $pdo->prepare("SELECT * FROM tbl_brand ORDER BY brand_name ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
									foreach ($result as $row) {
										echo '<option value="'.$row['brand_id'].'">'.$row['brand_name'].'</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>

			</form>


		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>