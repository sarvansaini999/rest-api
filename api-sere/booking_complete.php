<?php 
	include('./includes/init.php');
	include('languages/en/inc_policies_signature.php');
	include('languages/en/booking_complete.php');
	
	$booking_info=array();
	
	$bId = 5590;
	$_SESSION['hdbId']='';
	
	if($bId==''){
		header("Location: ".WEBSITE_HTTP_URL);
		exit(); //Stop running the script
	}
	
	
	if(isset($_SESSION['booking_info']) && $_SESSION['booking_info'] !=''){
		$booking_info=$_SESSION['booking_info'];
		
	}else{
		header("Location: ".WEBSITE_HTTP_URL);
		exit;			
	}	
	$q_str = '&room_type='.$booking_info[0]['room_type'].'&chk_in='.$booking_info[0]['start_date'].'&chk_out='.$booking_info[0]['end_date'].'&adults='.$adults.'&children='.$children;
	$api = 'http://localhost:8080/serenity/api/booking_complete?bid='.$bId."".$q_str;
	
	$cUrl = $api;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $cUrl);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$res = curl_exec($ch);
	curl_close($ch);
	
	$rs = json_decode($res, true);
	echo "<pre>";
	print_r($rs);
	echo "</pre>";
	
	$already_completed=false;
	if(isset($_COOKIE['booking']) && $_COOKIE['booking'] ==$bId){
		$message=LANG_BOOKING_ALREADY_CONFIRMED;
		$already_completed=true;
	}else{	
		$arryrooms = array('Serenity Suite', 
					   'Serenity Suite Seaview', 
					   'Grand Seaview Suite', 
					   'H20 Suite', 
					   'Penthouse Seaview  Suite', 
					   'Pool Residence',
					   'Serenity Room',
					   '3 Bed Penthouse Seaview Suite'
					   );
					   

		/*$strSQL = 'UPDATE tblbooking_transaction SET tblbooking_sts=0 WHERE tblbooking_id='.$bId;
		mysql_query($strSQL);
		
		
		$strSQLs = 'SELECT * FROM tblbooking_transaction WHERE tblbooking_id='.$bId;
		$query=mysql_query($strSQLs);*/
		
		$r=$rs[transaction];
		
		//$strDetail = 'SELECT * FROM tblbooking_transaction_detail WHERE tblbooking_id='.$bId;
		
		
		//$rquery=mysql_query($strDetail);
		
		
		$room_name='';
		$room_price_per_night='';
		
		
		$body = "<div style='font-family:Arial;font-size:12px;'><div style='width:700px;height:68px;padding 10px 0 10px 0;text-align:center;'><a href='http://www.serenityphuket.com' target='_blank'><img src='http://www.serenityphuket.com/images/image002.jpg' width='150' height='68' alt='Senenity Phuket' border='0'></a></div><br /><br />Dear ".$r[tblbooking_title].' '.$r[tblbooking_name].' '.$r[tblbooking_surname]."<br /><br />Thank you for choosing to stay at Serenity Resort & Residences. Please find below your confirmed reservation details as follows:<br /><br />";

		$body .="<table width='500px' border='0' cellspacing='0' cellpadding='0' style='font-family:Arial;font-size:12px;'>";
		//$body .="<tr><td width='150'><b>Check-In/Out: </b></td><td width='350'>".date('d/m/Y', strtotime($r[tblbooking_startdate]))." - ".date('d/m/Y', strtotime($r[tblbooking_enddate]))."</td></tr>";
		
		$body .="<tr><td colspan='2'>&nbsp;</td></tr>";
		$body .="</table>";
		
		$body .="<table width='800' border='0' cellspacing='0' cellpadding='0' style='font-family:Arial;font-size:12px;text-align:center;'>
		<tr style='background-color:#9b8a3b;color:#ffffff;'>
			<td width='30px'><strong>No.</strong></td>
			<td width='250px'><strong>Room Type</strong></td>
			<td width='50px'><strong>Rooms</strong></td>
			<td width='130px'><strong>Bedding Type</strong></td>
			<td width='50px'><strong>Adult</strong></td>
			<td width='50px'><strong>Child</strong></td>";
		
		$body .="<td><strong>Price per Room Per Night</strong></td>";

		$body .="
		</tr>
		<tr style='background-color:#ece9da;'><td colspan='7'>&nbsp;</td></tr>";
		
		$i=1;

		$extra_beds=0;
		
		$room_ids='';
		$room_names='';
		
		foreach($rs[transaction_detail] as $rdetail){
			//$sqlroom = 'SELECT * FROM tblbooking_transaction_rooms t WHERE tblbooking_id = ' . $bId . ' AND tblroom_id = ' . $rdetail[tblroom_id] . ' ORDER BY tblbooking_id ASC, tblroom_id ASC, tblroom_order ASC';
			//$queryroom = mysql_query($sqlroom);
			foreach($rs[transaction_room] as $result){
				
				/*extra beds*/
				$extra_beds=calculate_extra_beds($rdetail['tblroom_id'],$result['tblroom_adult'],$rdetail['tblbooking_room']);	
				
				$room_type = $rdetail['tblroom_id'];
				$adults = $result['tblroom_adult'];
				$children = $result['tblroom_child'];
				$rooms = $rdetail['tblbooking_room'];
				
				$room_name=$rdetail['tblbooking_roomtype'];
				$room_price_per_night=$rdetail['tblbooking_rate'];
				
				
				$body .= "<tr style='background-color:#ece9da;'>
					<td>". $i.".</td>
					<td>";

				foreach($booking_info as $val){
				
						if($room_ids ==''){
							$room_ids=$val['room_type'];
						}else{
							$room_ids .= ',' . $val['room_type'];
						}
						
						if($room_names ==''){
							$room_names=$arryrooms[$val['room_type']-1];
						}else{
							$room_names .=',' . $arryrooms[$val['room_type']-1];
						}
				
						$checkin=explode('/',$val['start_date']);
						$checkout=explode('/',$val['end_date']);
						
						$temp_startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
						$temp_enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
						$body .= $arryrooms[$val['room_type']-1] . '<br/>';
						$body .= '[ ' . date('d/m/Y',$temp_startdate) . ' - ' .date('d/m/Y',$temp_enddate) . ' ]<br />';	
				}				
				
				$body .= "</td>
					<td>". $rdetail['tblbooking_room']."</td>
					<td>". $rdetail['tblbooking_bedding']."</td>
					<td>". $result['tblroom_adult']."</td>
					<td>". $result['tblroom_child']."</td>
					<td>". number_format($rdetail['tblbooking_rate'])."</td>
					</tr>";
				$i++;
			}

			$summary = $rdetail['tblbooking_summary'];
			
			$name = $r[tblbooking_title].' '.$r[tblbooking_name].' '.$r[tblbooking_surname];
			
		}
		
		$body .= "
			<tr style='background-color:#ece9da;'><td colspan='7'>&nbsp;</td></tr>
			<tr style='background-color:#ece9da;'>
				<td colspan='3'>&nbsp;</td>
				<td colspan='3'><strong>Grand Total ( including taxes ) :</strong></td>
				<td><strong>".number_format($summary) . "</strong></td>
			</tr>";
		$body .= "<tr><td colspan='7' style='background-color:#ece9da;'>&nbsp;</td></tr>";
		$body .= "</table>";
		
		
		$body .= "<p><strong>*** Rates above are inclusive of 10% service charge & 7% VAT & 1 % provincial tax.</strong></p><p>*** Airport transfer in limousine (max. 3 guests) or minivan (max. 6 guests) at Baht 1400++ for limousine and Baht 1600++ for mini van is available. Please let us know  your flight details should you wish to book airport transfer. </p>";
		$body .= "<p><strong>NOTE:</strong> The credit card used to make your reservation must be presented by the credit card owner for verification upon check-in.</p>";	
		
		
		$body .="
		<table width='100%' border='0' cellpadding='0' cellspacing='0' style='font-family:Arial;font-size:12px;'>
			<tr><td width='23%'><strong>Booking ID:</strong></td><td width='77%'>".$bId."</td></tr>
			<tr><td width='23%'><strong>Title:</strong></td><td width='77%'>".$r['tblbooking_title']."</td></tr>
			<tr><td><strong>Name: </strong></td><td>".$r['tblbooking_name']." ".$r['tblbooking_surname']."</td></tr>
			<tr><td><strong>Email:</strong></td><td>".$r['tblbooking_email']."</td></tr>
			<tr><td><strong>Telephone:</strong></td><td>".$r['tblbooking_tel']."</td></tr>
			<tr><td colspan='2'>&nbsp;</td></tr>
			<tr><td colspan='2'>&nbsp;</td></tr><tr><td><strong>Promotion Code:</strong></td><td>" . $r['tblbooking_coupon_code']."</td></tr>
			<tr><td colspan='2'>&nbsp;</td></tr>";
			/*
			<tr><td><strong>Arrival date:</strong></td><td>".$s_date."</td></tr>
			<tr><td><strong>Departure date:</strong></td><td>".$e_date."</td></tr>";
			*/
		
		
		$special_incentives='';
		if(sizeOf($booking_info) == 1){
			$incentives=$rs[incentives];
			if(is_array($incentives) && sizeOf($incentives) > 0 ){
				foreach($incentives as $incentive){
					if($special_incentives ==''){
						$special_incentives=$incentive['title'];
					}else{
						$special_incentives .= ', ' . $incentive['title'];
					}
				}
			}		
			
			if('' != $special_incentives){
				$body .="<tr><td><strong>Included:</strong></td><td>".$special_incentives."</td></tr>";
			}
		}

		
		$airport_transfer_str='';
		if((isset($_SESSION['airport_transfer_price']) && $_SESSION['airport_transfer_price'] > 0 ) || (isset($_SESSION['airport_transfer_included']) && $_SESSION['airport_transfer_included'] > 0)){
			$airport_transfer_str='Airport Transfer';
			
			$body .="<tr><td colspan='2'>&nbsp;</td></tr>";
			$body .="<tr><td><strong>Airport Transfer:</strong></td><td>" . $_SESSION['transfer_vehicle'] ."</td></tr>";
			$body .="<tr><td><strong>Arrival Flight No:</strong></td><td>" . $r[airport_arrival_flight_no]."</td></tr>";
			$body .="<tr><td><strong>Arrival Time:</strong></td><td>" . $r[airport_arrival_time]."</td></tr>";
			$body .="<tr><td><strong>Departure Flight No:</strong></td><td>" . $r[airport_departure_flight_no]."</td></tr>";
			$body .="<tr><td><strong>Departure Time:</strong></td><td>" . $r[airport_departure_time]."</td></tr>";
			$body .="<tr><td colspan='2'>&nbsp;</td></tr>";
		}		
		
		if($extra_beds > 0){
			$body .="<tr><td><strong>Extra Beds:</strong></td><td>".$extra_beds."</td></tr>";
		}
		
		if(isset($_SESSION['upsell'])){
			foreach($_SESSION['upsell'] as $key=>$val){
				if($upsel_string ==''){
					$upsel_string=$val[0];
				}else{
					$upsel_string .= ', ' . $val[0];
				}
			}
			$body .="<tr><td><strong>Enhancements:</strong></td><td>".$upsel_string."</td></tr>";	
		}		
		
		$body .="
			<tr><td colspan='2' align='center'>&nbsp;</td></tr></table>";
		
		//Credit Card
		$body .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' style='font-family:Arial;font-size:12px;'>
		<tr><td colspan='2'><strong>Credit Card</strong></td></tr>
		
		<tr><td width='23%'><strong>Card Type: </strong></td><td width='77%'>".$r['tblbooking_cardtype']."</td></tr>
		<tr><td><strong>Card Holder: </strong></td><td width='77%'>".$r['tblbooking_creditcard_holder']."</td></tr>
		<tr><td><strong>Credit Card:</strong></td><td>". format_cc_number($r['tblbooking_creditcard'])."</td></tr>
		<tr><td><strong>Expire Date:</strong></td><td>".$r['tblbooking_expiredate']."&nbsp;(month/year)</td></tr>
		<tr><td colspan='2' align='center'>&nbsp;</td></tr></table>";
		
			
		$body .= "<h3 style='font-family:Arial;font-size:16px;font-weight:bold;'>" . LANG_TERMS_TITLE ."</h3>";
		$body .= LANG_TERMS ;
		$body .= "<h3 style='font-family:Arial;font-size:16px;font-weight:bold;'>" . LANG_FAMILY_POLICY_TITLE ."</h3>";
		$body .= LANG_FAMILY_POLICY ;
		$body .= "<h3 style='font-family:Arial;font-size:16px;font-weight:bold;'>" . LANG_CANCELLATION_POLICY_TITLE ."</h3>";
		$body .= LANG_CANCELLATION_POLICY . '<br/><br/>';
		
		
		$body .= LANG_THANKS_SIGNATURE;

		$body .= "</div>";

		//echo $body;
		//die();
		

		$subject = "Voucher: Serenity Resort & Residences";
		send_email('booking',$subject,$body,$r['tblbooking_email']);				
		

		/*set cookie*/
		setcookie("booking", $bId, time()+60*60*24*30, "/", ".serenityphuket.com");
		$message=LANG_THANK_YOU_FOR_BOOKING;		
	}
		
	/*
	* calculate nights
	*/
	$nights=0;
	foreach($booking_info as $val){
		$nights += calculate_nights_stay($val['start_date'],$val['end_date']);
	}

	
	/*see if extra room or extra bed need to added*/
	$extra_beds_price=0;
	foreach($booking_info as $val){
		$room_type=$val['room_type'];
		$extra_beds_price_temp=calculate_extra_beds_price($room_type,$adults,$rooms);
		if($extra_beds_price_temp > 0 ){
			$temp_nights= calculate_nights_stay($val['start_date'],$val['end_date']);
			$extra_beds_price += $extra_beds_price_temp * $temp_nights;
		}
	}	
	

	/*calculate total price*/
	$totalprice = $rs[price];
	$gross_before_tax=0;
	
	
	/*discount*/
	$discount=0;
	foreach($booking_info as $val){
		$discount +=room_total_discount($val['room_type'],$val['start_date'],$val['end_date']);
	}
	
	/*
	* upsell
	*/		
	$upsell_price=0;	
	if(isset($_SESSION['upsell']) && '' != $_SESSION['upsell'] && isset($_SESSION['upsell_price']) && ''  != $_SESSION['upsell_price']){
		$upsell_price=$_SESSION['upsell_price'];
	}
	
	/*
	* Airport transfer
	*/
	$transfer_price=0;
	if(isset($_SESSION['airport_transfer_price']) && $_SESSION['airport_transfer_price'] > 0){
		$transfer_price=$_SESSION['airport_transfer_price'];
	}
		
	
	$rooms_price_total=$totalprice * $rooms - $discount;
	
	$average_price_per_night_without_tax=round($rooms_price_total/$nights);
	
	/*
	* Add Extra Bed price
	*/
	if($extra_beds_price > 0){
		$average_price_per_night_without_tax=$average_price_per_night_without_tax + round($extra_beds_price/$nights);
	}

	
	/*
	* coupon code
	*/
	if($_SESSION['coupon_code'] !=""){	
		$query_text="SELECT discount FROM coupon_code WHERE coupon_code='".$_SESSION['coupon_code']."' AND status='1'";
		$coupon_result=mysql_query($query_text);
		if(mysql_num_rows($coupon_result) > 0){	
			$coupon_row=mysql_fetch_assoc($coupon_result); 
			$average_price_per_night_without_tax=$average_price_per_night_without_tax - round(($coupon_row['discount'] / 100)* $average_price_per_night_without_tax);
		}
	}
	
	$tax_charges=round(tax_room_price($average_price_per_night_without_tax) - $average_price_per_night_without_tax);		
	
	$average_price_per_night=$average_price_per_night_without_tax + $tax_charges;//average price per night [upsell prices are not included here]
	$gross_after_tax=round(($average_price_per_night * $nights) +  tax_extra_services($upsell_price) + tax_extra_services($transfer_price),0);
		
		
	
	$_SESSION['bid']=$bId;
	$_SESSION['name']=$name;
	$_SESSION['rooms_count']=$rooms;
	
	$popurl = WEBSITE_HTTPS_URL."booking_complete_pop.php?bid=" . $bId;


?>
<!DOCTYPE html>
<html>
	<head>

		<!-- Basic -->
		<meta charset="utf-8">
		<title>Phuket Hotels| Luxury Hotels in Phuket | Serenity Resort &amp; Residences</title>
		<meta name="keywords" content="phuket hotel, phuket resort, villa, beachfront resort, beachfront villa, phuket Residences, phuket villa, beachfront property thailand, Luxury Hotels, 5 star hotel in phuket" />
		<meta name="description" content="Serenity Resort and Residences, Phuket. Luxury 5 star Hotel, Spa, Well Being and Beach, Phuket Hotels" />
		
		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- Web Fonts  -->
		<link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/bootstrap.css">
		<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css">
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
		
		
		
		<!-- booking system -->
		<link type="text/css" href="css/booking.css" rel="stylesheet" />
		<link type="text/css" href="css/ui/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
		<link href="js/impromptu/2.8/impromptu.css" rel="stylesheet" type="text/css" />			
		
		
		<?php if(!$already_completed && $track_bookings=='1'){?>

			<?php include("./includes/common.googleanalytics.php"); ?>

			<!-- Google Code for Booking Conversion Page -->
			<script type="text/javascript">
				/* <![CDATA[ */
				var google_conversion_id = 1011401642;
				var google_conversion_language = "en";
				var google_conversion_format = "3";
				var google_conversion_color = "ffffff";
				var google_conversion_label = "jSmFCIa7tgIQqoej4gM";
				var google_conversion_value = <?php echo $summary;?>;
				/* ]]> */
			</script>
			<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>
			<noscript>
				<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1011401642/?label=jSmFCIa7tgIQqoej4gM&amp;guid=ON&amp;script=0"/>
				</div>
			</noscript>		
			<?php 
				$total_before_tax=round($rooms_price_total + $extra_beds_price + $upsell_price + $transfer_price,0);
				$tax=round((tax_room_price($rooms_price_total) + tax_extra_services($extra_beds_price) + tax_extra_services($upsell_price) + tax_extra_services($transfer_price)) - $total_before_tax,0);
			?>
			
			
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push(['_setAccount', 'UA-17783371-1']);
			  _gaq.push(['_trackPageview']);


			  _gaq.push(['_addTrans',
			  '<?php echo $bId; ?>',           // order ID - required
			  'Serenity',      // affiliation or store name
			  '<?php echo $total_before_tax; ?>',          // total - required
			  '<?php echo $tax;?>',                  // tax
			  '',                  // shipping
			  '',                  // city
			  '',                  // state or province
			  ''           // country
			]);


		   // add item might be called for every item in the shopping cart
		   // where your ecommerce engine loops through each item in the cart and
		   // prints out _addItem for each
		  _gaq.push(['_addItem',
			'<?php echo $bId; ?>',           // order ID - required
			'<?php echo $room_ids; ?>',           // SKU/code - required
			'<?php echo $room_names; ?>',        // product name
			'<?php echo $upsel_string;if('' != $airport_transfer_str){if('' != $upsel_string){ echo ',' . $airport_transfer_str;}else{ echo $airport_transfer_str;}} ?>',   // category or variation
			'<?php echo round($room_price_per_night);?>',          // unit price - required
			'<?php echo ($rooms * $nights)?>'               // quantity - required
		  ]);

			_gaq.push(['_trackTrans']);
			
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
			</script>
			
			<script type="text/javascript" src="https://secure-hotel-tracker.com/tics/log.php?act=conversion&ref=<?php echo $bId; ?>&amount=<?php echo $total_before_tax; ?>&currency=THB&idbe=3&idwihp=168320"></script>	
			
		<?php 
		}
		?>	
		
		<script language="javascript">
			function openpopup() {
				window.open('<?php echo $popurl;?>','PrintPage','toolbar=No,status=yes,scrollbars=yes,width=1000,height=600');
			}
		</script>	
		
		
	</head>
	<body>

		<div class="body">
		
			<?php require_once('includes/inc_nav.php');?>	

			<div role="main" class="main">
				<div class="slider-container">
					<div class="slider" id="revolutionSlider" data-plugin-revolution-slider data-plugin-options='{"startheight": 500}'>
						<ul>
							<li data-transition="fade" data-slotamount="13" data-masterspeed="300" >
								<img src="img/slides/investment-1.jpg" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat">
							</li>
							

						</ul>
					</div>
				</div>

				<div class="home-intro light">
					<div class="container">
						<div class="col-md-12">	
							<h3>Thank you: <span><?php echo $name;?></span></h3>
						</div>	
					</div>
				</div>

				<div class="container">

					<div class="row">
						
						<div class="col-md-8">
							<p> 
								Your confirmation code is: <?php echo $bId;?><br />Thank you for choosing	Serenity Resort & Residences, your reservation has been received and you will shortly receive an email confirmation.
								<br /><br />
								Thank you again and we look forward to welcoming you to Serenity soon!
								<br /><br />
								Kind regards,
								<br /><br />
								Serenity Team.
							</p>


							<div class="room_container">
								<div>
									<div class="team_thumb fleft center"><img width="111px" height="96px" border="0" src="./img/team/admin_support.png"></div>
									<div class="fright rooms_content">
										<p>If you have any questions regarding your booking please contact our reservations department at <a href="mailto:rsvnm@serenityphuket.com">rsvnm@serenityphuket.com</a>.com or call us on +66 (0) 76 371 900 Ext. 701</p>
									</div>
									<div class="clear"></div>
								</div>
								<br /><br />
							</div>
							<br class="clear" />
							<div class="horizontal-space"></div>
							<div class="horizontal-space"></div>
							<div class="horizontal-space"></div>	
							<!-- policies starts-->
							<div>
								<h4 class="small-text"><?php echo LANG_TERMS_TITLE;?></h4>
								<div class="horizontal-space"></div>
								<div><h5><a href="#" onClick="toggle('div_family');return false;" class="small-text"><span id="div_family_show_symbol">+</span> <?php echo LANG_FAMILY_POLICY_TITLE;?></a></h5></div>
								<div id="div_family" style="padding-left:20px;display:none;">
									<div class="horizontal-space"></div>
									<div class="small-text">
										<?php echo LANG_FAMILY_POLICY;?>	
									</div>
								</div>	
								<div class="horizontal-space"></div>
								<div><h5><a href="#" onClick="toggle('div_cancellation');return false;" class="small-text"><span id="div_cancellation_show_symbol">+</span> <?php echo LANG_CANCELLATION_POLICY_TITLE;?></a></h5></div>
								<div id="div_cancellation" style="padding-left:20px;display:none;">
									<div class="horizontal-space"></div>
									<div class="small-text">
										<?php echo LANG_CANCELLATION_POLICY;?>
									</div>
								</div>		
							</div>	
							<!--policies end-->
							<div class="horizontal-space"></div>
							<p>
								<a href="privacy_policy.php" target="_blank" class="small-text">Privacy Policy</a>
							</p>
							<p>
								<a href="faq.php" target="_blank" class="small-text">Frequently Asked Questions</a>
							</p>				
							
							
						</div>
						
						
						<div class="col-md-4">
							<aside class="sidebar">
								<h1 class="sidebar-title"><img border="0" src="./img/icon_print.png" onClick="javascript: openpopup();" style="cursor:pointer;" /></h1>
								<div class="horizontal-space"></div>
								<?php include('./includes/inc_booking_details.php');?>	
							</aside>
						</div>


					</div>

				</div>

			</div>

		<?php include("./includes/footer.php"); ?>

		</div>

		<!-- Vendor -->
		<script src="vendor/jquery/jquery.js"></script>
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
		<script src="./js/ui/jquery-ui-1.7.2.custom.min.js"></script>	
		<script src="js/impromptu/2.8/jquery-impromptu.2.8.min.js"></script>	
		<script src="js/common.js"></script>
		<script src="js/internal_pages.js"></script>
		<script src="js/coupon_code.js"></script>		 
		
		<script src="js/internal-pages-booking.js"></script>
		<script src="js/checkout.js"></script>		
		
		<script src="js/footer.js"></script>
        <script async src="https://api.triptease.io/identity-service/confirm?hotelkey=d81857e8a5220685b1aedcd797dcc8061e7c722a&bookingValue=<?php echo $rooms_price_total; ?>&bookingCurrency=THB&bookingReference=<?php echo $bId; ?>"></script>
		
	</body>
</html>
