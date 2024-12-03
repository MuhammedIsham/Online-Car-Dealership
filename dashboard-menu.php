<?php
$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
?>
<ul>
	<li class="<?php if($cur_page == 'dashboard.php'){echo 'active';} ?>"><a href="dashboard.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>Dashboard</a></li>
	<li class="<?php if($cur_page == 'car-add.php'){echo 'active';} ?>"><a href="car-add.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>Add Car</a></li>
	<li class="<?php if($cur_page == 'car-view.php'){echo 'active';} ?>"><a href="car-view.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>View Cars</a></li>
	<li class="<?php if($cur_page == 'profile-edit.php'){echo 'active';} ?>"><a href="profile-edit.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>Update Profile</a></li>
	<li class="<?php if($cur_page == 'payment.php'){echo 'active';} ?>"><a href="payment.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>Make Payment</a></li>
	<li><a href="logout.php"><span><i class="fa fa-arrow-circle-o-right "></i></span>Logout</a></li>
</ul>