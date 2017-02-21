<?php
include('./includes/init.php');
//include('languages/en/inc_policies_signature.php');

/* TODO: the function below is called second time here.. need to call this once only in init.php.. but before that need to resolve variables conflict n other pages*/
$chk_in=get_value_post_get('chk_in');
$chk_out=get_value_post_get('chk_out');
$adults=get_value_post_get('selAdults');
if($adults =='' || $adults < 0){
	$adults='2';
}
$children=get_value_post_get('selChildren');
if($children == '' || $children < 0 ){$children='0';}

$rooms=1;

//$chk_in $chk_out $adults $children $rooms

if('' != $chk_in && valid_date($chk_in) && '' != $chk_out && valid_date($chk_out)){
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);
	
	/*
	* set search criteria in session
	*/
	$_SESSION['selAdults']=$adults;
	$_SESSION['selChildren']=$children;

	$_SESSION['start_date']=$chk_in;
	$_SESSION['end_date']=$chk_out;
	
	
	
	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
	$backout_date = mktime(0, 0, 0, $checkout[1], $checkout[0]-1, $checkout[2]);
	
	$nights = calculate_nights_stay($chk_in,$chk_out);
	
	$arryPrice = array();
	//$backout_date = array();
	$roomstyle = array();
	$roomsstyle = array();
	$found=0;
	$format = "json";
	
	$api = 'http://localhost:8080/serenity/api/searchroom?chkin='.$startdate.'&chckout='.$backout_date.'&adults='.$adults.'&children='.$children.'&chk_in='.$chk_in.'&chk_out='.$chk_out;
	$cUrl = $api;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $cUrl);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$res = curl_exec($ch);
	curl_close($ch);
	
	$rs = json_decode($res, true);
	
	
	foreach($rs[allotment] as $r=>$value) {
		$r = $value;
		$cnt = count($r);
		if($cnt > 0){
			
			if($r[rooms]==0){
			
				array_push($arryPrice, array('0', $r[rooms]));
			}else{
			
				array_push($arryPrice, array($r[tblallotment_price], $r[rooms]));
			}
			if($r[rooms]>0){
				array_push($roomsstyle, $r[tblallotment_price]);
				$found++;
			}
		}else{
			
			array_push($arryPrice, array('0', 0));
		}
	}
	
	if($found==0){
		$message_rooms_not_available='
			Dear Guest!<br/><br/>
			Thank you very much for choosing Serenity Resort & Residences for your next holidays in Phuket.
			<br/><br/>
			We are fully booked on certain room types for certain dates already, but you may select your dates and room category from the grid below.
			<br/><br/>
			Looking forward to welcoming you, we remain,
		';
	}else{
		/*
		* check minimum nights
		*/
		
		if(date_between($startdate) || date_between($enddate)){
			//check if customer is booking at least 3 days
			if($nights < 1){
				$message='Minimum of 1 nights stay is required between Dec 27, 2016 and Jan 8, 2017';
			}
		}	
	}
}else{
	$message='Please select dates and search again.';
}

if(isset($message_rooms_not_available) && $message_rooms_not_available !=''){

	$_SESSION['search_rooms_message']=$message_rooms_not_available;
	//header('Location: search-rooms-message.php');
	//exit;
}


/*
* get room names, guests allowed per room
*/

$rooms_desc_array=array();
$roomname = $rs[roomname];
if(is_array($roomname) && sizeOf($roomname) > 0){
	$row_rooms = $roomname;
		$rooms_desc_array[$row_rooms['id']]= array('name'=>$row_rooms['name'],'guests_allowed'=>$row_rooms['guests_allowed']);
	
}else{
	$message="There was problem getting rooms information.";
}

/**
* airport transfer check 
*/

$airport_transfer_check_date=strtotime("10/31/2013");
$airport_transfer_checkin_date=explode("/",$chk_in);
$airport_transfer_checkin=strtotime($airport_transfer_checkin_date[1] . "/" . $airport_transfer_checkin_date[0]. "/" . $airport_transfer_checkin_date[2]);
?>
<!DOCTYPE html>
<html>
	<head>

		<!-- Basic -->
		<meta charset="utf-8">
		<title>Search for Rates and Availability at The Serenity Resort Rawai Phuket</title>
		<meta name="keywords" content="phuket hotel, phuket resort, villa, beachfront resort, beachfront villa, phuket Residences, phuket villa, beachfront property thailand, Luxury Hotels, 5 star hotel in phuket" />
		<meta name="description" content="Search for the Best Available Rates and latest availability of Rooms, Suites and Residences at The Serenity Phuket Resort in Phuket Thailand." />
		
		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Web Fonts  -->
        <!--<link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">-->
		<link href="production/css/01b_google_font.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/bootstrap.css">
		<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css">
		<link rel="stylesheet" href="css/font-awesome.css">
		<link rel="stylesheet" href="vendor/owlcarousel/owl.carousel.min.css" media="screen">
		<link rel="stylesheet" href="vendor/owlcarousel/owl.theme.default.min.css" media="screen">
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" media="screen">

		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css">
		<link rel="stylesheet" href="css/theme-elements.css">
		<link rel="stylesheet" href="css/theme-blog.css">
		<link rel="stylesheet" href="css/theme-shop.css">
		<link rel="stylesheet" href="css/theme-animate.css">

		<!-- Current Page CSS -->
		<link rel="stylesheet" href="vendor/rs-plugin/css/settings.css" media="screen">
		<link rel="stylesheet" href="vendor/circle-flip-slideshow/css/component.css" media="screen">

		<!-- Skin CSS -->
		<link rel="stylesheet" href="css/skins/default.css">

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Head Libs -->
		<script src="vendor/modernizr/modernizr.js"></script>

		<!--[if IE]>
			<link rel="stylesheet" href="css/ie.css">
		<![endif]-->

		<!--[if lte IE 8]>
			<script src="vendor/respond/respond.js"></script>
			<script src="vendor/excanvas/excanvas.js"></script>
		<![endif]-->
		
		
		<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css" />
		
		<!-- booking system -->
		<link type="text/css" href="css/booking.css" rel="stylesheet" />
		<link type="text/css" href="css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link href="js/impromptu/2.8/impromptu.css" rel="stylesheet" type="text/css" />			
		
		<!-- Currency Converter -->
		<link rel="stylesheet" href="css/pagestyle.css">
        <!--<link rel="stylesheet" href="css/jquery.jscc.min.css">-->
	
		
        <link href="css/custom-notif.css" rel="stylesheet" type="text/css" />
        <style>
        #myModal {
            z-index : 2000;
        }
        #save_search {
            z-index : 2000;
        }
        body.sticky-menu-active #header {
            top : -85px;
        }
        </style>
		<?php // include("./includes/common.googleanalytics.php"); ?>
		<script src="vendor/jquery/jquery.js"></script>
	</head>
	<body>

		<div class="body">
		
            <!-- NAV START -->

            <header id="header" class="flat-menu clean-top">
                <div class="header-top" style="display:initial">
                    <div class="container">
                        <div class="row" style="padding:0px 10px 0px 10px">
                            Get in touch! <span><i class="fa fa-phone"></i>+66 (0) 76 371 900</span> | <a href="mailto:rsvn@serenityphuket.com">rsvn@serenityphuket.com</a>
                            <div class="dropdown pull-right">
                                <a class="dropdown-toggle" href="#" id="cr_cur" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="text-decoration: none;">
                                    <b>
                                    Currency : <span id="cur_code"><?php echo $_COOKIE['currency']!='' ? $_COOKIE['currency'] : 'THB'; ?></span>
                                    <span class="caret"></span>
                                    </b>
                                </a>
                                <ul class="dropdown-menu change_cur" aria-labelledby="cr_cur">
                                    <li data-cur="THB"><a style="cursor: pointer;">THB (Thai Baht)</a></li>
                                    <li data-cur="USD"><a style="cursor: pointer;">USD (US Dollar)</a></li>
                                    <li data-cur="EUR"><a style="cursor: pointer;">EUR (EURO)</a></li>
                                    <li data-cur="GBP"><a style="cursor: pointer;">GBP (British Pound)</a></li>
                                    <li data-cur="AUD"><a style="cursor: pointer;">AUD (Australian Dollar)</a></li>
                                    <li data-cur="CNY"><a style="cursor: pointer;">CNY (Chinese Yuan)</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="logo">
                        <a href="index.php">
                            <img alt="Serenity Resort & Residences Luxury hotel in Rawai Phuket" width="140" height="63" data-sticky-width="120" data-sticky-height="54" src="img/logo.png">
                        </a>
                    </div>
                    <button class="btn btn-responsive-nav btn-inverse" data-toggle="collapse" data-target=".nav-main-collapse">
                        <i class="fa fa-bars"></i>
                    </button>
                </div>
                <div class="navbar-collapse nav-main-collapse collapse">
                    <div class="container">
                        <nav class="nav-main mega-menu">
                            <ul class="nav nav-pills nav-main" id="mainMenu">
                                
                                <li <?php if ($current_filename =='' || $current_filename=='index.php'){ ?>class="active" <?php } ?>>
                                    <a href="index.php">Home</a>
                                </li>
                                
                                
                                <?php if($current_filename != 'contact-information.php' && $current_filename != 'contact-information-package.php' && $current_filename != 'contact-information.php' && $current_filename != 'contact-information-long-stay.php'){ ?>
                                
                                    <li class="dropdown <?php if ($current_filename=='Suites_and_Residences_%2520LandingPage.php' || $current_filename=='serenity_rooms.php' || $current_filename=='serenity_suite.php' || $current_filename=='serenity_sea_view_suite.php' || $current_filename=='serenity_grand_suite.php' || $current_filename=='serenity_penthouse_suite.php' || $current_filename=='h20_suite.php' || $current_filename=='the_residences.php' || $current_filename=='best_rate_guarantee.php' || $current_filename=='serenity-long-term-rentals.php' ){ ?> active <?php } ?>">
                                        <a class="dropdown-toggle" href="Suites_and_Residences_%2520LandingPage.php">Accommodation<i class="fa fa-angle-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="serenity_rooms.php">Serenity Room</a></li>
                                                    <li><a href="serenity_suite.php">Serenity Suite</a></li>
                                                    <li><a href="serenity_sea_view_suite.php">Serenity Seaview Suite</a></li>
                                                    <li><a href="serenity_grand_suite.php">2 Bed Grand Seaview Suite</a></li>
                                                    <li><a href="serenity_penthouse_suite.php">2 Bed Penthouse Seaview Suite</a></li>
                                                    <li><a href="h20_suite.php">H20 Suite</a></li>
                                                    <li><a href="the_residences.php">2 Bed Pool Residence</a></li>
                                                    <li><a href="best_rate_guarantee.php">BEST RATE GUARANTEE</a></li>
                                                    <li><a href="serenity-long-term-rentals.php">Long Stay Rentals</a></li>
                                                </ul>
                                    </li>

                                    <li class="dropdown <?php if ($current_filename =='east_88.php' || $current_filename=='east-88-events.php' || $current_filename == 'weddings_events.php' || $current_filename =='east-88-events.php' || $current_filename =='4-cocktails.php' || $current_filename =='chinese-new-year.php' || $current_filename =='christmas_event.php' || $current_filename == 'drink-all-day.php' || $current_filename == 'fullmoon-party.php' || $current_filename == 'happy-hour.php' || $current_filename == 'new_year.php' || $current_filename =='rugby-world-cup.php' || $current_filename =='welcome_to_thailand.php'){ ?> active <?php } ?>">
                                        <a class="dropdown-toggle" href="east_88.php">Dining &amp; Events<i class="fa fa-angle-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="east_88.php">Bar &amp; Restaurant</a></li>
                                                    <li><a href="east-88-events.php">Upcoming Events</a></li>

                                                    <li><a href="weddings_events.php">Weddings &amp; Events</a></li>
                                                </ul>
                                    </li>

                                    <li class="dropdown <?php if ($current_filename =='spa.php' || $current_filename=='balance-fitness.php'){ ?> active <?php } ?>">
                                        <a class="dropdown-toggle" href="spa.php">Wellness<i class="fa fa-angle-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="spa.php">Spa Treatments</a></li>
                                                    <li><a href="phuket-wellness-holidays.php">Wellness Holidays</a></li>
                                                </ul>
                                    </li>

                                    <li <?php if ($current_filename=='gallery.php'){ ?>class="active" <?php } ?>>
                                        <a href="gallery.php">Gallery</a>
                                    </li>

                                    <li <?php if ($current_filename=='packages.php'){ ?>class="active" <?php } ?>>
                                        <a href="packages.php" style="color:#e4382f">Offers</a>
                                    </li>

                                    <li class="dropdown <?php if ($current_filename =='activities.php'){ ?> active <?php } ?>">
                                        <a class="dropdown-toggle" href="/blog/">Area Guide<i class="fa fa-angle-down"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="activities.php">Activities</a></li>
                                                    <li><a href="/blog/">Phuket Blog</a></li>
                                                </ul>
                                    </li>

                                    <li <?php if ($current_filename=='serenity_apartment_for_sale.php'){ ?>class="active" <?php } ?>>
                                        <a href="serenity_apartment_for_sale.php">Investments</a>
                                    </li>
                                <?php } ?>		
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>

            <!-- NAV END -->

			<div role="main" class="main">
				<div class="slider-container">
					<div class="slider" id="revolutionSlider" data-plugin-revolution-slider data-plugin-options='{"startheight": 500}'>
						<ul>
							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-1.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>
							
							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-2.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-3.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-4.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-5.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-6.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/booking-7.jpg" data-bgfit="cover" data-bgposition="center top" data-bgrepeat="no-repeat">
							</li>

						</ul>
					</div>
				</div>

				<div class="home-intro light">
					<div class="container" style="padding-top:20px">
						<?php if('' == $message){?>
							<div class="col-md-4">
                                <h2>Your <strong>search results:</strong></h2>
                            </div>
							<div class="col-md-8" style="padding-top:10px;">
                                <h4><strong>Dates:</strong> <?php echo date("M jS",$startdate);?> - <?php echo date("jS M",$enddate);?> - <span><strong>Adults:</strong></span> <?php if($adults > 0) echo $adults;?> - <span><strong>Children:</strong></span> <?php echo $children;?></h4>
                            </div>
							<div class="clear"></div>
                            <div class="col-sm-12" style="text-align:center;padding-bottom:30px">
                                <button type="button" id="save-search" class="btn btn-primary" data-toggle="modal" data-target="#save_search"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> &nbsp;Email Me This Search</button>
                                <!-- Modal Start-->

                                <div class="modal fade" tabindex="-1" role="dialog" id="save_search">
                                    <div class="modal-dialog" style="width:300px;margin:auto;margin-top:15%">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Email Me This Search
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row" style="padding-left:20px;padding-right:20px" id="body_save_search">
                                                    <form id="form_save_search">
                                                        <div class="input-group">
                                                            <input class="form-control" placeholder="Email Address" type="email" id="fs_email"/>
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-default" id="btn_save_search" style="background-color:#9b8a3b;color:#f0f0f0" type="submit"><span id="span_save_search"></span> Submit</button> 
                                                            </span>
                                                        </div>
                                                    </form>
                                                </div>
                                                <br/>
                                                <label id="label_save_search"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal End-->

                            </div>
						<?php } ?>
					</div>
				</div>

				<div class="container">

					<div class="row">
						<div class="col-md-8">
						
							<?php if('' == $message){?>

								<?php 
									/*
										swimming pool maintence period
									*/
									/*
									echo swimming_pool_under_maintence($startdate,$enddate);
									*/
									
								?>
								
									<!-- serenity room-->
								<?php if(($adults  + $children)< 3 && $arryPrice[6][1] > 0){?>
									
									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/serenity-rooms-thumb.jpg">
												
												<?php 
													$incentives=$rs[incentives][7];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
														
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_7').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_7" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center><a href="" onclick="toggle('serenity_room_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a></center>
											
											</div>
												
												
											<div class="col-md-7">
												<span class="room_title"><h4><?php  echo $rs[rooms][6][name] ?></h4></span>
												<p><?php  echo $rs[rooms][6][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 2 Adults or 1 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 2 Adults or 1 Adult and 1 Child"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_rooms.php" onclick="return toggle('serenity_rooms_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>
												
												<div>
													<div class="fright small-text">
														<?php
															
																//echo 'Normal Price: <span class="hightlight-text small-text">' . format_currency($row_price['total']) . '</span>';
																$discount=$rs[discount][7];
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
															
														?>
													</div>
												</div>										
												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[6][0]);?>"><?php echo  format_currency($arryPrice[6][0]);?></span>&nbsp;&nbsp;<?php } ?>  <span id="applicable_price_show_1" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[6][0]  - ($discount/$nights) );?>"><?php echo format_currency($arryPrice[6][0]  - ($discount/$nights) );?></span></h6>
												
												<form name="frm_serenity_guest_rooms" id="frm_serenity_guest_rooms" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[7]['name']?>',<?php echo $rooms_desc_array[7]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">
													
													<input type="hidden" id="applicable_price_1" name="applicable_price_1" value="<?php echo round($arryPrice[6][0]  - ($discount/$nights) );?>"/>
													
													<input type="hidden" name="room_type" value="7">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[6][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>													
												
											</div>
										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_rooms_toggle" style="display: none">
												<div>
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 1 King Bed</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 2 Adults or 1 Adult and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: Not available</li>			
													</ul>
												
												Modern conveniences including a flat panel TV, DVD player and sound system, to help you to relax in style. With 30 square meters of luxurious 
												living space, the Serenity Rooms are a great place to rest up before enjoying the many activities within the resort or around Phuket.<br/><br/>
												
												The Serenity Rooms feature a modern style en-suite bathroom as well as supremely comfortable beds, furnished with the finest fabrics, to ensure 
												blissful sleep and make the rooms a comfortable and relaxing place to be.<br/>&nbsp;</div>
												
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Serenity-Room-1.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Room-2.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Room-3.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_rm.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>
											<div id="serenity_room_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family1');return false;" class="small-text"><span id="div_family1_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family1" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation1');return false;" class="small-text"><span id="div_cancellation1_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation1" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>
											<hr />
										</div>	
									</div>	
								
								<?php }?>
									
									
								
								<!-- serenity suite -->	
								<?php if($arryPrice[0][1] > 0){ ?>	
									
									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/serenity-suite-thumb.jpg">
												
												<?php 
													
													$incentives=$rs[incentives][1];
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_7').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_7" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												<center><a href="" onclick="toggle('serenity_suite_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a></center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][0][name] ?></h4></span>
												<p><?php  echo $rs[rooms][0][description] ?>...<br/><br/><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adult and 1 Child (charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_suite.php" onclick="return toggle('serenity_suite_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>
												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][1];
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>										
												
												
												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[0][0]);?>"><?php echo  format_currency($arryPrice[0][0]);?></span>&nbsp;&nbsp;<?php } ?>  <span id="applicable_price_show_2" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[0][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[0][0] - ($discount/$nights));?></span></h6>
												<form name="frm_serenity_garden" id="frm_serenity_garden" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[1]['name']?>',<?php echo $rooms_desc_array[1]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_2" name="applicable_price_2" value="<?php echo round($arryPrice[0][0] - ($discount/$nights));?>"/>
													
													<input type="hidden" name="room_type" value="1">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[0][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>												
												
											</div>

										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_suite_toggle" style="display: none">
												<div>
												
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 1 King Bed</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 3 Adults or 2 Adult and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 1 per room</li>			
													</ul>

												The fully equipped kitchen lets you fix your favourite meals or just treat yourself to the minibar in style.<br/><br/> 
												
												A large balcony with outdoor seating and sun loungers links to the interior living area through large sliding glass doors 
												and provide a beautiful and private way to enjoy the tropical surroundings. <br/><br/>
												
												Each of the Serenity Suites has entertainment centres with flat screen TVs, DVD and CD players. Supremely comfortable beds 
												furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
												
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Serenity-Suite-1-Large.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Serenity-Suite-1-Thumbnail.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Suite-2.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Suite-3.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_suite.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
												
											</div>	
											<div id="serenity_suite_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family2');return false;" class="small-text"><span id="div_family2_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family2" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation2');return false;" class="small-text"><span id="div_cancellation2_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation2" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>

												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>									
								<?php }?>	
									
								
								<!-- Serenity Seaview Suite -->	
								<?php if($arryPrice[1][1] > 0){ ?>

									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/serenity-suite-seaview-thumb.jpg">
												
												<?php 
													$incentives= $rs[incentives][2];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img  src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>"  onclick="$('#dialog_2').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_2" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_seaview_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][1][name] ?></h4></span>
												<p><?php  echo $rs[rooms][1][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adult and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full Sea View"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_sea_view_suite.php" onclick="return toggle('serenity_sea_view_suite_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>
												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][2];											
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>										
												
												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[1][0]);?>"><?php echo  format_currency($arryPrice[1][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_3" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[1][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[1][0] - ($discount/$nights));?></span></h6>
												<form name="frm_serenity_garden" id="frm_serenity_garden" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[2]['name']?>',<?php echo $rooms_desc_array[2]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_3" name="applicable_price_3" value="<?php echo round($arryPrice[1][0] - ($discount/$nights));?>"/>

													<input type="hidden" name="room_type" value="2">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[1][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	
												
											</div>
										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_sea_view_suite_toggle" style="display: none">
												<div>
												
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 1 King Bed</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 3 Adults or 2 Adult and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 1 per room</li>			
													</ul>
									
												The large balcony features an outdoor dining area and the great view can be enjoyed from the entire apartment 
												by opening up the floor to ceiling sliding glass doors. <br/><br/>
												
												The rooftop lounge, accessible through an exterior stairway on the balcony, has an even better view over the 
												bay. A dining area, sun loungers, and even a private Jacuzzi, all make the rooftop lounge an unbeatable 
												spot to enjoy the view.The open living plan of the apartment links the bedroom with the large living 
												area, dining room, and fully equipped European style kitchen.<br/><br/>
												
												Just off the bedroom is a living room area with an entertainment centre with a flat screen TV, DVD and CD player as well 
												as casual seating area ideal for leisurely snacks and meals from the extensive room service menu. Supremely 
												comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
												
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-1.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-2.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-3.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-3.jpg"/></a>&nbsp;
													<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/seaview_suite.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
												
											</div>
											<div id="serenity_seaview_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family3');return false;" class="small-text"><span id="div_family3_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family3" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation3');return false;" class="small-text"><span id="div_cancellation3_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation3" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>
								
								<?php } ?>
								
								
								<!-- Grand Seaview Suite-->
								<?php if($arryPrice[2][1] > 0){ ?>	

									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/serenity-grand-suite-thumb.jpg">
												
												<?php 
													$incentives=$rs[incentives][3];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_3').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_3" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_grandsuite_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][2][name] ?></h4></span>
												<p><?php  echo $rs[rooms][2][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full Sea View"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_grand_suite.php" onclick="return toggle('serenity_grand_suite_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>
												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][3];											
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>										
												
												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[2][0]);?>"><?php echo  format_currency($arryPrice[2][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_4" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[2][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[2][0] - ($discount/$nights));?></span></h6>
												<form name="frm_serenity_grand_suite" id="frm_serenity_grand_suite" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[3]['name']?>',<?php echo $rooms_desc_array[3]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);;">

													<input type="hidden" id="applicable_price_4" name="applicable_price_4" value="<?php echo round($arryPrice[2][0] - ($discount/$nights));?>"/>

													<input type="hidden" name="room_type" value="3">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[2][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	
												
												
											</div>
										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_grand_suite_toggle" style="display: none">
												<div>
												
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 2 King Beds</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 6 Adults or 5 Adult and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 2 per room</li>			
													</ul>
									
												Set amongst beautifully landscaped gardens, the Grand Seaview Suites stunning views of Chalong Bay. The 
												large interior provides plenty of space for up to 5 adults to relax in two private bedrooms, each with 
												en-suite bathrooms and individual entertainment centres. <br/><br/>
												
												Large sliding glass doors open up from the living and dining room area onto a spacious private balcony, 
												offering majestic views over Chalong Bay. The large fully equipped European style kitchen lets you fix 
												your favourite meals or just treat yourself to the minibar in style<br/><br/>
												
												The Grand Seaview Suites bedrooms and living areas each have their own entertainment centres with flat 
												screen TVs, DVD and CD players, with the master bedroom enjoying his and hers sinks, a large bathtub and 
												separate shower area. <br/><br/>
												
												The large balcony areas have beautiful garden and sea views, with these units being located in the front 
												two buildings, closest to the pool and the beachfront. The balconies have an outdoor table and 
												chairs for informal in-room dining and sun loungers for you to relax and enjoy the marvellous views.<br/><br/>
												
												Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;
												</div>
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Grand-Suite-1-Large.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Grand-Suite-1-Thumbnail.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Grand-Suite-2.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Grand-Suite-3.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_suite.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>		
											
											<div id="serenity_grandsuite_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family4');return false;" class="small-text"><span id="div_family4_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family4" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation4');return false;" class="small-text"><span id="div_cancellation4_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation4" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>

								
								<?php } ?>
								
								
								<!-- Penthouse Seaview Suite-->
								<?php if($arryPrice[4][1] > 0){ ?>

									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/serenity-penthouse-thumb.jpg">
												
												<?php 
													$incentives=$rs[incentives][5];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_5').show();"/>
																</div>		
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_5" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_penthouse_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][4][name] ?></h4></span>
												<p><?php  echo $rs[rooms][4][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adult and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full Sea View"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_penthouse_suite.php" onclick="return toggle('serenity_penthouse_suite_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>
												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][5];											
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>										

												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[4][0]);?>"><?php echo  format_currency($arryPrice[4][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_5" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[4][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[4][0] - ($discount/$nights));?></span></h6>
												<form name="frm_two_bedroom_penthouse" id="frm_two_bedroom_penthouse" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[5]['name']?>',<?php echo $rooms_desc_array[5]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_5" name="applicable_price_5" value="<?php echo round($arryPrice[4][0] - ($discount/$nights));?>"/>
												
													<input type="hidden" name="room_type" value="5">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[4][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	

												
											</div>

										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_penthouse_suite_toggle" style="display: none">
												<div>
												
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 2 King Beds</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 6 Adults or 5 Adult and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 2 per room</li>			
													</ul>
									
												The stunning duplex Penthouse Seaview Suites are located on the top floors of the resort, offer gorgeous 
												panoramic views over Chalong Bay, from both the balcony and rooftop lounge area. <br/><br/>
												
												The large Penthouse Seaview Suite apartments offer 2 spacious individual bedrooms, two bathrooms, a 
												living and dining area and a fully equipped European style Kitchen.<br/><br/>
												
												Both Bedrooms and the living room have individual entertainment centres with flat screen TVs and DVD and CD Players.<br/><br/>
												
												Each of the bedrooms also features spacious en-suite bathrooms, with the master bath enjoying his and hers 
												sinks a large bathtub and a separate shower area.<br/>&nbsp;
												</div>
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Penthouse-Suite-1.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Penthouse-Suite-2.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Penthouse-Suite-3.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_ph_suite.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>						

											<div id="serenity_penthouse_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family5');return false;" class="small-text"><span id="div_family5_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family5" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation5');return false;" class="small-text"><span id="div_cancellation5_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation5" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>
								<?php } ?>
								
								
								
								<!-- 3 bedroom penthouse-->
								<!-- we no longer have this -->
								<?php if($arryPrice[7][1] > 0 && 1==2){ ?>
								
									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/thumb/Penthouse3BR/small/1.jpg">
												
												<?php 
													$incentives=$rs[incentives][8];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_8').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_8" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_3brpenthouse_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][7][name] ?></h4></span>
												<p><?php  echo $rs[rooms][7][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 7 Adults or 6 Adults and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full Sea View"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="serenity_penthouse_suite_3bedroom.php" onclick="return toggle('serenity_penthouse_suite_3bedroom_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>

												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][8];											
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>

												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
												
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[7][0]);?>"><?php echo  format_currency($arryPrice[7][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_6" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[7][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[7][0] - ($discount/$nights));?></span></h6>
												<form name="frm_two_bedroom_penthouse" id="frm_two_bedroom_penthouse" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[8]['name']?>',<?php echo $rooms_desc_array[8]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_6" name="applicable_price_6" value="<?php echo round($arryPrice[7][0] - ($discount/$nights));?>"/>

													<input type="hidden" name="room_type" value="8">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[7][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	
												
											</div>

										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="serenity_penthouse_suite_3bedroom_toggle" style="display: none">
												<div>
													The Large 3 bedroom penthouse seaview suite apartments offer three spacious individual bedrooms, three en-suite bathrooms, a living and dining area and a fully equipped European style Kitchen.<br/><br/> Two of the three bedrooms has their own balcony area. 
													The spacious Balconies have beautiful garden and sea views as these units are located in the front two building closest to the pool and beachfront.<br/><br/> The balconies have an outdoor table and chairs for informal in-room dining for you to relax and enjoy the stunning views.<br/>&nbsp;
												</div>
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-1.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-2.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-2.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/3-bed-penthouse-suite-3.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-3.jpg"/></a>&nbsp;
													<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/3bedpenthouse.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>
											
											<div id="serenity_3brpenthouse_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family6');return false;" class="small-text"><span id="div_family6_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family6" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation6');return false;" class="small-text"><span id="div_cancellation6_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation6" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>
								
								<?php } ?>	
								
								
								<!-- H2O Suite-->
								<?php if($arryPrice[3][1] > 0){ ?>
								
									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/h20-suite-thumb.jpg">
												
												<?php 
													$incentives=$rs[incentives][4];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_4').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_4" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_h20_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][3][name] ?></h4></span>
												<p><?php  echo $rs[rooms][3][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 3 Adults or 2 Adults and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full sea view"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="h20_suite.php" onclick="return toggle('h20_suite_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>

												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][4];
																if($discount > 0){
																	//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
																}
																
														?>
													</div>
												</div>

												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[3][0]);?>"><?php echo  format_currency($arryPrice[3][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_7" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[3][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[3][0] - ($discount/$nights));?></span></h6>
												<form name="frm_h20" id="frm_h20" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[4]['name']?>',<?php echo $rooms_desc_array[4]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_7" name="applicable_price_7" value="<?php echo round($arryPrice[3][0] - ($discount/$nights));?>"/>

													<input type="hidden" name="room_type" value="4">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[3][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	
												
												
											</div>

										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="h20_suite_toggle" style="display: none">
												<div>
													
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 1 King Bed</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 3 Adults or 2 Adults and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 1 per room</li>			
													</ul>
									
													The H20 Pool Suites are the perfect romantic getaway offering a beautifully outfitted bedroom with king size 
													bed, a luxurious lounge and adjoining kitchen, balcony, and rooftop lounge with private pool. <br/><br/>
													
													The rooftop lounge's private pool has sweeping sea views of the bay and gardens below. Lounge chairs let you 
													enjoy the views and take in the sun beside the pool and a dining area on the upper and lower balcony gives 
													you the perfect spot to dine as the sun goes down.<br/><br/>
													
													The two H2O units provide breath-taking views over the bay from your own private pool rooftop pool and lounge.<br/><br/>
													
													Located on the top floor of building A, these units are perfect for a romantic getaway or honeymoon.<br/>&nbsp;
												</div>
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/H2O-Suite-1.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/H20-Image-2-Large.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/H20-Image-2-Thumbnail.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/H20-Suite-3.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/h2o.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>
											
											<div id="serenity_h20_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family7');return false;" class="small-text"><span id="div_family7_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family7" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation7');return false;" class="small-text"><span id="div_cancellation7_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation7" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>
								
								
								<?php } ?>
								
								
								<!-- POOL RESISENCES -->
								<?php if($arryPrice[5][1] > 0){ ?>
								
									<div class="room">
										<div>
											<div class="col-md-5 room-thumb">
												<img src="img/rooms/booking/the-residences-thumb.jpg">
												
												<?php 
													$incentives=$rs[incentives][6];
													
													if(is_array($incentives) && sizeOf($incentives) > 0 ){
														foreach($incentives as $incentive){
															?>
																<div class="incentive-image">
																	<img src="./uploads/incentives/<?php echo $incentive['id'];?>/<?php echo $incentive['thumb_image'];?>" alt="<?php echo $incentive['description'];?>" onclick="$('#dialog_6').show();"/>
																</div>	
															<?php
														}
													}
												?>
												<!--prepare the dialog for incentives-->
												<?php if(is_array($incentives) && sizeOf($incentives) > 0 ){ ?>
												<div id="dialog_6" class="dialog">
													<div class="fright dialog-close" onclick="$(this).parent().hide();"><img src="img/close.png" /></div>
													<div class="horizontal-space"></div>
													<div class="dialog-content">
														<?php foreach($incentives as $incentive){ ?>
															<b><?php echo $incentive['title'];?></b><br/><br/>
															<?php echo $incentive['description'];?>	
															<br/><br/>
														<?php }?>
													</div>
												</div>
												<?php } ?>
												
												<center>
													<?php if($airport_transfer_checkin <= $airport_transfer_check_date){ ?>
														<?php if($nights > 4 && $nights < 7){ ?>
															<span class="message very-small-text">* Free 1 way Transfer</span><br/>
														<?php }else if($nights > 6){ ?>
															<span class="message very-small-text">* Free 2 way Transfer</span><br/>
														<?php }?>
													<?php } ?>
												
													<a href="" onclick="toggle('serenity_residences_conditions');return false;" class="small-text"><i class="fa fa-paperclip"></i> View Booking Conditions</a>
												</center>
											
											</div>
												
												
											<div class="col-md-7">
												
												<span class="room_title"><h4><?php  echo $rs[rooms][5][name] ?></h4></span>
												<p><?php  echo $rs[rooms][5][description] ?>...<br/><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adults and 1 Child"></i><i class="fa fa-user" title="Maximum Occupancy: 6 Adults or 5 Adults and 1 Child (Charges apply for extra bed)"></i>&nbsp;&nbsp;<i class="fa fa-coffee" title="Buffet Breakfast Included"></i>&nbsp;&nbsp;<i class="fa fa-eye" title="Full Sea View"></i>&nbsp;&nbsp;<i class="fa fa-wifi" title="FREE Wifi in Room & public areas"></i>&nbsp;&nbsp;<i class="fa fa-glass" title="FREE Welcome drink"></i><br/><a href="the_residences.php" onclick="return toggle('the_residences_toggle');" class="rooms-description-link"><i class="fa fa-arrow-right"></i> Room Description, Photos & 360 Tour</a></p>

												
												
												<div>
													<div class="fright small-text">
														<?php
															$discount=$rs[discount][6];
															if($discount > 0){
																//echo '&nbsp;&nbsp; <span class="message">Discount:</span> <span class="hightlight-text small-text">' . format_currency($discount) . '</span>';
															}
																
														?>
													</div>
												</div>
												<h6 class="hightlight_text left">Avg rate per night* <span class="very-small-text">(including breakfast)</span></h6>
                                                <h6 class="hightlight_rate"><?php if($discount > 0){?><span class="strikethrough small-text" data-origin-price="<?php echo  format_currency($arryPrice[5][0]);?>"><?php echo  format_currency($arryPrice[5][0]);?></span>&nbsp;&nbsp;<?php } ?> <span id="applicable_price_show_8" class="custombasetarget pricetag" data-origin-price="<?php echo format_currency($arryPrice[5][0] - ($discount/$nights));?>"><?php echo format_currency($arryPrice[5][0] - ($discount/$nights));?></span></h6>
												<form name="frm_residences" id="frm_residences" action="<?php echo WEBSITE_HTTPS_URL;?>contact-information.php" method="POST" onsubmit="javascript: return validate_guests_per_room('<?php echo $rooms_desc_array[6]['name']?>',<?php echo $rooms_desc_array[6]['guests_allowed']; ?>, <?php echo ($adults + $children);?>);">

													<input type="hidden" id="applicable_price_8" name="applicable_price_8" value="<?php echo round($arryPrice[5][0] - ($discount/$nights));?>"/>

													<input type="hidden" name="room_type" value="6">
													<input type="hidden" name="chk_in" value="<?php echo $chk_in;?>">
													<input type="hidden" name="chk_out" value="<?php echo $chk_out;?>">
													<input type="hidden" name="selAdults" value="<?php echo $adults;?>">
													<input type="hidden" name="price" value="<?php echo format_currency($arryPrice[5][0]  - ($discount/$nights) );?>">
													<input type="hidden" name="selChildren" value="<?php echo $children;?>">
													<input type="hidden" name="rooms" value="<?php echo $rooms; ?>"/>
													<input type="submit" class="btn btn-primary btn-sml" value="Book Now" />
												</form>	
											</div>

										</div>
										
										<div class="horizontal-space"></div>

										<div>
											<div id="the_residences_toggle" style="display: none">
												<div>
													
													<ul class="list list-skills icons list-unstyled">
														<li><i class="fa fa-inbox"></i> Beds: 2 King Beds</li>
														<li><i class="fa fa-users"></i> Maximum Occupancy: 6 Adults or 5 Adults and 1 Child</li>
														<li><i class="fa fa-user"></i> Extra Beds Allowed: 2 per room</li>			
													</ul>

													With a private pool and sundeck with direct access to the sea, a large living room and dining area, two bedrooms 
													on the second floor each with sea views, and a private rooftop lounge overlooking the water, nothing is missing.<br/><br/>
													
													The Pool residences offer the ultimate tropical lifestyle experience with a large glass frontage, which allows 
													you to enjoy the wonderful view from the large living room and from the fully equipped European Style Kitchen.<br/>&nbsp; 
												</div>
												<div class="horizontal-space"></div>
												<div>
													<a href="./img/gallery/searchrooms/Pool-Residence-1.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-1.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Pool-Residence-2-Large.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Pool-Residence-2-Thumbnail.jpg"/></a>&nbsp;
													<a href="./img/gallery/searchrooms/Pool-Residence-3.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-3.jpg"/></a>&nbsp;
													<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_br_rsdnce.htm?iframe=true&amp;width=950&amp;height=650" target="_blank"><img src="./img/gallery/searchrooms/360.jpg"/></a>
												</div>	
											</div>
											
											<div id="serenity_residences_conditions" style="display: none">
												<h4><?php echo LANG_BOOKING_CONDITIONS_TITLE; ?></h4>
												<div>
													<?php echo LANG_BOOKING_CONDITIONS; ?>
												</div>
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_family8');return false;" class="small-text"><span id="div_family8_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE; ?></a></h5></div>
												<div id="div_family8" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_FAMILY_POLICY; ?>						
													</div>
												</div>	
												<div class="horizontal-space"></div>
												<div><h5><a href="#" onclick="toggle('div_cancellation8');return false;" class="small-text"><span id="div_cancellation8_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE; ?></a></h5></div>
												<div id="div_cancellation8" style="padding-left:20px;display:none;">
													<div class="horizontal-space"></div>
													<div class="small-text">
														<?php echo LANG_CANCELLATION_POLICY;?>
													</div>
												</div>
												<div class="clear"></div>	
											</div>						
											<hr />
										</div>	
									</div>								
								
								<?php } ?>
								
								<div class="horizontal-space"></div>
								<center><em>* Above rates are subject to 10% service charge, applicable 7% government tax and 1% provincial tax.</em></center>	
								<div class="horizontal-space"></div>
								
							<?php }else{ ?>
								<?php echo $message;?>	
							<?php } ?>	
							
						</div>
						
						
                        <input type="hidden" name="best_price_thb" value="">
						<div class="col-md-4">
							<aside class="sidebar">
                                <div class="price-fighter-widget"
                                    data-pf-hotelkey="d81857e8a5220685b1aedcd797dcc8061e7c722a"
                                    data-pf-rooms="1"
                                    data-pf-direct-price="{direct-price}"
                                    data-pf-currency="<?php echo $_COOKIE['currency']!='' ? $_COOKIE['currency'] : 'THB'; ?>"
                                    data-pf-checkin="<?php echo date('Y-m-d', $startdate);?>"
                                    data-pf-checkout="<?php echo date('Y-m-d', $enddate);?>"
                                    data-pf-adults="<?php echo $adults;?>"
                                    data-pf-children="<?php echo $children;?>"
                                    data-pf-room-rate="Best Price"
                                    data-pf-total="false"
                                    data-pf-layout="rocket"
                                >
                                </div>
                                <!--data-pf-room-rate="{room-rate}"-->
								<form id="rooms_form" name="rooms_form" method="GET" action="search-rooms.php" onsubmit="return checkBooking(this);">
									<?php include('./includes/inc_fastbooking_sidebar.php');?>
								</form>	
							</aside>
                            <script>
                                function get_best_price(cb){
                                    var best_price = null;
                                    var input_price = $("input[name^='applicable_price_']");
                                    var len_input_price = input_price.length;
                                    if(len_input_price < 1){cb(best_price)}
                                    input_price.each(function(index, data){
                                        var count = index+1;
                                        var cr_price = parseFloat(data['value']);
                                        if(best_price){
                                            if(cr_price < best_price){
                                                best_price = cr_price;
                                            }
                                        }else{
                                            best_price = cr_price;
                                        }
                                        if(count==len_input_price){
                                            cb(best_price);
                                        }
                                    });
                                };
                                get_best_price(function(best_price){
                                    var widget = document.getElementsByClassName("price-fighter-widget")[0];
                                    widget.setAttribute("data-pf-direct-price", best_price);
                                    $("input[name='best_price_thb']").val(best_price);
                                });
                            </script>
						</div>						

					</div>

				</div>

			</div>

		<?php include("./includes/footer.php"); ?>

		</div>

		<!-- Vendor -->
        <!--<script src="vendor/jquery/jquery.js"></script>-->
		<script src="vendor/jquery.appear/jquery.appear.js"></script>
		<script src="vendor/jquery.easing/jquery.easing.js"></script>
		<script src="vendor/jquery-cookie/jquery-cookie.js"></script>
		<script src="vendor/bootstrap/bootstrap.js"></script>
		<script src="vendor/common/common.js"></script>
		<script src="vendor/jquery.validation/jquery.validation.js"></script>
		<script src="vendor/jquery.stellar/jquery.stellar.js"></script>
		<script src="vendor/jquery.easy-pie-chart/jquery.easy-pie-chart.js"></script>
		<script src="vendor/jquery.gmap/jquery.gmap.js"></script>
		<script src="vendor/isotope/jquery.isotope.js"></script>
		<script src="vendor/owlcarousel/owl.carousel.js"></script>
		<script src="vendor/jflickrfeed/jflickrfeed.js"></script>
		<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
		<script src="vendor/vide/vide.js"></script>

		<!-- Specific Page Vendor and Views -->
		<script src="vendor/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
		<script src="vendor/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
		<script src="vendor/circle-flip-slideshow/js/jquery.flipshow.js"></script>
		<script src="js/views/view.home.js"></script>
				
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

		<!-- Google Analytics: Change UA-XXXXX-X to be your site's ID. Go to http://www.google.com/analytics/ for more information.
		<script type="text/javascript">
		
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-12345678-1']);
			_gaq.push(['_trackPageview']);
		
			(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		
		</script>
		 -->
		<!-- website custom scripts-->
		<script src="js/ui/jquery-ui-1.7.2.custom.min.js"></script>	
		<script src="js/impromptu/2.8/jquery-impromptu.2.8.min.js"></script>	
		<script src="js/common.js"></script>
		<script src="js/internal_pages.js"></script>
		<script src="js/coupon_code.js"></script>		 
		
		<script src="js/prettyphoto/jquery.prettyPhoto.js"></script>	
		
		<script src="js/footer.js"></script>
		
		<script src="js/jquery.scc.min.js"></script>	
        <script type="text/javascript" src="js/bootstrap-notify.min.js"></script>
        <script type="text/javascript" src="js/notif.js"></script>
        <script type="text/javascript" src="js/save_search.js"></script>
        <script type="text/javascript" src="js/money.min.js"></script>
        <script type="text/javascript" src="js/money_custom.js"></script>
        <!--
		<script>
			jQuery(document).ready(function(){

				// Basic (no option)
				jQuery('.basicprice').currencyConverter({});  

				// Custom base and target currency
				jQuery('.custombasetarget').currencyConverter({
					baseCurrency: "THB",
					targets: ["USD", "AUD", "GBP","EUR","CNY"]
				}); 


			});
		</script>		
        -->
		
	</body>
</html>
