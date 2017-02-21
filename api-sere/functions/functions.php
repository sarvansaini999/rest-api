<?php 
/*
* check if given date is between  27 dec,2015 and Jan 9 2016
*/
function date_between($dt){
	if($dt >= mktime(0, 0, 0, 12, 28, 2016) && $dt <=mktime(0, 0, 0, 1, 9, 2017)){
		return true;
	}else{
		return false;
	}
}


/*
* swimming pool maintence message
*/
function swimming_pool_under_maintence($chk_in,$chk_out){
	return '';
	
	if(($chk_in >= mktime(0, 0, 0, 9, 4, 2015) && $chk_in <=mktime(0, 0, 0, 9, 19, 2015)) || ($chk_out >= mktime(0, 0, 0, 9, 4, 2015) && $chk_out <=mktime(0, 0, 0, 9, 19, 2015))){
		return '
			<p style="padding:0 0 20px 15px;color:#ff0000;"><strong>Note:</strong><br/>Our swimming pool will be closed for maintenance from 4 to 19 September 2015. Guests are welcome to use the pool at Friendship Beach Resort instead, which is located at walking distance from Serenity. Alternatively, a daily free shuttle service from Serenity to Nai Harn Beach is also available at 10.00h every morning.</p>
		';
	}else{
		return '';
	}	
}


function make_string_safe($str){
	return addslashes($str);
}

function string_for_display($str){
	return stripslashes($str);
}

/*
* add currency symbol
*/
function format_currency($amt){
	if('' == $amt) return '';
	
	return 'THB ' . number_format(round($amt));
}


/*
* format currnecy without symbol
*/

function format_currency_without_symbol($amt){
	if('' == $amt) return '';
	
	return number_format(round($amt));
}

/*
* format credit cardnumber to to show only last few characters
*/

function format_cc_number($cc_no){
	if($cc_no =='') return '';
	return '**** **** **** ' . substr($cc_no,-4,4);
}

/*
* check if given date is valid
*/

function valid_date($str){
	if('' == $str) return false;
	$dt = explode('/',$str);
	if(sizeof($dt)==3){
		return checkdate($dt[1],$dt[0],$dt[2]);
	}else{
		return false;
	}
}

/* calculate number of nights */

function number_nights($startdate,$enddate){
	//$nights = floor(($enddate-$startdate)/86400);
	//problem with 1 Nov to 5 Nov
	
	return floor(($enddate-$startdate)/86400);
	
}

/*
* character limiter which maintains last word integrity
*/
function character_limiter($str, $n, $end_char = '&#8230;'){
	if (strlen($str) < $n){
		return $str;
	}

	$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

	if (strlen($str) <= $n){
		return $str;
	}

	$out = "";
	
	foreach (explode(' ', trim($str)) as $val){
		$out .= $val.' ';
		if (strlen($out) >= $n){
			$out = trim($out);
			return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
		}
	}
}

/*
* send email
*/
function send_email($email_type,$subject,$body,$to=''){
	
	return true;
	require_once('class.phpmailer.php');
	
	$mailer= new PHPMailer();
	$mailer->IsSMTP();
	$mailer->Host = "mail.serenityphuket.com";
	$mailer->SMTPAuth = true;
	$mailer->Username = 'info@serenityphuket.com';
	$mailer->Password = '8?atJ2Er';
	$mailer->IsHTML(true);
	
	$mailer->From = 'rsvn@serenityphuket.com';
	$mailer->FromName = 'Reservation SerenityPhuket';

	/**
	* This is extra email TO..for example like email of customer
	*/
	if($to !=''){
		$mailer->AddAddress($to);
	}
	
	if($email_type =='booking'){
		$mailer->AddAddress('rsvnm@serenityphuket.com');
		$mailer->AddAddress('rsvn@serenityphuket.com');
		
		$mailer->AddBCC('rammigill@hotmail.com');
		$mailer->AddBCC('ecm@serenityphuket.com');
		
	}else if($email_type =='contact'){
		$mailer->AddAddress('rsvnm@serenityphuket.com');
		$mailer->AddAddress('rsvn@serenityphuket.com');

		$mailer->AddBCC('ecm@serenityphuket.com');
		$mailer->AddBCC('rammigill@hotmail.com');		
	}else if($email_type =='weddings'){
		$mailer->AddBCC('ecm@serenityphuket.com');
		$mailer->AddBCC('rammigill@hotmail.com');		
	}else if($email_type =='resturant'){
		$mailer->AddAddress('bookings@east88phuket.com');
		$mailer->AddBCC('ecm@ccc-hotels.com');
		$mailer->AddBCC('rammigill@hotmail.com');		
	}else{
		$mailer->AddAddress('rsvnm@serenityphuket.com');
		$mailer->AddAddress('rsvn@serenityphuket.com');
		
		$mailer->AddBCC('ecm@serenityphuket.com');
		$mailer->AddBCC('rammigill@hotmail.com');		
	}
	
	$mailer->Subject = $subject;
	$mailer->Body = $body;
	
	if(!$mailer->Send()){
		/**
		* PHP MAILER:failed for some reason like say unintended password change.. try normal php mail
		*/
	
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Additional headers
		$headers .= 'To: Bookings SerenityPhuket <rsvn@serenityphuket.com>, ' .$to . "\r\n";
		$headers .= 'From: Bookings SerenityPhuket <rsvn@serenityphuket.com>' . "\r\n";
		$headers .= 'Bcc: ecm@serenityphuket.com,rammigill@hotmail.com' . "\r\n";

		$headers .= 'To: ' .$to . "\r\n";
		$headers .= 'From: Bookings SerenityPhuket <rsvn@serenityphuket.com>' . "\r\n";
		
		
		// Mail it
		@mail($to, $subject, $body, $headers);			
	}
	
}



/*
* tax calclulations
* room rates
*/

function tax_room_price($amount){
	return $amount * 1.187;
}


/*
* tax clculations
* services which include extra bed as well :  1.177
*/
function tax_extra_services($amount){
	return $amount * 1.177;
}


/*
function service_tax($amount){
	$service_charge=10;//%
	return round(($service_charge * $amount/100),2);
}



function vat_tax($amount){
	$vat=7;//%
	return round(($vat * ($amount + service_tax($amount))/100),2);
}


function provincial_tax($amount){
	$provincial=1;//%
	return round(($provincial * $amount/100),2);
}


function calculate_gross_amount_after_tax($amount){
	return round(($amount + ((17.7/100) * $amount)),2);
}


function calculate_gross_amount_after_tax_extra_bed($amount){
	return round(($amount + service_tax($amount) + vat_tax($amount)),2);
}


function calculate_amount_upsell($amount){
	if($amount > 0){
		return round(($amount + service_tax($amount) + vat_tax($amount)),2);
	}else{
		return 0;
	}	
}
*/


/*
* packages
*/

function get_package_details($package_id){
	if ($package_id =='' OR $package_id < 0 ){return false;}
	$query_text="SELECT * FROM packages WHERE id={$package_id} AND is_active=1";
	$result=mysql_query($query_text);
	
	if(mysql_num_rows($result) == 1){
		$row=mysql_fetch_assoc($result);
		return $row;
	}else{
		return false;
	}	
}

/*
* package exception dates
*/

function exception_packages_dates($package_id,$start_date,$end_date){
	$start_date_array = explode('/',$start_date);
	$end_date_array = explode('/',$end_date);
	
 	$startdate =$start_date_array[2]."-".$start_date_array[1]."-".$start_date_array[0];
	$enddate =$end_date_array[2]."-".$end_date_array[1]."-".$end_date_array[0];
	
	$query_text="SELECT COUNT(*) AS total_rows FROM excepation_packages WHERE (package_id=$package_id)
			AND (((CAST(start_date AS date) >= '".$startdate."' AND CAST(start_date AS date) <= '".$enddate."') or (CAST(end_date AS date) <= '".$enddate."' AND CAST(end_date AS date) >= '".$startdate."')) ||
			((CAST(start_date AS date) <= '".$startdate."') AND (CAST(end_date AS date)  >= '".$enddate."')) )";
	
	
	
	$sql=mysql_query($query_text);
	
	
	$result=mysql_fetch_assoc($sql);
	if($result["total_rows"] > 0){
		return false;
	}else{
		return true;
	}
}


/*
Extra Beds
*/

function calculate_extra_beds_price($room_type,$adults,$rooms){
	$extra_beds_price=0;
	$query_text='SELECT extra_beds_allowed,adults_included_in_rate,extra_bed_price FROM rooms WHERE id=' .$room_type;
	$result_extra_bed=mysql_query($query_text);
	if(mysql_num_rows($result_extra_bed)==1){
		$row_extra_bed=mysql_fetch_assoc($result_extra_bed);
		//echo 'Adults:' . $adults . ':Allowed Per Room' .$row_extra_bed['adults_included_in_rate'] .':'. ($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms));
		if($row_extra_bed['extra_beds_allowed'] =='1'){
			if($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms) >= 1){
				$extra_beds_price=($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms)) * $row_extra_bed['extra_bed_price']; 
			}
		}
	}
	return $extra_beds_price;
}

function calculate_extra_beds($room_type,$adults,$rooms){
	$extra_beds=0;
	$query_text='SELECT extra_beds_allowed,adults_included_in_rate FROM rooms WHERE id=' .$room_type;
	$result_extra_bed=mysql_query($query_text);
	if(mysql_num_rows($result_extra_bed)==1){
		$row_extra_bed=mysql_fetch_assoc($result_extra_bed);
		if($row_extra_bed['extra_beds_allowed'] =='1'){
			if($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms) >= 1){
				$extra_beds=$adults - ($row_extra_bed['adults_included_in_rate'] * $rooms); 
			}
		}
	}
	return $extra_beds;

}


/* function to genrate secure hash of booking id + salt */

function encrypt_booking_id($str) { 
    // Create a salt 
    $salt = md5($str."$%*4!#;\.k~'(_@"); 
    
    // Hash the string 
    $string = md5("$salt$str$salt"); 
    
    return $string; 
}


function get_value_serenity($key,$method){
	switch($method){
		case "POST":
			return isset($_POST[$key])?$_POST[$key]:'';
			break;
		case "GET":
			return isset($_GET[$key])?$_GET[$key]:'';
			break;
		case "SESSION":
			return isset($_SESSION[$key])?$_SESSION[$key]:'';
			break;
		default:
			return isset($_REQUEST[$key])?$_REQUEST[$key]:'';
	}
}

function get_value($key,$method){
	switch($method){
		case "POST":
			return isset($_POST[$key])?$_POST[$key]:'';
			break;
		case "GET":
			return isset($_GET[$key])?$_GET[$key]:'';
			break;
		case "SESSION":
			return isset($_SESSION[$key])?$_SESSION[$key]:'';
			break;
		default:
			return isset($_REQUEST[$key])?$_REQUEST[$key]:'';
	}
}

/*booking engine required values can come from get or post*/
function get_value_post_get($key){
	if('' == $key) return '';
	
	
	if(isset($_GET[$key]) && $_GET[$key] !==''){
		return $_GET[$key];	
	}else if(isset($_POST[$key]) && $_POST[$key] !=''){
		return $_POST[$key];
	}	

	return '';	
}


/* insert book now button on rooms page if room is available for choosen dates in cookie*/

function insert_book_now_form($room_id,$chk_in,$chk_out,$adults,$children){
	if($room_id =='' || $chk_in =='' || $chk_out =='' || $adults =='') return '';
	
	$form_code='';
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);
	
	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
	$backout_date = mktime(0, 0, 0, $checkout[1], $checkout[0]-1, $checkout[2]);	

	$query_text='SELECT MIN(tblallotment_room) AS availablity FROM tblbooking_allotment WHERE tblallotment_date BETWEEN \''.date('Y/m/d', $startdate).'\' AND \'' . date('Y/m/d', $backout_date) .'\' AND tblroomtype_id='. $room_id .' GROUP BY tblroomtype_id';
	$result_availablity=mysql_query($query_text);
	if(mysql_num_rows($result_availablity) > 0){
		$row_availablity=mysql_fetch_assoc($result_availablity);
		if($row_availablity['availablity'] =="10"){
			$form_code='<form name="room_book_now" action="'. WEBSITE_HTTPS_URL . 'contact-information.php" method="post">';
				$form_code .='<input type="hidden" name="room_type" value="' . $room_id.'">';
				$form_code .='<input type="hidden" name="rooms" value="1">';
				$form_code .='<input type="hidden" name="chk_in" value="'. $chk_in.'">';
				$form_code .='<input type="hidden" name="chk_out" value="' . $chk_out.'">';
				$form_code .='<input type="hidden" name="selAdults" value="' . $adults.'">';
				$form_code .='<input type="hidden" name="selChildren" value="' . $children.'">';
				$form_code .='<input type="submit" value="Book Now" class="btn-gold">';
			$form_code .='</form>';
			
			return $form_code;
		}
	}		
}

/*
* check how much discount is applicable
* room id, check_in date, check_out date required.
* will return first discount value .. its for display purpose only
* actual discount calculation is done in [ room_total_discount ] based on per day
* will retun numeric value like 10 or 15 or 5 etc
*/

/*
function room_discount_value($room_id,$chk_in,$chk_out){
	if($room_id =='' || $chk_in =='' || $chk_out =='') return 0;

	$discount=0;//default.. no discount
	
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);

	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

	$nights = number_nights($startdate,$enddate);
	
	$i=0;
	
	while ($i < $nights) {
		$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
		
		$query_text="SELECT discount,room_type, FROM discounts WHERE is_active='1' AND ('$rundate' BETWEEN start_date AND end_date) LIMIT 0,1";
		$result_discount=mysql_query($query_text);
		
		if(mysql_num_rows($result_discount) > 0){		
			$row_discount=mysql_fetch_assoc($result_discount);
			$rooms_applicable=$row_discount['room_type'];
			if($rooms_applicable != ''){
				$rooms_array=explode(',', $rooms_applicable);
				for($counter=0;$counter<sizeOf($rooms_array);$counter++){
					if($rooms_array[$counter] == $room_id){
						return $row_discount['discount'];
					}
				}	
			}			
		}	
		$i++;	
	}
	
	return $discount;
}
*/

/*
* return total discount value given discount % and total price
*/

function room_total_discount($room_id, $chk_in,$chk_out){
	
	$discount=0;//assume no discount by default

	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);

	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

	//$nights = number_nights($startdate,$enddate);
	$nights=calculate_nights_stay($chk_in,$chk_out);	
	
	$i=0;
	
	while ($i < $nights) {
		$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
		$qprice = mysql_query('SELECT tblroomtype_id, tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_id);
		$rprice = mysql_fetch_array($qprice);
		
		//get discount for this day
		$query_text="SELECT discount,room_type,minimum_nights FROM discounts WHERE is_active='1' AND ('$rundate' BETWEEN start_date AND end_date)";
		$result_discount=mysql_query($query_text);
		$discount_given_date=0;
		
		if(mysql_num_rows($result_discount) > 0){
			while($row_discount=mysql_fetch_assoc($result_discount)){
				$rooms_applicable=$row_discount['room_type'];
				
				if($rooms_applicable != '' && $row_discount['minimum_nights'] <= $nights){
					$rooms_array=explode(',', $rooms_applicable);
					for($counter=0;$counter<sizeOf($rooms_array);$counter++){
						if($rooms_array[$counter] == $room_id){
							$discount_given_date +=($row_discount['discount'] * $rprice['tblallotment_price'])/100;
							break;
						}
					}	
				}
			}
		}
		$discount +=$discount_given_date;
		$i++;
	}
	return $discount;
}


/*
* long stay
* return total discount value given discount % and total price
*/

function room_total_discount_long_stay($room_id, $chk_in,$chk_out){
	
	$discount=0;//assume no discount by default

	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);

	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

	//$nights = number_nights($startdate,$enddate);
	$nights=calculate_nights_stay($chk_in,$chk_out);	
	
	$i=0;
	
	while ($i < $nights) {
		$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
		if($nights < 30){
			$qprice = mysql_query('SELECT tblroomtype_id, price_less_than_month as tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_id);
		}else{
			$qprice = mysql_query('SELECT tblroomtype_id, price_more_than_month as tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_id);
		}
		
		$rprice = mysql_fetch_array($qprice);
		
		//get discount for this day
		$query_text="SELECT discount,room_type,minimum_nights FROM discounts_long_stay WHERE is_active='1' AND ('$rundate' BETWEEN start_date AND end_date)";
		$result_discount=mysql_query($query_text);
		$discount_given_date=0;
		
		if(mysql_num_rows($result_discount) > 0){
			while($row_discount=mysql_fetch_assoc($result_discount)){
				$rooms_applicable=$row_discount['room_type'];
				
				if($rooms_applicable != '' && $row_discount['minimum_nights'] <= $nights){
					$rooms_array=explode(',', $rooms_applicable);
					for($counter=0;$counter<sizeOf($rooms_array);$counter++){
						if($rooms_array[$counter] == $room_id){
							$discount_given_date +=($row_discount['discount'] * $rprice[tblallotment_price])/100;
							break;
						}
					}	
				}
			}
		}
		$discount +=$discount_given_date;
		$i++;
	}
	return $discount;
}



/*
*
* get incentive applicable to this room in given dates
* will return array
*/


function get_incentives($room_id, $chk_in,$chk_out,$adults,$children){
	if($room_id =='' || $chk_in =='' || $chk_out =='') return '';

	$incentives=array();//array to return: {id,title, description,thumb_image}
	
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);

	$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
	$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

	$start_date = date('Y-m-d', mktime('00','00','00', date('m',$startdate), date('d',$startdate), date('Y',$startdate)));
	$end_date = date('Y-m-d', mktime('00','00','00', date('m',$enddate), date('d',$enddate) -1 , date('Y',$enddate)));

	//$nights = number_nights($startdate,$enddate);
	$nights = calculate_nights_stay($chk_in,$chk_out);
	
	$query_text="SELECT id,title,description,room_type,thumb_image,minimum_nights,incentive_type,airport_transfer FROM incentives WHERE is_active='1' AND ('$start_date' BETWEEN start_date AND end_date  AND '$end_date' BETWEEN start_date AND end_date)";
	
	$result=mysql_query($query_text);
	
	if(mysql_num_rows($result) >0 ){
		while($row=mysql_fetch_assoc($result)){
			$rooms=$row['room_type'];
			if($rooms !=''){
				$rooms_array=explode(',', $rooms);
				if(sizeOf($rooms_array) > 0 && $row['minimum_nights'] <= $nights){
					for($i=0;$i<sizeOf($rooms_array);$i++){
						if($rooms_array[$i] == $room_id){
							/*this incetive is applicable to this room*/
							
							/*
							*incentive_type=B, A, F
							*B: applicable to all
							*A: appliable if children selected is zero
							*F: if children selected greater than 0
							*/
							if($row['incentive_type'] == 'B' || ($row['incentive_type'] == 'A' && $children ==0) || ($row['incentive_type'] == 'F' && $children > 0)){
								$incentives[]=array('id'=>$row['id'],'title' =>$row['title'],'description' =>$row['description'],'thumb_image' =>$row['thumb_image'],'airport_transfer'=>$row['airport_transfer']);
								break;
							}	
						}
					}
				}
			}			
		}
	}
	return $incentives;
}


/*
* multiple rooms booking functions start
*/

//create matrix of dates/rooms

function get_booking_dates($chk_in,$chk_out){
	
	/*
	* rooms available
	*/
	$rooms_available=false;
	
	$rooms_data=array();
	$rooms_data['SerenityRoom']='<p>Chic but comfortable 30m2 rooms which combine contemporary design and modern conveniences with exotic Thai touches.</p><div>Modern conveniences including a flat panel TV, DVD player and sound system, to help you to relax in style. With 30 square meters of luxurious living space, the Serenity Rooms are a great place to rest up before enjoying the many activities within the resort or around Phuket.<br/><br/>The Serenity Rooms feature a modern style en-suite bathroom as well as supremely comfortable beds, furnished with the finest fabrics, to ensure blissful sleep and make the rooms a comfortable and relaxing place to be.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Room-1.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Room-2.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Room-3.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_rm.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
							
    $rooms_data['SerenitySuite']='<p>Spacious & Luxurious 80m2 suites with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>The fully equipped kitchen lets you fix your favourite meals or just treat yourself to the minibar in style.<br/><br/> A large balcony with outdoor seating and sun loungers links to the interior living area through large sliding glass doors and provide a beautiful and private way to enjoy the tropical surroundings. <br/><br/>Each of the Serenity Suites has entertainment centres with flat screen TVs, DVD and CD players. Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Suite-1-Large.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Serenity-Suite-1-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Suite-2.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Suite-3.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	$rooms_data['SeaViewSuite']='<p>Spacious & Luxurious 80m2 sea view suites with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>The large balcony features an outdoor dining area and the great view can be enjoyed from the entire apartment by opening up the floor to ceiling sliding glass doors. <br/><br/>The rooftop lounge, accessible through an exterior stairway on the balcony, has an even better view over the bay. A dining area, sun loungers, and even a private Jacuzzi, all make the rooftop lounge an unbeatable spot to enjoy the view.The open living plan of the apartment links the bedroom with the large living area, dining room, and fully equipped European style kitchen. <br/><br/>Just off the bedroom is a living room area with an entertainment centre with a flat screen TV, DVD and CD player as well as casual seating area ideal for leisurely snacks and meals from the extensive room service menu. Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-1.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-2.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-3.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-3.jpg"/></a>&nbsp;
								<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/seaview_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['H20Suite']='<p>Romantic 200 m2 Private Pool Suite with luxurious living area, European Style Kitchen and rooftop lounge with Private Pool.</p><div>
								The H20 Pool Suites are the perfect romantic getaway offering a beautifully outfitted bedroom with king size bed, a luxurious lounge and adjoining kitchen, balcony, and rooftop lounge with private pool. <br/><br/>The rooftop lounge s private pool has sweeping sea views of the bay and gardens below. Lounge chairs let you enjoy the views and take in the sun beside the pool and a dining area on the upper and lower balcony gives you the perfect spot to dine as the sun goes down.
								<br/><br/>The two H2O units provide breath-taking views over the bay from your own private pool rooftop pool and lounge. <br/><br/>Located on the top floor of building A, these units are perfect for a romantic getaway or honeymoon.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/H2O-Suite-1.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/H20-Image-2-Large.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/H20-Image-2-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/H20-Suite-3.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/h2o.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	$rooms_data['3BedPenthouseSeaviewSuite']='<p>Stunning 3 bedroom 340m2 Top floor Duplex Penthouse suites with panoramic sea views, rooftop lounge and Jacuzzi with contemporary design.</p><div>
								The Large 3 bedroom penthouse seaview suite apartments offer three spacious individual bedrooms, three en-suite bathrooms, a living and dining area and a fully equipped European style Kitchen.<br/><br/> Two of the three bedrooms has their own balcony area. 
								The spacious Balconies have beautiful garden and sea views as these units are located in the front two building closest to the pool and beachfront.<br/><br/> The balconies have an outdoor table and chairs for informal in-room dining for you to relax and enjoy the stunning views.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-1.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-2.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/3-bed-penthouse-suite-3.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-3.jpg"/></a>&nbsp;
								<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/3bedpenthouse.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['PoolResidence']='<p>Ultimate Luxury 350 m2 two bedroom Private Pool Residence, just steps from the water, with private pool, direct sea access and spacious living areas.</p><div>
								With a private pool and sundeck with direct access to the sea, a large living room and dining area, two bedrooms on the second floor each with sea views, and a private rooftop lounge overlooking the water, nothing is missing.
								<br/><br/>The Pool residences offer the ultimate tropical lifestyle experience with a large glass frontage, which allows you to enjoy the wonderful view from the large living room and from the fully equipped European Style Kitchen.<br/>&nbsp; 
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Pool-Residence-1.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Pool-Residence-2-Large.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Pool-Residence-2-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Pool-Residence-3.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_br_rsdnce.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['PenthouseSeaviewSuite']='<p>Stunning 2 bedroom 280m2 Top floor Duplex Penthouse Seaview Suites with panoramic sea views, large rooftop lounge and balcony area with contemporary design.</p><div>The stunning duplex Penthouse Seaview Suites are located on the top floors of the resort, offer gorgeous panoramic views over Chalong Bay, from both the balcony and rooftop lounge area. <br/><br/>The large Penthouse Seaview Suite apartments offer 2 spacious individual bedrooms, two bathrooms, a living and dining area and a fully equipped European style Kitchen. 
								<br/><br/>Both Bedrooms and the living room have individual entertainment centres with flat screen TVs and DVD and CD Players. <br/><br/>Each of the bedrooms also features spacious en-suite bathrooms, with the master bath enjoying his and hers sinks a large bathtub and a separate shower area.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Penthouse-Suite-1.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Penthouse-Suite-2.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Penthouse-Suite-3.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_ph_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';						

	$rooms_data['GrandSeaviewSuite']='<p>Luxurious 2 bedroom 150m2 sea and garden view apartments with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>Set amongst beautifully landscaped gardens, the Grand Seaview Suites stunning views of Chalong Bay. The large interior provides plenty of space for up to 5 adults to relax in two private bedrooms, each with en-suite bathrooms and individual entertainment centres. <br/><br/>Large sliding glass doors open up from the living and dining room area onto a spacious private balcony, offering majestic views over Chalong Bay. The large fully equipped European style kitchen lets you fix your favourite meals or just treat yourself to the minibar in style
								<br/><br/>The Grand Seaview Suites bedrooms and living areas each have their own entertainment centres with flat screen TVs, DVD and CD players, with the master bedroom enjoying his and hers sinks, a large bathtub and separate shower area. <br/><br/>The large balcony areas have beautiful garden and sea views, with these units being located in the front two buildings, closest to the pool and the beachfront. The balconies have an outdoor table and chairs for informal in-room dining and sun loungers for you to relax and enjoy the marvellous views.<br/><br/>Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Grand-Suite-1-Large.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Grand-Suite-1-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Grand-Suite-2.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Grand-Suite-3.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	
	
	
	
	
		
	$rooms_names=array();
	$query="SELECT * FROM rooms  ORDER BY display_order";
	$result=mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($result)){
		while($row=mysql_fetch_assoc($result)){
			/*****Get rooms names in databse create array********/
			$rooms_names[$row['id']]=$row['short_name'];
		}
	}
	
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);	
	
	$start_date=$checkin[2].'-'.$checkin[1].'-'.$checkin[0];
	$end_date=$checkout[2].'-'.$checkout[1].'-'.$checkout[0];	
	
	$dates = (strtotime($start_date) - strtotime($end_date)) / (60 * 60 * 24);
	
	/******Find number of dates********/
	$dates=substr($dates, 1);
	
	
	$dates_array=array();
	$dates_query_array=array();
	
	$count=0;
	$m=0;
	for($i=0; $i <=$dates; $i++){
		if($m==5){
			$count++;	
			$m=0;
		}
		/*******create dates array*******/
		$dates_array[$count][]= gmdate("d M Y", strtotime("+$i day", strtotime($start_date)));
		$dates_query_array[$count][]=gmdate("Y-m-d", strtotime("+$i day", strtotime($start_date)));
		$m++;
	}
	
	
	$temp_data="<form method='post' id='frmMultiRoom' action='" . WEBSITE_HTTPS_URL."contact-information.php'>
					<table cellspacing='5' cellpadding='5' width='100%'>
						<tr><td  colspan='5'></td></tr>
					";
	
	for($c=0; $c < sizeof($dates_array); $c++){
	 	$n=1;
		foreach($rooms_names as $m=>$mm){	
			if($n==1){
				/******First row show dates**********/
				$temp_data.="<tr><td colspan='5'>&nbsp;</td></tr><tr style='background:#cecece;font-weight:bold;'><td width='20%' style='padding:5px;'>Room Types</td>";
					foreach($dates_array[$c] as $values){		
						$temp_data.="<td width='14%' style='padding:5px;'>$values</td>";		
					}
				$temp_data.="</tr>";
			}
			
			$temp_data .="<tr><td colspan='5'>&nbsp;</td></tr>";
			
			$temp_data .="<tr style='border-bottom:solid 1px #cecece;'><td>";
			$temp_data .='<a style="text-decoration:none;cursor:pointer;" onclick="return display_rooms_informaion(\''.str_replace(" ","",$rooms_names[$m]).$c.'\');">' . $rooms_names[$m] . '</a>';
			$temp_data.='<div id="'.str_replace(" ","",$rooms_names[$m]).$c.'" class="dialog dialog-searchbar dialog-rooms-popup" >
			<div class="fright dialog-close" onclick="jQuery(this).parent().hide();"><img src="http://www.serenityphuket.com/img/close.png" /></div>
				<div> <h1>'.$rooms_names[$m].'</h1></div>
				'.$rooms_data[str_replace(" ","",$rooms_names[$m])].'
			</div>';
			
			$temp_data .="</td>";
			
			foreach($dates_query_array[$c] as $val){	

				if(isset($_SESSION['selAdults']) && isset($_SESSION['selChildren']) &&  ($_SESSION['selAdults'] + $_SESSION['selChildren']) > 2){
					$query_text="SELECT tblbooking_allotment.*,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE rooms.id <> 7 AND tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";
				}else{
					$query_text="SELECT tblbooking_allotment.*,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";
				}
				$result_rooms=mysql_query($query_text) or die(mysql_error());
				
				$flag=0;	 
				if(mysql_num_rows($result_rooms)){
					while($row=mysql_fetch_assoc($result_rooms)){
						  
						$date=explode(" ",$row['tblallotment_date']);
						$date=$date[0];
						/******create check box string**********/
						$temp_data.="<td><input class='group1 date_".$date."' type='checkbox' val='" . round($row['tblallotment_price'],0) ."'  value='".$row['tblroomtype_id'] . '_' . $date ."' name='dates[".$date."][".$row['tblroomtype_id']."]' id='dates_".$date."_".$row['tblroomtype_id']."' >&nbsp;". number_format(round($row['tblallotment_price'],0))."</td>";
						//$row['tblallotment_price']
						$flag=1;
						
						$rooms_available=true;	
					}
				}
				if($flag==0){
					$temp_data.="<td><span class='message very-small-text'>SOLD</span></td>";
				}
			}
			
			
			$temp_data.='</tr>';
			$n++;
		}
	}
	
	$temp_data .='</table>';
	
	if($rooms_available){
		$temp_data .='<br/><table width="100%" >';
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .='<tr>';
		$temp_data .='<td  style="color:red;font-weight: bold;text-align:right;"><span id="total_price"></span> <span id="currency_symbol"></span>&nbsp;&nbsp;&nbsp;&nbsp;<input id="submit_multipal_rooms" class="btn btn-primary btn-sml" type="submit" name="submit" value="BOOK NOW"></td>';
		$temp_data .='</tr>';
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .='</table>';
	}	
	$temp_data .='</form>';
	
	
	/*
	* out put matrix if some rooms are available, else return nothing
	*/
	echo $temp_data;
}


/*
Long stay
*/

function get_booking_dates_long_stay($chk_in,$chk_out){
	
	/*
	* rooms available
	*/
	$rooms_available=false;
	$rooms_data=array();
	
	$nights = calculate_nights_stay($chk_in,$chk_out);
	
	$rooms_data['SerenityRoom']='<p>Chic but comfortable 30m2 rooms which combine contemporary design and modern conveniences with exotic Thai touches.</p><div>Modern conveniences including a flat panel TV, DVD player and sound system, to help you to relax in style. With 30 square meters of luxurious living space, the Serenity Rooms are a great place to rest up before enjoying the many activities within the resort or around Phuket.<br/><br/>The Serenity Rooms feature a modern style en-suite bathroom as well as supremely comfortable beds, furnished with the finest fabrics, to ensure blissful sleep and make the rooms a comfortable and relaxing place to be.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Room-1.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Room-2.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Room-3.jpg" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/Thumb-Serenity-Room-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_rm.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityroom]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
							
    $rooms_data['SerenitySuite']='<p>Spacious & Luxurious 80m2 suites with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>The fully equipped kitchen lets you fix your favourite meals or just treat yourself to the minibar in style.<br/><br/> A large balcony with outdoor seating and sun loungers links to the interior living area through large sliding glass doors and provide a beautiful and private way to enjoy the tropical surroundings. <br/><br/>Each of the Serenity Suites has entertainment centres with flat screen TVs, DVD and CD players. Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Suite-1-Large.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Serenity-Suite-1-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Suite-2.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Suite-3.jpg" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/sernty_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitysuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	$rooms_data['SeaViewSuite']='<p>Spacious & Luxurious 80m2 sea view suites with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>The large balcony features an outdoor dining area and the great view can be enjoyed from the entire apartment by opening up the floor to ceiling sliding glass doors. <br/><br/>The rooftop lounge, accessible through an exterior stairway on the balcony, has an even better view over the bay. A dining area, sun loungers, and even a private Jacuzzi, all make the rooftop lounge an unbeatable spot to enjoy the view.The open living plan of the apartment links the bedroom with the large living area, dining room, and fully equipped European style kitchen. <br/><br/>Just off the bedroom is a living room area with an entertainment centre with a flat screen TV, DVD and CD player as well as casual seating area ideal for leisurely snacks and meals from the extensive room service menu. Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-1.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-2.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Serenity-Seaview-Suite-3.jpg" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/Thumbnail-Serenity-Seaview-Suite-3.jpg"/></a>&nbsp;
								<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/seaview_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityseasuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['H20Suite']='<p>Romantic 200 m2 Private Pool Suite with luxurious living area, European Style Kitchen and rooftop lounge with Private Pool.</p><div>
								The H20 Pool Suites are the perfect romantic getaway offering a beautifully outfitted bedroom with king size bed, a luxurious lounge and adjoining kitchen, balcony, and rooftop lounge with private pool. <br/><br/>The rooftop lounge s private pool has sweeping sea views of the bay and gardens below. Lounge chairs let you enjoy the views and take in the sun beside the pool and a dining area on the upper and lower balcony gives you the perfect spot to dine as the sun goes down.
								<br/><br/>The two H2O units provide breath-taking views over the bay from your own private pool rooftop pool and lounge. <br/><br/>Located on the top floor of building A, these units are perfect for a romantic getaway or honeymoon.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/H2O-Suite-1.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/H20-Image-2-Large.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/H20-Image-2-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/H20-Suite-3.jpg" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/Thumbnail-H2O-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/h2o.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenityh2o]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	$rooms_data['3BedPenthouseSeaviewSuite']='<p>Stunning 3 bedroom 340m2 Top floor Duplex Penthouse suites with panoramic sea views, rooftop lounge and Jacuzzi with contemporary design.</p><div>
								The Large 3 bedroom penthouse seaview suite apartments offer three spacious individual bedrooms, three en-suite bathrooms, a living and dining area and a fully equipped European style Kitchen.<br/><br/> Two of the three bedrooms has their own balcony area. 
								The spacious Balconies have beautiful garden and sea views as these units are located in the front two building closest to the pool and beachfront.<br/><br/> The balconies have an outdoor table and chairs for informal in-room dining for you to relax and enjoy the stunning views.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-1.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/3-bedroom-penthouse-suite-2.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/3-bed-penthouse-suite-3.jpg" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/Thumbnail-3-bedroom-penthouse-suite-3.jpg"/></a>&nbsp;
								<a href="http://79.170.44.157/eyenavthailand.com/serenity_phuket/3bedpenthouse.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenity3pent]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['PoolResidence']='<p>Ultimate Luxury 350 m2 two bedroom Private Pool Residence, just steps from the water, with private pool, direct sea access and spacious living areas.</p><div>
								With a private pool and sundeck with direct access to the sea, a large living room and dining area, two bedrooms on the second floor each with sea views, and a private rooftop lounge overlooking the water, nothing is missing.
								<br/><br/>The Pool residences offer the ultimate tropical lifestyle experience with a large glass frontage, which allows you to enjoy the wonderful view from the large living room and from the fully equipped European Style Kitchen.<br/>&nbsp; 
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Pool-Residence-1.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Pool-Residence-2-Large.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Pool-Residence-2-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Pool-Residence-3.jpg" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/Thumbnail-Pool-Residence-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_br_rsdnce.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitypool]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	$rooms_data['PenthouseSeaviewSuite']='<p>Stunning 2 bedroom 280m2 Top floor Duplex Penthouse Seaview Suites with panoramic sea views, large rooftop lounge and balcony area with contemporary design.</p><div>The stunning duplex Penthouse Seaview Suites are located on the top floors of the resort, offer gorgeous panoramic views over Chalong Bay, from both the balcony and rooftop lounge area. <br/><br/>The large Penthouse Seaview Suite apartments offer 2 spacious individual bedrooms, two bathrooms, a living and dining area and a fully equipped European style Kitchen. 
								<br/><br/>Both Bedrooms and the living room have individual entertainment centres with flat screen TVs and DVD and CD Players. <br/><br/>Each of the bedrooms also features spacious en-suite bathrooms, with the master bath enjoying his and hers sinks a large bathtub and a separate shower area.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Penthouse-Suite-1.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-1.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Penthouse-Suite-2.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Penthouse-Suite-3.jpg" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/Thumbnail-Penthouse-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_ph_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitypent]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';						

	$rooms_data['GrandSeaviewSuite']='<p>Luxurious 2 bedroom 150m2 sea and garden view apartments with contemporary interiors and open plan style living areas, kitchen and large balcony.</p><div>Set amongst beautifully landscaped gardens, the Grand Seaview Suites stunning views of Chalong Bay. The large interior provides plenty of space for up to 5 adults to relax in two private bedrooms, each with en-suite bathrooms and individual entertainment centres. <br/><br/>Large sliding glass doors open up from the living and dining room area onto a spacious private balcony, offering majestic views over Chalong Bay. The large fully equipped European style kitchen lets you fix your favourite meals or just treat yourself to the minibar in style
								<br/><br/>The Grand Seaview Suites bedrooms and living areas each have their own entertainment centres with flat screen TVs, DVD and CD players, with the master bedroom enjoying his and hers sinks, a large bathtub and separate shower area. <br/><br/>The large balcony areas have beautiful garden and sea views, with these units being located in the front two buildings, closest to the pool and the beachfront. The balconies have an outdoor table and chairs for informal in-room dining and sun loungers for you to relax and enjoy the marvellous views.<br/><br/>Supremely comfortable beds furnished with the finest fabrics ensure blissful sleep.<br/>&nbsp;
							</div>
							<div class="horizontal-space"></div>
							<div>
								<a href="./img/gallery/searchrooms/Grand-Suite-1-Large.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Grand-Suite-1-Thumbnail.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Grand-Suite-2.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-2.jpg"/></a>&nbsp;
								<a href="./img/gallery/searchrooms/Grand-Suite-3.jpg" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/Thumbnail-Grand-Suite-3.jpg"/></a>&nbsp;
								<a href="http://www.eyenav.com/content/thailand/serenity_phuket/two_brm_suite.htm?iframe=true&amp;width=950&amp;height=650" rel="prettyPhoto[serenitygrandsuite]"><img src="./img/gallery/searchrooms/360.jpg"/></a>
							</div>';
	
	
	
	
	
	
		
	$rooms_names=array();
	$query="SELECT * FROM rooms  ORDER BY display_order";
	$result=mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($result)){
		while($row=mysql_fetch_assoc($result)){
			/*****Get rooms names in databse create array********/
			$rooms_names[$row['id']]=$row['short_name'];
		}
	}
	
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);	
	
	$start_date=$checkin[2].'-'.$checkin[1].'-'.$checkin[0];
	$end_date=$checkout[2].'-'.$checkout[1].'-'.$checkout[0];	
	
	$dates = (strtotime($start_date) - strtotime($end_date)) / (60 * 60 * 24);
	
	/******Find number of dates********/
	$dates=substr($dates, 1);
	
	
	$dates_array=array();
	$dates_query_array=array();
	
	$count=0;
	$m=0;
	for($i=0; $i <=$dates; $i++){
		if($m==5){
			$count++;	
			$m=0;
		}
		/*******create dates array*******/
		$dates_array[$count][]= gmdate("d M Y", strtotime("+$i day", strtotime($start_date)));
		$dates_query_array[$count][]=gmdate("Y-m-d", strtotime("+$i day", strtotime($start_date)));
		$m++;
	}
	
	
	$temp_data="<form method='post' id='frmMultiRoom' action='" . WEBSITE_HTTPS_URL."contact-information-long-stay.php'>
					<table cellspacing='5' cellpadding='5' width='100%'>
						<tr><td  colspan='5'></td></tr>
					";
	
	for($c=0; $c < sizeof($dates_array); $c++){
	 	$n=1;
		foreach($rooms_names as $m=>$mm){	
			if($n==1){
				/******First row show dates**********/
				$temp_data.="<tr><td colspan='5'>&nbsp;</td></tr><tr style='background:#cecece;font-weight:bold;'><td width='20%' style='padding:5px;'>Room Types</td>";
					foreach($dates_array[$c] as $values){		
						$temp_data.="<td width='14%' style='padding:5px;'>$values</td>";		
					}
				$temp_data.="</tr>";
			}
			
			$temp_data .="<tr><td colspan='5'>&nbsp;</td></tr>";
			
			$temp_data .="<tr style='border-bottom:solid 1px #cecece;'><td>";
			$temp_data .='<a style="text-decoration:none;cursor:pointer;" onclick="return display_rooms_informaion(\''.str_replace(" ","",$rooms_names[$m]).$c.'\');">' . $rooms_names[$m] . '</a>';
			$temp_data.='<div id="'.str_replace(" ","",$rooms_names[$m]).$c.'" class="dialog dialog-searchbar dialog-rooms-popup" >
			<div class="fright dialog-close" onclick="jQuery(this).parent().hide();"><img src="http://www.serenityphuket.com/img/close.png" /></div>
				<div> <h1>'.$rooms_names[$m].'</h1></div>
				'.$rooms_data[str_replace(" ","",$rooms_names[$m])].'
			</div>';
			
			$temp_data .="</td>";
			
			foreach($dates_query_array[$c] as $val){	

				if(isset($_SESSION['selAdults']) && isset($_SESSION['selChildren']) &&  ($_SESSION['selAdults'] + $_SESSION['selChildren']) > 2){
				
					if($nights < 30){
						$query_text="SELECT tblbooking_allotment.tblroomtype_id, tblbooking_allotment.price_less_than_month as tblallotment_price,tblbooking_allotment.tblallotment_date,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE rooms.id <> 7 AND tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";					
					}else{
						$query_text="SELECT tblbooking_allotment.tblroomtype_id, tblbooking_allotment.price_more_than_month as tblallotment_price,tblbooking_allotment.tblallotment_date,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE rooms.id <> 7 AND tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";						
					}				
					//$query_text="SELECT tblbooking_allotment.*,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE rooms.id <> 7 AND tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";
				}else{
					if($nights < 30){
						$query_text="SELECT tblbooking_allotment.tblroomtype_id, tblbooking_allotment.price_less_than_month as tblallotment_price,tblbooking_allotment.tblallotment_date,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE  tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";					
					}else{
						$query_text="SELECT tblbooking_allotment.tblroomtype_id, tblbooking_allotment.price_more_than_month as tblallotment_price,tblbooking_allotment.tblallotment_date,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE  tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";						
					}				
				
					//$query_text="SELECT tblbooking_allotment.*,rooms.display_order FROM tblbooking_allotment LEFT JOIN rooms ON tblbooking_allotment.tblroomtype_id=rooms.id  WHERE tblallotment_date = '{$val}' AND tblbooking_allotment.tblallotment_room='10' AND tblbooking_allotment.tblroomtype_id='".$m."'  ORDER BY  rooms.id ASC";
				}
				$result_rooms=mysql_query($query_text) or die(mysql_error());
				
				$flag=0;	 
				if(mysql_num_rows($result_rooms)){
					while($row=mysql_fetch_assoc($result_rooms)){
						  
						$date=explode(" ",$row['tblallotment_date']);
						$date=$date[0];
						/******create check box string**********/
						$temp_data.="<td><input class='group1 date_".$date."' type='checkbox' val='" . round($row['tblallotment_price'],0) ."'  value='".$row['tblroomtype_id'] . '_' . $date ."' name='dates[".$date."][".$row['tblroomtype_id']."]' id='dates_".$date."_".$row['tblroomtype_id']."' >&nbsp;". number_format(round($row['tblallotment_price'],0))."</td>";
						//$row['tblallotment_price']
						$flag=1;
						
						$rooms_available=true;	
					}
				}
				if($flag==0){
					$temp_data.="<td><span class='message very-small-text'>SOLD</span></td>";
				}
			}
			
			
			$temp_data.='</tr>';
			$n++;
		}
	}
	
	$temp_data .='</table>';
	
	if($rooms_available){
		$temp_data .='<br/><table width="100%" >';
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .='<tr>';
		$temp_data .='<td  style="color:red;font-weight: bold;text-align:right;"><span id="total_price"></span> <span id="currency_symbol"></span>&nbsp;&nbsp;&nbsp;&nbsp;<input id="submit_multipal_rooms" class="btn btn-primary btn-sml" type="submit" name="submit" value="BOOK NOW"></td>';
		$temp_data .='</tr>';
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .="<tr><td>&nbsp;</td></tr>";
		$temp_data .='</table>';
	}	
	$temp_data .='</form>';
	
	
	/*
	* out put matrix if some rooms are available, else return nothing
	*/
	echo $temp_data;
}




/*
* nights calculation
* call number_nights function internally
* m-d-y
*/

function calculate_nights_stay($chk_in,$chk_out){
	$checkin = explode('/',$chk_in);
	$checkout = explode('/',$chk_out);

	$datetime1 = new DateTime($checkin[2] . '-' . $checkin[1] . '-' . $checkin[0]);
	$datetime2 = new DateTime($checkout[2] . '-' . $checkout[1] . '-' . $checkout[0]);
	//$interval = $datetime1->diff($datetime2);
	//return $interval->format('%a');
	$days = round(abs($datetime1->format('U') - $datetime2->format('U')) / (60*60*24));
	return $days;
}


/**
* security
*/

function outputKey(){  
    //Generate the key and store it inside the class  
    $key = generateKey();  
    //Store the form key in the session  
    $_SESSION['hash_key'] = $key;  
      
    //Output the form key  
    echo "<input type='hidden' name='hash_key' id='hash_key' value='".$key."' />";  
}

function generateKey() {  
    //Get the IP-address of the user  
    $ip = $_SERVER['REMOTE_ADDR'];  
    $uniqid = uniqid(mt_rand(), true);  
      
    //Return the hash  
    return md5($ip . $uniqid);  
}   
?>