<?php require_once('header.php'); ?>

<section class="content-header">
  <h1>Dashboard</h1>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_car_category");
$statement->execute();
$total_car_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_brand");
$statement->execute();
$total_brand = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_model");
$statement->execute();
$total_model = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=1");
$statement->execute();
$total_approved_car = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=0");
$statement->execute();
$total_pending_car = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_news");
$statement->execute();
$total_news = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_seller WHERE seller_access=1");
$statement->execute();
$total_seller = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_subscriber");
$statement->execute();
$total_subscriber = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_pricing_plan");
$statement->execute();
$total_pricing_plan = $statement->rowCount();
?>

<section class="content">

  <div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Car Categories</span>
          <span class="info-box-number"><?php echo $total_car_category; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Brands</span>
          <span class="info-box-number"><?php echo $total_brand; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-hand-o-right"></i></span>

        <div class="info-box-content">
          <span class="info-box-text">Total Models</span>
          <span class="info-box-number"><?php echo $total_model; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Approved Cars</span>
          <span class="info-box-number"><?php echo $total_approved_car; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Pending Cars</span>
          <span class="info-box-number"><?php echo $total_pending_car; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total News</span>
          <span class="info-box-number"><?php echo $total_news; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Sellers</span>
          <span class="info-box-number"><?php echo $total_seller; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-red"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Subscribers</span>
          <span class="info-box-number"><?php echo $total_subscriber; ?></span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
      <div class="info-box">
        <span class="info-box-icon bg-green"><i class="fa fa-hand-o-right"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Pricing Plans</span>
          <span class="info-box-number"><?php echo $total_pricing_plan; ?></span>
        </div>
      </div>
    </div>
  </div>


</section>

<?php require_once('footer.php'); ?>