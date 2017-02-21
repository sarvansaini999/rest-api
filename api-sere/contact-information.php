<?php 
	include('./includes/init.php');
	include('languages/en/inc_policies_signature.php');
	$arryrooms = array('Serenity Suite', 
					   'Serenity Suite Seaview', 
					   'Grand Seaview Suite', 
					   'H20 Suite', 
					   'Penthouse Seaview Suite', 
					   'Pool Residence',
					   'Serenity Room',
					   '3 Bed Penthouse Seaview Suite'
					   );
	
	$message='';
	$booking_error='';

	$booking_info=array();
	/*
	* very important part
	* dates/rooms can come from two sources
	* normal single room and chk in/chk out dates
	* multiple rooms and related chkin chk out dates
	*/
	
	if(isset($_POST['chk_in']) && valid_date($_POST['chk_in']) && isset($_POST['chk_out']) && valid_date($_POST['chk_out']) && isset($_POST['room_type']) &&  $_POST['room_type'] !=''){
		//normal booking
		$chk_in = get_value('chk_in','POST');
		$chk_out = get_value('chk_out','POST');
		$room_type = get_value('room_type','POST');
		$price = get_value('price','POST');
		
		$booking_info[]=array('room_type'=>$room_type,'start_date'=>$chk_in,'end_date'=>$chk_out);
		
	}else if(isset($_POST['dates']) && $_POST['dates'] !=''){
		//multiple rooms/multiple days
		foreach($_POST['dates'] as $val){
			foreach($val as $data){
				$data_array=explode('_',$data);
				if(sizeOf($data_array) ==2){
					$data_room_type=$data_array[0];
					
					$data_temp_date=$data_array[1];
					$data_temp_date_array=explode('-',$data_temp_date);

					$data_chk_in = mktime(0, 0, 0, $data_temp_date_array[1], $data_temp_date_array[2], $data_temp_date_array[0]);
					$data_chk_in_date=date('d/m/Y',$data_chk_in);
					$data_chk_out = mktime(0, 0, 0, $data_temp_date_array[1], $data_temp_date_array[2] +1, $data_temp_date_array[0]);
					$data_chk_out_date=date('d/m/Y',$data_chk_out);
	
					$booking_info[]=array('room_type'=>$data_room_type,'start_date'=>$data_chk_in_date,'end_date'=>$data_chk_out_date);
				}
			}
		}
	}
	
	
	/*
	* set get dates and room type
	*/
	if(isset($_POST['frmaction']) && $_POST['frmaction']=="CHECKBOOKINGINFO" ){
		//get values from session
		if(isset($_SESSION['booking_info']) && $_SESSION['booking_info'] !=''){
			$booking_info=$_SESSION['booking_info'];
		}else{
			header("Location: ".WEBSITE_HTTP_URL);
			exit;			
		}	
	}else{
		//set session
		if(sizeOf($booking_info) > 0){
			$_SESSION['booking_info'] =$booking_info;
		}else{
			header("Location: ".WEBSITE_HTTP_URL);
			exit;			
		}
		
	}
	
	
	/*
	* check duplicates ie same date with multiple rooms
	*/
	$duplicates_check_array=array();
	foreach($booking_info as $val){
		$duplicates_check_array[]=$val['start_date'];
	}	
	if(count(array_unique($duplicates_check_array)) < count($duplicates_check_array)){
		header("Location: ".WEBSITE_HTTP_URL);
		exit;
	}
	
	
	/*
	* get rooms, adults and children from session
	*/
	$rooms = 1;//get_value('rooms','POST');
	
	if(isset($_SESSION['selAdults']) && $_SESSION['selAdults'] > 0 && isset($_SESSION['selChildren'])){
		$adults = get_value('selAdults','SESSION');
		$children = get_value('selChildren','SESSION');	
	}else{
		$adults = get_value('selAdults','POST');
		$children = get_value('selChildren','POST');
        if($adults==''){
            header("Location:index.php");
            exit;	
        }
	}

	/*
	* check if the number of guests is more than a rooms allows for
	
	
	foreach($booking_info as $val){
		$room_type=$val['room_type'];
		
		$query_text='SELECT name, adults_included_in_rate,number_of_extra_beds FROM rooms WHERE id=' . $room_type;
		$result_room=mysql_query($query_text);
		
		if(mysql_num_rows($result_room) == 1){
			$row_room=mysql_fetch_assoc($result_room);
			$temp_room_name=$row_room['name'];
			$temp_allowed_extra_beds=$row_room['number_of_extra_beds'];
			$temp_adults_included=$row_room['adults_included_in_rate'];
			
			if(($adults + $children) > ($temp_allowed_extra_beds + $temp_adults_included)){
				$booking_error = 'The <b>' . $temp_room_name .'</b> accomodates a maximum of <b>' . ($temp_allowed_extra_beds + $temp_adults_included) . '</b> people per room, excluding children under 6. To complete this booking, please reduce the number of guests for this reservation.<br/><br/>To go back to home page <a href="index.php">click here</a><br/><br/>Or email us at <a href="mailto:rsvnm@serenityphuket.com">rsvnm@serenityphuket.com</a>' . '<br/>';
				break;		
			}
		}else{
			$booking_error='An error has occured. Please contact reservation depratment at <a href="mailto:rsvnm@serenityphuket.com">rsvnm@serenityphuket.com</a>.' . '<br/>';
		}
		
	}		
*/

	
	/* get other variables */
	$cbotitle = get_value('cbotitle','POST');
	$txtname = get_value('txtname','POST');
	$txtsurname = get_value('txtsurname','POST');
	$txtemail = get_value('txtemail','POST');
	$txttel = get_value('txttel','POST');
	$txttelcountry = get_value('txttelcountry','POST');
	$txtcardholder = get_value('txtcardholder','POST');	
	$cbocardtype = get_value('cbocardtype','POST');
	$txtcreditcard = get_value('txtcreditcard','POST');
	$cboexpmonth = get_value('cboexpmonth','POST');
	$cboexpyear = get_value('cboexpyear','POST');
	$txtcouponcode = get_value('txtcouponcode','POST');
	$post_cp = get_value('txtcouponcode','POST');
	
	$airport_transfer_included=get_value('airport_transfer_included','POST');
	$no_transfer_info=get_value('no_transfer_info','POST');
	
	$txtArrivalFlight=get_value('txtArrivalFlight','POST');
	$txtArrivalTime=get_value('txtArrivalTime','POST');
	$txtDepartureFlight=get_value('txtDepartureFlight','POST');
	$txtDepartureTime=get_value('txtDepartureTime','POST');
	
	
	$txtaddr = get_value('txtaddr','POST');
	$txtzipcode = get_value('txtzipcode','POST');
	$txtstate = get_value('txtstate','POST');
	$cbocountry = get_value('cbocountry','POST');
	$txtarrival = get_value('txtarrival','POST');
	
	$package = get_value('package','POST');
	$avg_price_per_night = get_value('booking_rate','POST');
	$gross_with_tax = get_value('booking_summary','POST');
	
	
	
	
	/*
	* calculate nights
	*/
	$nights=0;
	foreach($booking_info as $val){
		$nights += calculate_nights_stay($val['start_date'],$val['end_date']);
	}	
	
	
	/*
	* check if booking in peak season and meets minimum nights criteria
	*/
	
	$booking_in_peak=false;
	foreach($booking_info as $val){
		$checkin_temp = explode('/',$val['start_date']);
		$checkout_temp=explode('/',$val['end_date']);
		
		$startdate_temp=mktime(0, 0, 0, $checkin_temp[1], $checkin_temp[0], $checkin_temp[2]);
		$enddate_temp=mktime(0, 0, 0, $checkout_temp[1], $checkout_temp[0], $checkout_temp[2]);
		
		if(date_between($startdate_temp) || date_between($enddate_temp)){
			$booking_in_peak=true;
		}	
		
	}
	
	if($booking_in_peak && $nights < 5){
		$booking_error += 'Minimum of 5 nights stay is required between Dec 27, 2016 and Jan 9, 2017' . '<br/>';
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
	
	//$price = 0;
	$totalprice = $price;
	$gross_before_tax=0;
	
	$abf_bonus_nights=0;		
	
	$totalprice = str_replace("THB","",$totalprice);
	$totalp = str_replace(",","",$totalprice);
	foreach($booking_info as $val){
	
		$temp_nights=calculate_nights_stay($val['start_date'],$val['end_date']);
		$room_type=$val['room_type'];
		$checkin=explode('/',$val['start_date']);
		$startdate=mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
		$i = 0;
		while ($i < $temp_nights) {
			$price += $totalp;
			$totalprice += $totalp;

			$i++;
		}
	}
	unset($_SESSION['total_price']);
	unset($_SESSION['price']);
	$_SESSION['total_price']=$totalprice;
	$_SESSION['price']=$price;
	$totalprice = $_SESSION['total_price'];
	$price = $_SESSION['price'];
	
	
	/*discount*/
	$discount=0;

	foreach($booking_info as $val){
		$discount +=room_total_discount($val['room_type'],$val['start_date'],$val['end_date']);
	}
	
	
	
	$price=round(($price-$discount)/$nights,0);

	
	
	/*upsell*/
	$upsell_price=0;

	$_SESSION['upsell']='';
	$_SESSION['upsell_price']='';

	unset($_SESSION['upsell']);
	unset($_SESSION['upsell_price']);

	if(isset($_POST['upsell'])){
		foreach($_POST['upsell'] as $key=>$val){
			$txt_upsell="SELECT name,price,per_adult FROM products WHERE id='".$key."'";
			$sql_upsell=mysql_query($txt_upsell);
			$result_upsell=mysql_fetch_assoc($sql_upsell);
			if($result_upsell['per_adult'] == 'T'){
				$upsell_price= ($result_upsell['price'] * $adults ) + $upsell_price;
			}else{
				$upsell_price= $result_upsell['price'] + $upsell_price;
			}

			$_SESSION['upsell_price']=$upsell_price;

			$_SESSION['upsell'][]=array($result_upsell['name']);	
			
		}
	}
	
	/*
	* Airport transfer
	*/
	$transfer_price=0;
	
	$_SESSION['airport_transfer_price']='';
	unset($_SESSION['airport_transfer_price']);
	$_SESSION['airport_transfer_included']='0';
	unset($_SESSION['airport_transfer_included']);
	
	
	if($airport_transfer_included == '0' && isset($_POST['airport_transfer_price']) && $_POST['airport_transfer_price'] > 0){
		if(isset($_POST['airport_transfer']) && $_POST['airport_transfer'] > 0){
			$transfer_price=$_POST['airport_transfer_price'];
		}else if(isset($_POST['chkOneWayOnly']) && $_POST['chkOneWayOnly'] == '1'){
			$transfer_price=$_POST['airport_transfer_price'] / 2;
		}
		
		$_SESSION['airport_transfer_price']=$transfer_price;
		
	}else if($airport_transfer_included > 0){
		$_SESSION['airport_transfer_included']=$airport_transfer_included;
	}
	
	
		$totalprice;
		$rooms_price_total = $totalprice * $rooms - $discount;
	

	$average_price_per_night_without_tax=round($rooms_price_total/$nights);
	
	/*
	* Add Extra Bed price
	*/
	if($extra_beds_price > 0){
		$average_price_per_night_without_tax=$average_price_per_night_without_tax + round($extra_beds_price/$nights);
	}
	
	/* see if we need to apply coupon code*/
	$_SESSION['coupon_code']='';
	unset($_SESSION['coupon_code']);
	
	if($txtcouponcode !=""){	
		$query_text="SELECT discount FROM coupon_code WHERE coupon_code='".$txtcouponcode."' AND status='1'";
		$coupon_result=mysql_query($query_text);
		if(mysql_num_rows($coupon_result) > 0){
			/*
			* set coupon code in session
			*/
			$_SESSION['coupon_code']=$txtcouponcode;
			
			$coupon_row=mysql_fetch_assoc($coupon_result); 
			$average_price_per_night_without_tax=$average_price_per_night_without_tax - round(($coupon_row['discount'] / 100)* $average_price_per_night_without_tax);
		}
	}
	

	
	$tax_charges=round(tax_room_price($average_price_per_night_without_tax) - $average_price_per_night_without_tax);

	$average_price_per_night=$average_price_per_night_without_tax + $tax_charges;//average price per night [upsell prices are not included here]
	
	$gross_after_tax=round(($average_price_per_night * $nights) +  tax_extra_services($upsell_price) + tax_extra_services($transfer_price),0) ;
	
	
	
	//check available
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);
	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
	$backout_date = mktime(0, 0, 0, $checkout[1], $checkout[0]-1, $checkout[2]);
    $introom = $room_type;
    //$strPrice = 'SELECT tblroomtype_id, MIN(tblallotment_room) AS rooms, CEILING(AVG(tblallotment_price)) as tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date BETWEEN \''.date('Y/m/d', $startdate).'\' AND \'' . date('Y/m/d', $backout_date) .'\' AND tblroomtype_id=' . $introom . ' GROUP BY tblroomtype_id ORDER BY tblroomtype_id ASC ';
    //$query = mysql_query($strPrice);
    //$result = mysql_fetch_array($query);
	$chkAvail = array(
					'startdate' => $startdate,
					'enddate' => $backout_date,
					'room_type' =>$introom
				);
	
	/*
	* to database
	*/
	$booked = false;
	$strSQL = array();
	$strDetail = array();
	$strRooms = array();
	$acArr = array(
				 'chkin' => $chk_in
				,'chkout' 	=>$chk_out
				,'adults' 	=>$adults
				,'children' =>$children
			);
	
	$post = array('info'=>$booking_info, 'ac'=>$acArr, 'chkavil'=>$chkAvail);
	
	if(isset($_POST['frmaction']) && $_POST['frmaction']=="CHECKBOOKINGINFO" ){
		if($txtname!='' && $txtsurname!='' && $txtemail!='' && $txttel!='' && $txtcardholder !='' && $cbocardtype !='' && $txtcreditcard !='' && $cboexpmonth !='' && $cboexpyear !=''){
	
			$checkin=explode('/',$booking_info [0]['start_date']);
			$checkout=explode('/',$booking_info [0]['end_date']);
			
			$temp_startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
			$temp_enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
	
			
			$strSQL['tblbooking_title'] = $cbotitle;
			$strSQL['tblbooking_name'] = $txtname;
			$strSQL['tblbooking_surname'] = $txtsurname;
			$strSQL['tblbooking_addr'] = $txtaddr;
			$strSQL['tblbooking_zipcode'] = $txtzipcode;
			$strSQL['tblbooking_state'] = $txtstate;
			$strSQL['tblbooking_country'] = $cbocountry;
			$strSQL['tblbooking_email'] = $txtemail;
			$strSQL['tblbooking_tel_country'] = $txttelcountry;
			$strSQL['tblbooking_tel'] = $txttel;
			$strSQL['tblbooking_arrival'] = $txtarrival;
			$strSQL['tblbooking_startdate'] = $temp_startdate;
			$strSQL['tblbooking_enddate'] = $temp_enddate;
			
			if(($transfer_price > 0) || $airport_transfer_included > 0){
				$strSQL['airport_arrival_flight_no'] = $txtArrivalFlight;
				$strSQL['airport_arrival_time'] = $txtArrivalTime;
				$strSQL['airport_departure_flight_no'] = $txtDepartureFlight;
				$strSQL['airport_departure_time'] = $txtDepartureTime;
			}
			
			$strSQL['tblbooking_sts'] = 3;
			$strSQL['tblbooking_createdate'] = 'now()';
			$strSQL['tblbooking_updatedate'] = 'now()';
			$strSQL['tblbooking_creditcard_holder'] = $txtcardholder;
			$strSQL['tblbooking_creditcard'] = $txtcreditcard;
			$strSQL['tblbooking_cardtype'] = $cbocardtype;
			$strSQL['tblbooking_expiredate'] = $cboexpmonth;
			//$strSQL['tblbooking_expiredate'] = $cboexpmonth;
			$strSQL['tblbooking_expiredateyear'] = $cboexpyear;
			$strSQL['tblbooking_coupon_code'] = $txtcouponcode;
			
			if(sizeOf($booking_info) > 1){
				$strSQL['is_multiroom'] = 'T';
			}
			
			
			/*
			* if multiple rooms being booked
			*/
			$bedding = 'Double';
			$strDetail['tblbooking_id'] = $bId;
			$strDetail['tblbooking_roomtype'] = $arryrooms[$room_type -1];
			$strDetail['tblbooking_bedding'] = $bedding;
			$strDetail['tblbooking_rate'] = $avg_price_per_night;
			$strDetail['tblbooking_summary'] = $gross_with_tax;
			$strDetail['tblbooking_breakfast'] = '0';
			$strDetail['tblbooking_transfer'] = '0';
			$strDetail['tblbooking_room'] = $rooms;
			$strDetail['tblroom_id'] = $booking_info [0]['room_type'];
			$strDetail['tblbooking_createdate'] = 'now()';
			$strDetail['tblbooking_updatedate'] = 'now()';
			
			
			$strRooms['tblbooking_id'] = $bId;	
			$strRooms['tblroom_id'] = $booking_info [0]['room_type'];	
			$strRooms['tblroom_adult'] = $adults;	
			$strRooms['tblroom_child'] = $children;	
			$strRooms['tblroom_order'] = '1';	
			
			
			//$_SESSION['hdbId'] = $bId;
			
			$post = array('info'=>$booking_info,'ac'=>$acArr,'strsql'=>$strSQL, 'strdetail'=>$strDetail, 'strrooms'=>$strRooms, 'chkavil'=>$chkAvail);
			$booked = true;
			
		}else{
			$message='Required fields are missing. ';
		}
	}
	
	/*
	* check if coupon code applied in search rooms sidebar
	*/
	$txtcouponcode='';
	if(isset($_SESSION['coupon_code_sidebar']) && '' != $_SESSION['coupon_code_sidebar']){
		$txtcouponcode=$_SESSION['coupon_code_sidebar'];
		$_SESSION['coupon_code_sidebar']='';
	}
	
	$api = 'http://localhost:8080/serenity/api/bookroom?adults='.$adults.'&children='.$children;
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $api);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec ($ch);

	curl_close ($ch);
	
	/*
	* Getting results to api call.
	* Decode json to PHP array.
	*/
	
	$r = json_decode($result, true);
	//die();
	
	//echo $_SESSION['hdbId'];
	//print_r($r);
	if($booked == true){
	
		$redirecturl = WEBSITE_HTTPS_URL.'booking_complete.php';
		header("Location: ".$redirecturl);
		exit;
	}
	unset($_SESSION['hdbId']);
	$_SESSION['hdbId'] = $r[bid]+1;
	
	if(count($r == 1)){
		$row_room=$r[row_room];

		$temp_room_name=$row_room['name'];
		$temp_allowed_extra_beds=$row_room['number_of_extra_beds'];
		$temp_adults_included=$row_room['adults_included_in_rate'];
		
		if(($adults + $children) > ($temp_allowed_extra_beds + $temp_adults_included)){
			$booking_error = 'The <b>' . $temp_room_name .'</b> accomodates a maximum of <b>' . ($temp_allowed_extra_beds + $temp_adults_included) . '</b> people per room, excluding children under 6. To complete this booking, please reduce the number of guests for this reservation.<br/><br/>To go back to home page <a href="index.php">click here</a><br/><br/>Or email us at <a href="mailto:rsvnm@serenityphuket.com">rsvnm@serenityphuket.com</a>' . '<br/>';
			break;		
		}
	}else{
		$booking_error='An error has occured. Please contact reservation depratment at <a href="mailto:rsvnm@serenityphuket.com">rsvnm@serenityphuket.com</a>.' . '<br/>';
	}
	
	if($r['chk_avail']['rooms']==0){
        $booking_error = '<h2 class="sc_title_align_center sc_title sc_title_underline  color_1">Contact <span class="sc_highlight">Information</span></h2>';
        $booking_error .= '<div class="sc_content sc_subtitle sc_aligncenter text_styling">Rooms are not available for your choosen dates, please select different dates or <a href="mailto:rentals@rawaihomes.com?Subject=Rooms are not available" target="_top"> Contact us</a></div>';
        $booking_error .='<div class="sc_contact_form_button margin_top_small" style="text-align:center">';
        $booking_error .= '<div class="squareButton sc_button_style_accent_2 sc_button_size_big global big">';
        $booking_error .= '<a href="/search_rooms.php" onclick="goto_search()" class="sc_contact_form_submit"><span class="" aria-hidden="true"></span> &nbsp;Back to Search Rooms</a>';
        $booking_error .= '</div></div>';
    }

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
		<link href="production/css/01b_google_font.css" rel="stylesheet" media="none" onload="if(media!='all')media='all'">
        <!--<link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800%7CShadows+Into+Light" rel="stylesheet" type="text/css">-->
		
		<!-- Vendor CSS -->
		<link rel="stylesheet" href="vendor/bootstrap/bootstrap.css">
		<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css">
		<link rel="stylesheet" href="vendor/owlcarousel/owl.carousel.min.css" media="screen">
		<link rel="stylesheet" href="vendor/owlcarousel/owl.theme.default.min.css" media="screen">
		<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" media="screen">

        <link href="css/font-awesome.css" rel="stylesheet">

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
        </style>

		<?php include("./includes/common.googleanalytics.php"); ?>
	</head>
	<body>

		<div class="body">
		
            <!--NAVI START-->
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
                                
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>
            <!--NAVI END-->


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
					<div class="container">
						<div class="col-md-12">	
							<h2 style="padding-top:30px;text-align:center">Your <strong>Contact</strong> Information</h2>
						</div>	
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
                                                        <input class="form-control" style="height:34px" placeholder="Email Address" type="email" id="fs_email"/>
                                                        <input type="hidden" id="fs_room_type" value="<?php echo $room_type;?>">
                                                        <input type="hidden" id="fs_room_type_name" value="<?php 
                                                                foreach($booking_info as $val){
                                                                    echo $arryrooms[$val['room_type']-1] . ', ';
                                                                }
                                                            ?>">
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
					</div>
				</div>

				<div class="container">

					<div class="row">
						<?php if($booking_error ==''){?>
							<form action="<?php echo WEBSITE_HTTPS_URL.$current_filename;?>" method="post" target="_self" id="form" onSubmit="return checkContactInformation(this);">
								<input type="hidden" name="frmaction" value="CHECKBOOKINGINFO">
								<input type="hidden" name="booking_rate" value="<?php echo $average_price_per_night; ?>">
								<input type="hidden" name="booking_summary" value="<?php echo $gross_after_tax; ?>">
						
								<div class="col-md-8">
								
									
									<div class="booking_left">	
										<div id="bookingform" class="booking-form">
											<p class="message"><?php echo $message;?></p>
											<div class="horizontal-space"></div>
											
											<div class="col-md-3">
												<div class="booking-form-label">Title:</div>
											</div>
											<div class="col-md-9">
												<div class="booking-form-text">
													<select name="cbotitle" class="default" id="cbotitle">
														<option value="Mr.">Mr.</option>
														<option value="Miss.">Miss.</option>
														<option value="Mrs.">Mrs.</option>
													</select>
												</div>
											</div>	
											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Firstname:<span class="txtrequired">*</span></div>
											<div class="col-md-9"><input name="txtname" type="text" class="text" id="txtname" value="<?php echo $txtname;?>" /></div>
											
											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Lastname:<span class="txtrequired">*</span></div>
											<div class="col-md-9"><input name="txtsurname" type="text" class="text" id="txtsurname" value="<?php echo $txtsurname;?>" /></div>

											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Email:<span class="txtrequired">*</span></div>
											<div class="col-md-9"><input name="txtemail" type="text" class="text" id="txtemail" value="<?php echo $txtemail;?>" /></div>
											
											<div class="horizontal-space"></div>
																			
											<div class="col-md-3">Telephone:<span class="txtrequired">&nbsp;*</span></div>
											<div class="col-md-9">
												<input name="txttelcountry" type="hidden" class="text" style="width:30px" id="txttelcountry" maxlength="4" />
												<input name="txttel" type="text" class="text" id="txttel" value="<?php echo $txttel;?>" />
											</div>	
											
											<div class="col-md-12"><small>* Country Code and Telephone/Mobile Number</small></div>
											<div class="horizontal-space"></div>
					
										</div>
										<div class="horizontal-space"></div>
										<div class="horizontal-space"></div>
										<div class="horizontal-space"></div>
										<div class="horizontal-space"></div>
									</div>
									<hr />
									<div class="col-md-12"><h3>Your credit card details:</h3></div>
									<div class="booking_left">	
										<div class="booking-form">
										   
											<div class="col-md-3">Cardholder Name:<span class="txtrequired">*</span></div>
											<div class="col-md-9"><input id="txtcardholder" name="txtcardholder" type="text" class="text" value="<?php echo $txtcardholder;?>" /></div>
											<div class="horizontal-space"></div>
											 
											<div class="col-md-3">Card Type:<span class="txtrequired">*</span></div>
											<div class="col-md-9">
												<div class="fleft">
													<select name="cbocardtype" class="default" id="cbocardtype">
														<option value="MasterCard" <?php if($cbocardtype=='MasterCard') echo 'selected="selected"';?>>MasterCard</option>
														<option value="VisaCard" <?php if($cbocardtype=='VisaCard') echo 'selected="selected"';?>>Visa</option>
														<option value="AmExCard" <?php if($cbocardtype=='AmExCard') echo 'selected="selected"';?>>American Express</option>
														<option value="DinersClubCard" <?php if($cbocardtype=='DinersClubCard') echo 'selected="selected"';?>>Diners Club</option>
														<option value="DiscoverCard" <?php if($cbocardtype=='DiscoverCard') echo 'selected="selected"';?>>Discover</option>
														<option value="enRouteCard" <?php if($cbocardtype=='enRouteCard') echo 'selected="selected"';?>>enRoute</option>
														<option value="JCBCard" <?php if($cbocardtype=='JCBCard') echo 'selected="selected"';?>>JCB</option>
														<option value="ChinaUnionPay" <?php if($cbocardtype=='ChinaUnionPay') echo 'selected="selected"';?>>China Union Pay</option>
														
													</select>
												</div>
												<div class="fleft" style="">&nbsp;&nbsp;<img src="./img/cc_logos.jpg" width="211px" height="28px"/></div>
											</div>
											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Credit card no.:<span class="txtrequired">*</span></div>
											<div class="col-md-9"><input name="txtcreditcard" type="text" class="text" id="txtcreditcard" maxlength="16" value="<?php echo $txtcreditcard;?>"/></div>

											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Expiry Date:<span class="txtrequired">*</span></div>
											<div class="col-md-9">
												<select name="cboexpmonth" class="default" id="cboexpmonth">
													<option value="1" <?php if($cboexpmonth=='1') echo 'selected="selected"';?>>1</option>
													<option value="2" <?php if($cboexpmonth=='2') echo 'selected="selected"';?>>2</option>
													<option value="3" <?php if($cboexpmonth=='3') echo 'selected="selected"';?>>3</option>
													<option value="4" <?php if($cboexpmonth=='4') echo 'selected="selected"';?>>4</option>
													<option value="5" <?php if($cboexpmonth=='5') echo 'selected="selected"';?>>5</option>
													<option value="6" <?php if($cboexpmonth=='6') echo 'selected="selected"';?>>6</option>
													<option value="7" <?php if($cboexpmonth=='7') echo 'selected="selected"';?>>7</option>
													<option value="8" <?php if($cboexpmonth=='8') echo 'selected="selected"';?>>8</option>
													<option value="9" <?php if($cboexpmonth=='9') echo 'selected="selected"';?>>9</option>
													<option value="10" <?php if($cboexpmonth=='10') echo 'selected="selected"';?>>10</option>
													<option value="11" <?php if($cboexpmonth=='11') echo 'selected="selected"';?>>11</option>
													<option value="12" <?php if($cboexpmonth=='12') echo 'selected="selected"';?>>12</option>
												</select>
												&nbsp;&nbsp;
												<select name="cboexpyear" class="default" id="cboexpyear">
												<?php 
													$current_year=date('Y');
													for($i=$current_year;$i<$current_year + 15;$i++){
												?>
													<option value="<?php echo $i;?>"  <?php if($cboexpyear==$i) echo 'selected="selected"';?>><?php echo $i;?></option>
												<?php }?>
												</select>	
											</div>
											<div class="horizontal-space"></div>
											
											<div class="col-md-3">Promotion Code:</div>
											<div class="col-md-9"><input id="txtcouponcode" name="txtcouponcode" type="text" class="text" value="<?php echo $txtcouponcode;?>" style="width:55%" />&nbsp;<a href="#" onClick="return check_coupon_code();">Apply</a>&nbsp;<span id="couponcode_msg"></span></div>
											
											<div class="horizontal-space"></div>
											
											<div class="col-md-12"><small>* Your credit card will not be charged until you arrive at the hotel.</small></div>
											<div class="col-md-12"><small>** All Rates are in THB. The currency exchange tool should be used as a guide only.</small></div>
										</div>
									</div>
									<div class="horizontal-space"></div>
									<div class="horizontal-space"></div>
									<div class="horizontal-space"></div>	
									<hr />
									<!-- policies starts-->
									<div class="col-md-12">
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
									
										<!--policies end-->
										<div class="horizontal-space"></div>

											<h5><a href="privacy_policy.php" target="_blank" class="small-text">+ Privacy Policy</a></h5>
											<div class="horizontal-space"></div>
											<h5><a href="faq.php" target="_blank" class="small-text">+ Frequently Asked Questions</a></h5>											
									
									</div>	
									
								</div>
								
								
                                <input type="hidden" name="best_price_thb" value="<?php echo $average_price_per_night_without_tax ?>">
								<div class="col-md-4">
									<aside class="sidebar">
                                        <div class="price-fighter-widget"
                                            data-pf-hotelkey="d81857e8a5220685b1aedcd797dcc8061e7c722a"
                                            data-pf-rooms="1"
                                            data-pf-direct-price="<?php echo $average_price_per_night_without_tax?>"
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
										<?php include('./includes/inc_booking_details.php');?><br />
										<input type="submit" class="btn btn-primary btn-lg" value="CONFIRM YOUR RESERVATION" />
										<br/><br/>
										
										<a href="https://www.positivessl.com" style="font-family: arial; font-size: 10px; color: #212121; text-decoration: none;"><img src="https://www.positivessl.com/images-new/PositiveSSL_tl_trans2.png" alt="SSL Certificate" title="SSL Certificate" border="0" /></a>

									</aside>
								</div>

								<input  type='hidden' id="average_price_per_night_without_tax" name="average_price_per_night_without_tax" value="<?php echo $average_price_per_night_without_tax; ?>">
								<input type='hidden' id="num_nights" name="num_nights" value="<?php echo $nights;?>"/>
							</form>
						<?php }else{?>
							<p>&nbsp;</p>
							<p><?php echo $booking_error;?></p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							
						<?php }?>						

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
        <script type="text/javascript" src="js/bootstrap-notify.js"></script>
        <script type="text/javascript" src="js/notif.js"></script>
        <script type="text/javascript" src="js/custom.js"></script>
        <script type="text/javascript" src="js/save_search_2.js"></script>
        <script type="text/javascript" src="js/money.js"></script>
        <script type="text/javascript" src="js/money_custom.js"></script>
	</body>
</html>
