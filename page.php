<?php require_once('header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['slug']))
{
	header('location: index.php');
	exit;
}
else
{
	// Check the page slug is valid or not.
	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=? AND status=?");
	$statement->execute(array($_REQUEST['slug'],'Active'));
	$total = $statement->rowCount();
	if( $total == 0 )
	{
		header('location: index.php');
		exit;
	}
}

// Getting the detailed data of a page from page slug
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=?");
$statement->execute(array($_REQUEST['slug']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) 
{
	$page_name    = $row['page_name'];
	$page_slug    = $row['page_slug'];
	$page_content = $row['page_content'];
	$page_layout  = $row['page_layout'];
	$banner       = $row['banner'];
	$status       = $row['status'];
}

// If a page is not active, redirect the user while direct URL press
if($status == 'Inactive')
{
	header('location: index.php');
	exit;
}
?>

<!--Banner Start-->
<div class="banner-slider" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $banner; ?>)">
	<div class="bg"></div>
	<div class="bannder-table">
		<div class="banner-text">
			<h1><?php echo $page_name; ?></h1>
		</div>
	</div>
</div>
<!--Banner End-->


<?php if($page_layout == 'Full Width Page Layout'): ?>
<div class="about-area">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="about-text">
					<?php echo $page_content; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php if($page_layout == 'FAQ Page Layout'): ?>
<div class="faq-area bg-area">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				
				<?php
				$i=0;
				$j=0;
				$statement = $pdo->prepare("SELECT * FROM tbl_faq_category ORDER BY faq_category_id ASC");
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
				foreach ($result as $row) {
					$i++;
					?>
					<div class="accordion-item">
						<h3><?php echo $row['faq_category_name']; ?></h3>
						<dl class="faq-accordion">

							<?php
							$statement1 = $pdo->prepare("SELECT * FROM tbl_faq WHERE faq_category_id=?");
							$statement1->execute(array($row['faq_category_id']));
							$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);		
							foreach ($result1 as $row1) {
								$j++;
								?>
								<dt class="<?php if($j==1){echo 'open';} ?>"><?php echo $row1['faq_title']; ?></dt>
								<dd>
									<?php echo $row1['faq_content']; ?>
								</dd>
								<?php
							}
							?>
						</dl>
					</div>
					<?php
				}
				?>

			</div>
		</div>
	</div>
</div>
<?php endif; ?>


<?php if($page_layout == 'Contact Us Page Layout'): ?>
<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$contact_address = $row['contact_address'];
		$contact_email = $row['contact_email'];
		$contact_phone = $row['contact_phone'];
		$contact_fax = $row['contact_fax'];
		$contact_map_iframe = $row['contact_map_iframe'];
	}
?>
<div class="contact-area bg-area">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-6">
				<div class="contact-item">
					<div class="contact-text">
						<i class="fa fa-map-marker"></i>
						<h3>Address</h3>
						<p><?php echo $contact_address; ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="contact-item">
					<div class="contact-text">
						<i class="fa fa-phone"></i>
						<h3>Email</h3>
						<p><?php echo $contact_email; ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="contact-item">
					<div class="contact-text">
						<i class="fa fa-envelope-o"></i>
						<h3>Phone</h3>
						<p><?php echo $contact_phone; ?></p>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="contact-item">
					<div class="contact-text">
						<i class="fa fa-fax"></i>
						<h3>Fax</h3>
						<p><?php echo $contact_fax; ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">

<?php
// After form submit checking everything for email sending
if(isset($_POST['form_contact']))
{
	$error_message = '';
	$success_message = '';
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$receive_email = $row['receive_email'];
		$receive_email_subject = $row['receive_email_subject'];
		$receive_email_thank_you_message = $row['receive_email_thank_you_message'];
	}

    $valid = 1;

    if(empty($_POST['visitor_name']))
    {
        $valid = 0;
        $error_message .= 'Please enter your name.\n';
    }

    if(empty($_POST['visitor_phone']))
    {
        $valid = 0;
        $error_message .= 'Please enter your phone number.\n';
    }


    if(empty($_POST['visitor_email']))
    {
        $valid = 0;
        $error_message .= 'Please enter your email address.\n';
    }
    else
    {
    	// Email validation check
        if(!filter_var($_POST['visitor_email'], FILTER_VALIDATE_EMAIL))
        {
            $valid = 0;
            $error_message .= 'Please enter a valid email address.\n';
        }
    }

    if(empty($_POST['visitor_comment']))
    {
        $valid = 0;
        $error_message .= 'Please enter your comment.\n';
    }

    if($valid == 1)
    {
		
		$visitor_name = strip_tags($_POST['visitor_name']);
		$visitor_email = strip_tags($_POST['visitor_email']);
		$visitor_phone = strip_tags($_POST['visitor_phone']);
		$visitor_comment = strip_tags($_POST['visitor_comment']);

        // sending email
        $to_admin = $receive_email;
        $subject = $receive_email_subject;
		$message = '
<html><body>
<table>
<tr>
<td>Name</td>
<td>'.$visitor_name.'</td>
</tr>
<tr>
<td>Email</td>
<td>'.$visitor_email.'</td>
</tr>
<tr>
<td>Phone</td>
<td>'.$visitor_phone.'</td>
</tr>
<tr>
<td>Comment</td>
<td>'.nl2br($visitor_comment).'</td>
</tr>
</table>
</body></html>
';
		$headers = 'From: ' . $visitor_email . "\r\n" .
				   'Reply-To: ' . $visitor_email . "\r\n" .
				   'X-Mailer: PHP/' . phpversion() . "\r\n" . 
				   "MIME-Version: 1.0\r\n" . 
				   "Content-Type: text/html; charset=ISO-8859-1\r\n";

		// Sending email to admin				   
        mail($to_admin, $subject, $message, $headers); 
		
        $success_message = $receive_email_thank_you_message;

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



				<div class="contact-form">
					<form accept="<?php echo BASE_URL.URL_PAGE.$_REQUEST['slug']; ?>" method="post">
						<div class="form-row">

							<div class="form-group">
								<label for="">Full Name</label>
								<input type="text" class="form-control" name="visitor_name" placeholder="Full Name">
							</div>

							<div class="form-group">
								<label for="">Phone Number</label>
								<input type="text" class="form-control" name="visitor_phone" placeholder="Phone Number">
							</div>

							<div class="form-group">
								<label for="">Email</label>
								<input type="text" class="form-control" name="visitor_email" placeholder="Email">
							</div>


							<div class="form-group">
								<label for="">Massege</label>
								<textarea class="form-control" name="visitor_comment"></textarea>
							</div>

							<button type="submit" class="btn btn-primary" name="form_contact">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="map-area">
					<?php echo $contact_map_iframe; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php endif; ?>


<?php if($page_layout == 'Blog Page Layout'): ?>
<div class="blog-page-area">
	<div class="container">
		<div class="row">

			<?php
			$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
			$statement->execute();
			$total = $statement->rowCount();
			?>

			<?php if(!$total): ?>
			<p style="color:red;">Sorry! No News is found.</p>
			<?php else: ?>

			<?php
			/* ===================== Pagination Code Starts ================== */
			$adjacents = 10;	
			
			$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
			$statement->execute();
			$total_pages = $statement->rowCount();
			
			$targetpage = $_SERVER['PHP_SELF'];
			$limit = 6;                                 
			$page = @$_GET['page'];
			if($page) 
				$start = ($page - 1) * $limit;          
			else
				$start = 0;	
			

			$statement = $pdo->prepare("SELECT
									   t1.news_title,
			                           t1.news_slug,
			                           t1.news_content,
			                           t1.news_date,
			                           t1.photo,
			                           t1.category_id,

			                           t2.category_id,
			                           t2.category_name,
			                           t2.category_slug
			                           FROM tbl_news t1
			                           JOIN tbl_category t2
			                           ON t1.category_id = t2.category_id 		                           
			                           ORDER BY t1.news_id 
			                           LIMIT $start, $limit");
			$statement->execute();
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			
			$s1 = $_REQUEST['slug'];
					
			if ($page == 0) $page = 1;                  
			$prev = $page - 1;                          
			$next = $page + 1;                          
			$lastpage = ceil($total_pages/$limit);      
			$lpm1 = $lastpage - 1;   
			$pagination = "";
			if($lastpage > 1)
			{   
				$pagination .= "<div class=\"pagination\">";
				if ($page > 1) 
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$prev\">&#171; previous</a>";
				else
					$pagination.= "<span class=\"disabled\">&#171; previous</span>";    
				if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
				{   
					for ($counter = 1; $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
				}
				elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
				{
					if($page < 1 + ($adjacents * 2))        
					{
						for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
						{
							if ($counter == $page)
								$pagination.= "<span class=\"current\">$counter</span>";
							else
								$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
						}
						$pagination.= "...";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
					}
					elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
					{
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
						$pagination.= "...";
						for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
						{
							if ($counter == $page)
								$pagination.= "<span class=\"current\">$counter</span>";
							else
								$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
						}
						$pagination.= "...";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
					}
					else
					{
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
						$pagination.= "...";
						for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
						{
							if ($counter == $page)
								$pagination.= "<span class=\"current\">$counter</span>";
							else
								$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
						}
					}
				}
				if ($page < $counter - 1) 
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$next\">next &#187;</a>";
				else
					$pagination.= "<span class=\"disabled\">next &#187;</span>";
				$pagination.= "</div>\n";       
			}
			/* ===================== Pagination Code Ends ================== */
			?>
			
			<?php
			foreach ($result as $row) {
				?>
				<div class="col-md-4 col-sm-6 col-xs-12 blog-item">
					<div class="latest-item">
						<div class="latest-photo" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>)"></div>
						<div class="latest-text">
							<h2><a href="<?php echo BASE_URL.URL_NEWS.$row['news_slug']; ?>"><?php echo $row['news_title']; ?></a></h2>
							<ul>
								<li>Category: <a href="<?php echo BASE_URL.URL_CATEGORY.$row['category_slug']; ?>"><?php echo $row['category_name']; ?></a></li>
								<li>Date: <?php echo $row['news_date']; ?></li>
							</ul>
							<div class="latest-pra">
								<p>
									<?php echo substr($row['news_content'],0,200).' ...'; ?>
								</p>
								<a href="<?php echo BASE_URL.URL_NEWS.$row['news_slug']; ?>">Read more</a>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>							
			<?php endif; ?>
			
			<div class="col-md-12">
				<?php if($total): ?>
				<?php echo $pagination; ?>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>
<?php endif; ?>


<?php if($page_layout == 'Testimonial Page Layout'): ?>
<div class="testimonial-area testimonial-page">
	<div class="container">
		<div class="row">
			<div class="testimonial-gallery owl-carousel">
				<?php
					$statement = $pdo->prepare("SELECT * FROM tbl_testimonial");
					$statement->execute();
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);						
					foreach ($result as $row) {
						?>
						<div class="testimonial-item">
							<div class="testimonial-photo" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>)"></div>
							<div class="testimonial-text">
								<h2><?php echo $row['name']; ?></h2>
								<h3><?php echo $row['designation'].'('.$row['company'].')'; ?></h3>
								<div class="testimonial-pra">
									<p>
										<?php echo $row['comment']; ?>
									</p>
								</div>
							</div>
						</div>
						<?php
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>


<?php if($page_layout == 'Pricing Page Layout'): ?>
<div class="packages-area bg-area">
	<div class="container">
		<div class="row">

			<?php
			$statement = $pdo->prepare("SELECT * FROM tbl_pricing_plan");
			$statement->execute();
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);					
			foreach ($result as $row) {
				?>
				<div class="col-md-4 col-sm-6">
					<div class="packages-item">
						<div class="pack-head">
							<h3><?php echo $row['pricing_plan_name']; ?></h3>
							<h2>$<?php echo $row['pricing_plan_price']; ?></h2>
							<h4 class="first">Active Days: <?php echo $row['pricing_plan_day']; ?></h4>
							<h4>Number of Items Allowed: <?php echo $row['pricing_plan_item_allow']; ?></h4>
						</div>
						<div class="pack-body">
							<?php echo $row['pricing_plan_description']; ?>
						</div>
						<div class="pack-footer">
							<a href="<?php echo BASE_URL; ?>login.php">Buy Now</a>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<?php endif; ?>



<?php if($page_layout == 'New Car Page Layout'): ?>
<div class="lesting-area bg-area">
	<div class="container">
		<div class="row">


<?php 
    $adjacents = 10;

    $statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=1 AND car_condition=?");
	$statement->execute(array('New Car'));
    $total_pages = $statement->rowCount();

    $targetpage = $_SERVER['PHP_SELF'];
    $limit = 6;                                 
    $page = @$_GET['page'];
    if($page) 
        $start = ($page - 1) * $limit;          
    else
        $start = 0;

    $statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=1 AND car_condition=? ORDER BY car_id DESC LIMIT $start, $limit");
	$statement->execute(array('New Car'));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);


    if($page == 0) $page = 1;                  
    $prev = $page - 1;                          
    $next = $page + 1;                          
    $lastpage = ceil($total_pages/$limit);      
    $lpm1 = $lastpage - 1;   
    $pagination = "";
    if($lastpage > 1)
    {   
        $pagination .= "<div class=\"pagination\">";
        if ($page > 1) 
            $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$prev\">&#171; previous</a>";
        else
            $pagination.= "<span class=\"disabled\">&#171; previous</span>";    
        if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
        {   
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination.= "<span class=\"current\">$counter</span>";
                else
                    $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$counter\">$counter</a>";                 
            }
        }
        elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
        {
            if($page < 1 + ($adjacents * 2))        
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$counter\">$counter</a>";                 
                }
                $pagination.= "...";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$lastpage\">$lastpage</a>";       
            }
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."1\">1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."2\">2</a>";
                $pagination.= "...";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$counter\">$counter</a>";                 
                }
                $pagination.= "...";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$lastpage\">$lastpage</a>";       
            }
            else
            {
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."1\">1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."2\">2</a>";
                $pagination.= "...";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$counter\">$counter</a>";                 
                }
            }
        }
        if ($page < $counter - 1) 
            $pagination.= "<a href=\"".BASE_URL."page.php?slug=new-car&page="."$next\">next &#187;</a>";
        else
            $pagination.= "<span class=\"disabled\">next &#187;</span>";
        $pagination.= "</div>\n";       
    }
?>


			
				
				<?php
				if($total_pages==''):
					echo '<div class="error" style="font-size:24px;margin-top:20px;">Sorry! No Car is Found.</div>';
				else:
					foreach ($result as $row) {

						$seller_id = $row['seller_id'];
						$today = date('Y-m-d');

						$valid = 0;
						$statement1 = $pdo->prepare("SELECT * FROM tbl_payment WHERE seller_id=? AND payment_status=?");
						$statement1->execute(array($seller_id,'Completed'));
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);							
						foreach ($result1 as $row1) {
							if(($today>=$row1['payment_date'])&&($today<=$row1['expire_date'])) {
								$valid = 1;
								break;
							}
						}
						if($valid == 1):
						$statement1 = $pdo->prepare("SELECT * FROM tbl_car_category WHERE car_category_id=?");
						$statement1->execute(array($row['car_category_id']));
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
						$tot = $statement1->rowCount();
						foreach ($result1 as $row1) {
							$car_category_name = $row1['car_category_name'];
						}
						?>
						<div class="col-md-6 col-sm-12 listing-item-car-condition">
							<div class="row listing-item">
								<div class="col-md-4 col-sm-4 col-xs-12 listing-photo" style="background-image: url(<?php echo BASE_URL.'assets/uploads/cars/'.$row['featured_photo']; ?>)"></div>
								
								<div class="col-md-4 col-sm-4 col-xs-6 listing-text">
									<h2><?php echo $row['title']; ?></h2>
									<ul>
										<li>Type: <span><?php if($tot!=''){echo $car_category_name;} else{echo 'Not Specified';} ?></span></li>
										<li>Mileage: <span><?php if($row['mileage']!=''){echo $row['mileage'];} else{echo 'Not Specified';} ?></span></li>
										<li>Year: <span><?php if($row['year']!=0){echo $row['year'];} else{echo 'Not Specified';} ?></span></li>
									</ul>
								</div>
								<div class="col-md-4 col-sm-4 col-xs-6 listing-price">
									<h2>
										<?php if($row['regular_price']!=$row['sale_price']): ?>
											<del>$<?php echo $row['regular_price']; ?></del>
											$<?php echo $row['sale_price']; ?>
										<?php else: ?>
											$<?php echo $row['sale_price']; ?>
										<?php endif; ?>
									</h2>
									<a href="<?php echo BASE_URL.URL_CAR.$row['car_id']; ?>">View Detail</a>
								</div>									
							</div>
						</div>
						<?php
						endif;
					}
				endif;									
				?>
				<div class="row">
					<div class="col-md-12">
						<?php echo $pagination; ?>
					</div>
				</div>
		</div>
	</div>
</div>
<?php endif; ?>



<?php if($page_layout == 'Old Car Page Layout'): ?>
<div class="lesting-area bg-area">
	<div class="container">
		<div class="row">
<?php 
    $adjacents = 10;

    $statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=1 AND car_condition=?");
	$statement->execute(array('Old Car'));
    $total_pages = $statement->rowCount();

    $targetpage = $_SERVER['PHP_SELF'];
    $limit = 6;                                 
    $page = @$_GET['page'];
    if($page) 
        $start = ($page - 1) * $limit;          
    else
        $start = 0;

    $statement = $pdo->prepare("SELECT * FROM tbl_car WHERE status=1 AND car_condition=? ORDER BY car_id DESC LIMIT $start, $limit");
	$statement->execute(array('Old Car'));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);


    if($page == 0) $page = 1;                  
    $prev = $page - 1;                          
    $next = $page + 1;                          
    $lastpage = ceil($total_pages/$limit);      
    $lpm1 = $lastpage - 1;   
    $pagination = "";
    if($lastpage > 1)
    {   
        $pagination .= "<div class=\"pagination\">";
        if ($page > 1) 
            $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$prev\">&#171; previous</a>";
        else
            $pagination.= "<span class=\"disabled\">&#171; previous</span>";    
        if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
        {   
            for ($counter = 1; $counter <= $lastpage; $counter++)
            {
                if ($counter == $page)
                    $pagination.= "<span class=\"current\">$counter</span>";
                else
                    $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$counter\">$counter</a>";                 
            }
        }
        elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
        {
            if($page < 1 + ($adjacents * 2))        
            {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$counter\">$counter</a>";                 
                }
                $pagination.= "...";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$lastpage\">$lastpage</a>";       
            }
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."1\">1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."2\">2</a>";
                $pagination.= "...";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$counter\">$counter</a>";                 
                }
                $pagination.= "...";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$lastpage\">$lastpage</a>";       
            }
            else
            {
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."1\">1</a>";
                $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."2\">2</a>";
                $pagination.= "...";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$counter\">$counter</a>";                 
                }
            }
        }
        if ($page < $counter - 1) 
            $pagination.= "<a href=\"".BASE_URL."page.php?slug=old-car&page="."$next\">next &#187;</a>";
        else
            $pagination.= "<span class=\"disabled\">next &#187;</span>";
        $pagination.= "</div>\n";       
    }
?>


			
				
				<?php
				if($total_pages==''):
					echo '<div class="error" style="font-size:24px;margin-top:20px;">Sorry! No Car is Found.</div>';
				else:
					foreach ($result as $row) {

						$seller_id = $row['seller_id'];
						$today = date('Y-m-d');

						$valid = 0;
						$statement1 = $pdo->prepare("SELECT * FROM tbl_payment WHERE seller_id=? AND payment_status=?");
						$statement1->execute(array($seller_id,'Completed'));
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);							
						foreach ($result1 as $row1) {
							if(($today>=$row1['payment_date'])&&($today<=$row1['expire_date'])) {
								$valid = 1;
								break;
							}
						}
						if($valid == 1):
						$statement1 = $pdo->prepare("SELECT * FROM tbl_car_category WHERE car_category_id=?");
						$statement1->execute(array($row['car_category_id']));
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
						$tot = $statement1->rowCount();
						foreach ($result1 as $row1) {
							$car_category_name = $row1['car_category_name'];
						}
						?>
						<div class="col-md-6 col-sm-12 listing-item-car-condition">
							<div class="row listing-item">
								<div class="col-md-4 col-sm-4 col-xs-12 listing-photo" style="background-image: url(<?php echo BASE_URL.'assets/uploads/cars/'.$row['featured_photo']; ?>)"></div>
								
								<div class="col-md-4 col-sm-4 col-xs-6 listing-text">
									<h2><?php echo $row['title']; ?></h2>
									<ul>
										<li>Type: <span><?php if($tot!=''){echo $car_category_name;} else{echo 'Not Specified';} ?></span></li>
										<li>Mileage: <span><?php if($row['mileage']!=''){echo $row['mileage'];} else{echo 'Not Specified';} ?></span></li>
										<li>Year: <span><?php if($row['year']!=0){echo $row['year'];} else{echo 'Not Specified';} ?></span></li>
									</ul>
								</div>
								<div class="col-md-4 col-sm-4 col-xs-6 listing-price">
									<h2>
										<?php if($row['regular_price']!=$row['sale_price']): ?>
											<del>$<?php echo $row['regular_price']; ?></del>
											$<?php echo $row['sale_price']; ?>
										<?php else: ?>
											$<?php echo $row['sale_price']; ?>
										<?php endif; ?>
									</h2>
									<a href="<?php echo BASE_URL.URL_CAR.$row['car_id']; ?>">View Detail</a>
								</div>									
							</div>
						</div>
						<?php
						endif;
					}
				endif;									
				?>
				<div class="row">
					<div class="col-md-12">
						<?php echo $pagination; ?>
					</div>
				</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php require_once('footer.php'); ?>