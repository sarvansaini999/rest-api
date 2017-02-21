<?php
    
error_reporting( E_ALL & ~E_DEPRECATED );
	
	require_once("api.based.php");
	
	class RoomsApi extends API {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "serenity";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				
			$this->dbConnect();		

			 if( !isset($_SESSION) ){
				$this->init_session();
			}
		}
		
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		
		public function init_session(){
			session_start();
		}
		
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
		/*
		 * Private method searchroom for api call.
		 * 
		 *
		 */
		
		private function searchroom(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$rooms = $this->getrooms();
			
			$startdate = $_REQUEST['chkin'];
			$backout_date = $_REQUEST['chckout'];
			$chk_in = $_REQUEST['chk_in'];
			$chk_out = $_REQUEST['chk_out'];
			$adults = $_REQUEST['adults'];
			$children = $_REQUEST['children'];
			
			$result = array();
			$dis = array();
			$ince = array();
			
			$r_name = $this->get_rooms_name();
			
			for($introom=1;$introom<=8;$introom++){
				
				$strPrice = mysql_query('SELECT tblroomtype_id, MIN(tblallotment_room) AS rooms, CEILING(AVG(tblallotment_price)) as tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date BETWEEN \''.date('Y/m/d', $startdate).'\' AND \'' . date('Y/m/d', $backout_date) .'\' AND tblroomtype_id=' . $introom . ' GROUP BY tblroomtype_id ORDER BY tblroomtype_id ASC', $this->db);
				$query = $strPrice;
						
						while($rlt = mysql_fetch_array($query)){
							$result[] = $rlt;
						}
						$room_id = $introom;
						$discount = $this->room_total_discount($room_id, $chk_in,$chk_out);
						$dis[$introom] = $discount;
						$incentives = $this->get_incentives($room_id, $chk_in,$chk_out,$adults,$children);
						$ince[$introom] = $incentives;
						
			}
			
			$this->response($this->json(array('rooms'=>$rooms, 'roomname'=>$r_name, 'allotment'=>$result, 'incentives'=>$ince, 'discount'=>$dis)), 200);
			
		}
		
		/*
		 * Private method check_avail for check avail.
		 */
		
		private function check_avail($startdate, $backout_date, $introom){
		
				$strPrice = mysql_query('SELECT tblroomtype_id, MIN(tblallotment_room) AS rooms, CEILING(AVG(tblallotment_price)) as tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date BETWEEN \''.date('Y/m/d', $startdate).'\' AND \'' . date('Y/m/d', $backout_date) .'\' AND tblroomtype_id=' . $introom . ' GROUP BY tblroomtype_id ORDER BY tblroomtype_id ASC', $this->db);
				$query = $strPrice;
						
			return $rlt = mysql_fetch_array($query);
					
		}
		
		/*
		 * Private method getrooms geting all rooms info.
		 */
		
		private function getrooms(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			
			$rooms = mysql_query("SELECT id, name, description, img_url FROM rooms", $this->db);
			
			
				if(mysql_num_rows($rooms) > 0){
					$result = array();
					while($rlt = mysql_fetch_array($rooms,MYSQL_ASSOC)){
						$result[] = $rlt;
					}
					
					return $result;
				}
					
			$this->response('',204);	// If no records "No Content" status
		}
		
		/*
		 * Private method get_rooms_name geting room name and guests.
		 */
		
		
		private function get_rooms_name(){
		
			$query_text='SELECT id,name, adults_included_in_rate + number_of_extra_beds  as guests_allowed FROM rooms';
			$result_rooms=mysql_query($query_text);

			$rooms_desc_array=array();

			if(mysql_num_rows($result_rooms) > 0){
				while($row_rooms =mysql_fetch_assoc($result_rooms)){
					$rooms_desc_array[$row_rooms['id']]= array('name'=>$row_rooms['name'],'guests_allowed'=>$row_rooms['guests_allowed']);
				}
			}
			return $rooms_desc_array;
		}
		
		/*
		 * Private method room_total_discount geting total discount passed by 3 parameter.
		 * @ param1 as room_id
		 * @ param2 as check_in
		 * @ param3 as check_out
		 * return discount as value
		 */
		
		private function room_total_discount($room_id, $chk_in,$chk_out){
	
			$discount=0;//assume no discount by default

			$checkin = explode('/',$chk_in);
			$checkout = explode('/',$chk_out);

			$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
			$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

			
			$nights=$this->calculate_nights_stay($chk_in,$chk_out);	
			
			$i=0;
			
			while ($i < $nights) {
				$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
				$qprice = mysql_query('SELECT tblroomtype_id, tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_id);
				$rprice = mysql_fetch_array($qprice);
				
				
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
		 * Private method get_incentives.
		 * @ param1 as room_id
		 * @ param2 as check_in
		 * @ param3 as check_out
		 * @ param4 as adults
		 * @ param5 as children
		 * array to return: {id,title, description,thumb_image}
		 */
		
		function get_incentives($room_id, $chk_in,$chk_out,$adults,$children){
		
			if($room_id =='' || $chk_in =='' || $chk_out =='') return '';
			
			$incentives=array();
			
			$checkin = explode('/',$chk_in);
			$checkout = explode('/',$chk_out);
			
			$startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
			$enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);

			$start_date = date('Y-m-d', mktime('00','00','00', date('m',$startdate), date('d',$startdate), date('Y',$startdate)));
			$end_date = date('Y-m-d', mktime('00','00','00', date('m',$enddate), date('d',$enddate) -1 , date('Y',$enddate)));

			//$nights = number_nights($startdate,$enddate);
			$nights = $this->calculate_nights_stay($chk_in,$chk_out);
			
			$query_text="SELECT id,title,description,room_type,thumb_image,minimum_nights,incentive_type,airport_transfer FROM incentives WHERE is_active='1' AND ('$start_date' BETWEEN start_date AND end_date  AND '$end_date' BETWEEN start_date AND end_date)";
			
			$result=mysql_query($query_text, $this->db);
			
			if(mysql_num_rows($result) >0 ){
				while($row=mysql_fetch_assoc($result)){
					$rooms=$row['room_type'];
					
					if($rooms !=''){
						$rooms_array=explode(',', $rooms);
						//print_r($rooms_array);
						if(sizeOf($rooms_array) > 0 && $row['minimum_nights'] <= $nights){
							for($i=0;$i<sizeOf($rooms_array);$i++){
								
								if($rooms_array[$i] == $room_id){
									
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
		* Extra Beds
		*/

		function calculate_extra_beds_price($room_type,$adults,$rooms){
			$extra_beds_price=0;
			$query_text='SELECT extra_beds_allowed,adults_included_in_rate,extra_bed_price FROM rooms WHERE id=' .$room_type;
			$result_extra_bed=mysql_query($query_text, $this->db);
			if(mysql_num_rows($result_extra_bed)==1){
				$row_extra_bed=mysql_fetch_assoc($result_extra_bed);
				
				if($row_extra_bed['extra_beds_allowed'] =='1'){
					if($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms) >= 1){
						$extra_beds_price=($adults - ($row_extra_bed['adults_included_in_rate'] * $rooms)) * $row_extra_bed['extra_bed_price']; 
					}
				}
			}
			return $extra_beds_price;
		}
		
		/*
		* Calculate Nights
		*/
		
		private function calculate_nights_stay($chk_in,$chk_out){
			$checkin = explode('/',$chk_in);
			$checkout = explode('/',$chk_out);

			$datetime1 = new DateTime($checkin[2] . '-' . $checkin[1] . '-' . $checkin[0]);
			$datetime2 = new DateTime($checkout[2] . '-' . $checkout[1] . '-' . $checkout[0]);
			
			$days = round(abs($datetime1->format('U') - $datetime2->format('U')) / (60*60*24));
			return $days;
		}
		
		/*
		 * Private method bookroom called this method via api http://example.com/api/bookroom.
		 * array to return: { json feed}
		 */
		
		private function bookroom(){	
			
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$bId = "";
			$posts = $_POST;
			$chkavailibilty = "";
			$chkin = $_POST['ac']['chkin'];
			$chkout = $_POST['ac']['chkout'];
			$adults = $_POST['ac']['adults'];
			$children = $_POST['ac']['children'];
			$booking_info = $posts['info'];
			
			$price = 0;
			$totalprice = 0;
			$gross_before_tax=0;
			$rooms=1;
			$abf_bonus_nights=0;		
			$incentives = array();
			/*see if extra room or extra bed need to added*/
			$extra_beds_price=0;
			foreach($booking_info as $val){
				
				
			
				$room_type=$val['room_type'];
				$extra_beds_price_temp= $this->calculate_extra_beds_price($room_type,$adults,$rooms=1);
				if($extra_beds_price_temp > 0 ){
					$temp_nights= $this->calculate_nights_stay($val['start_date'],$val['end_date']);
					$extra_beds_price += $extra_beds_price_temp * $temp_nights;
				}
			}
			
			$incentives[] = $this->get_incentives(2,$chkin,$chkout,$adults,$children);
			foreach($booking_info as $val){
				/*
				* calculate nights
				*/
				$nights = $this->calculate_nights_stay($val['start_date'],$val['end_date']);
				$temp_nights= $this->calculate_nights_stay($val['start_date'],$val['end_date']);
				$room_type=$val['room_type'];
				$checkin=explode('/',$val['start_date']);
				$startdate=mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
				
				$i = 0;
				while ($i < $temp_nights) {
					
					$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
					$qprice = mysql_query('SELECT tblroomtype_id, tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_type, $this->db);
					
					$rprice = mysql_fetch_array($qprice);
					
					$price = $price + $rprice['tblallotment_price'];
					$totalprice = $totalprice + $rprice['tblallotment_price'];

					$i++;
				}
			}
			$_SESSION['total_price']=$totalprice;
			
			
			/*discount*/
			$discount=0;

			foreach($booking_info as $val){
				$discount = $discount + $this->room_total_discount($val['room_type'],$val['start_date'],$val['end_date']);
				
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
			
			
			$rooms_price_total=$totalprice * $rooms - $discount;

			$average_price_per_night_without_tax=round($rooms_price_total/$nights);
			
			/*
			* Add Extra Bed price
			*/
			if($extra_beds_price > 0){
				$average_price_per_night_without_tax=$average_price_per_night_without_tax + round($extra_beds_price/$nights);
			}
			
			
			
			$tax_charges=round($this->tax_room_price($average_price_per_night_without_tax) - $average_price_per_night_without_tax);

			$average_price_per_night=$average_price_per_night_without_tax + $tax_charges;//average price per night [upsell prices are not included here]
			
			$gross_after_tax=round(($average_price_per_night * $nights) +  $this->tax_extra_services($upsell_price) + $this->tax_extra_services($transfer_price = 0),0) ;
			
			$q = "select MAX(tblbooking_id) from tblbooking_transaction";
			$result = mysql_query($q);
			$data = mysql_fetch_array($result);

			$max_id = $data[0];
			if(isset($_POST['strsql'])){
				$strSQL = $posts['strsql'];
				$bId = $this->save_transction($strSQL);
				//unset($_SESSION['hdbId']);
				$_SESSION['hdbId'] = $bId;
			}
			//$bId = $_SESSION['hdbId'];
			if(isset($_POST['strdetail'])){
				$strDetail = $posts['strdetail'];
				
				$this->save_transction_detail($strDetail, $bId);
			}
			if(isset($_POST['strrooms'])){
				$strRooms = $posts['strrooms'];
				
				$this->save_transction_room($strRooms, $bId);
			}
			if(isset($_POST['chkavil'])){
				$chkavil = $posts['chkavil'];
				
				$chkavailibilty = $this->check_avail($chkavil['startdate'], $chkavil['startdate'], $chkavil['room_type']);
			}
			
			foreach($booking_info as $val){
				$room_type=$val['room_type'];
				
				$query_text='SELECT name, adults_included_in_rate,number_of_extra_beds FROM rooms WHERE id=' . $room_type;
				$result_room=mysql_query($query_text);
				
				if(mysql_num_rows($result_room) == 1){
					$row_room=mysql_fetch_assoc($result_room);
				}
				
			}
				$this->multiple_rooms($booking_info, $bId);
				
			// Return result objects 
			$this->response($this->json(array('row_room'=>$row_room, 'chk_avail'=>$chkavailibilty, 'ince'=>$incentives,'bid'=>$max_id)), 200);
		}
		
		private function multiple_rooms($booking_info, $bId=""){
			
			if(sizeOf($booking_info) > 1){
				for($i=1;$i< sizeOf($booking_info);$i++){
					$temp_room_type=$booking_info [$i]['room_type'];
					
					$checkin=explode('/',$booking_info [$i]['start_date']);
					$checkout=explode('/',$booking_info [$i]['end_date']);
					
					$temp_startdate = mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
					$temp_enddate = mktime(0, 0, 0, $checkout[1], $checkout[0], $checkout[2]);
					
					$query_text="INSERT INTO tblbooking_transaction_multiroom(tblbooking_id, tblroom_id, tblbooking_startdate, tblbooking_enddate, created_at,updated_at) VALUES (
						" . $bId."," . $temp_room_type . ",'" . date('Y/m/d',$temp_startdate)."','" . date('Y/m/d',$temp_enddate)."',now(), now()
					)";
					
					mysql_query($query_text);
				}	
			}
			
		}
		
		private function save_transction($strSQL){
			
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$cbotitle = $strSQL['tblbooking_title'];
			$txtname = $strSQL['tblbooking_name'];
			$txtsurname = $strSQL['tblbooking_surname'];
			$txtaddr = $strSQL['tblbooking_addr'];
			$txtzipcode = $strSQL['tblbooking_zipcode'];
			$txtstate = $strSQL['tblbooking_state'];
			$cbocountry = $strSQL['tblbooking_country'];
			$txtemail = $strSQL['tblbooking_email'];
			$txttelcountry = $strSQL['tblbooking_tel_country'];
			$txttel = $strSQL['tblbooking_tel'];
			$txtarrival = $strSQL['tblbooking_arrival'];
			$temp_startdate = $strSQL['tblbooking_startdate'];
			$temp_enddate = $strSQL['tblbooking_enddate'];
			
			if(isset($strSQL['airport_arrival_flight_no'])){
				$txtArrivalFlight = $strSQL['airport_arrival_flight_no'];
				$txtArrivalTime = $strSQL['airport_arrival_time'];
				$txtDepartureFlight = $strSQL['airport_departure_flight_no'];
				$txtDepartureTime = $strSQL['airport_departure_time'];
			
			}
			$tblbooking_sts = $strSQL['tblbooking_sts'];
			$createdate = $strSQL['tblbooking_createdate'];
			$updatedate = $strSQL['tblbooking_updatedate'];
			$txtcardholder = $strSQL['tblbooking_creditcard_holder'];
			$txtcreditcard = $strSQL['tblbooking_creditcard'];
			$cbocardtype = $strSQL['tblbooking_cardtype'];
			$cboexpmonth = $strSQL['tblbooking_expiredate'];
			$cboexpyear = $strSQL['tblbooking_expiredateyear'];
			$txtcouponcode = $strSQL['tblbooking_coupon_code'];
			
			if(sizeOf($booking_info) > 1){
				$is_multiroom = $strSQL['is_multiroom'];
			}
			
			
			/*insert information into db*/
			$strSQL='INSERT INTO tblbooking_transaction SET '.
			'tblbooking_title=\''.$this->make_string_safe($cbotitle).'\' '.
			',tblbooking_name=\''.$this->make_string_safe($txtname).'\' '.
			',tblbooking_surname=\''.$this->make_string_safe($txtsurname).'\' '.
			',tblbooking_addr=\''.$this->make_string_safe($txtaddr).'\' '.
			',tblbooking_zipcode=\''.$this->make_string_safe($txtzipcode).'\' '.
			',tblbooking_state=\''.$this->make_string_safe($txtstate).'\' '.
			',tblbooking_country=\''.$this->make_string_safe($cbocountry).'\' '.
			',tblbooking_email=\''.$this->make_string_safe($txtemail).'\' '.
			',tblbooking_tel_country=\''.$this->make_string_safe($txttelcountry).'\' '.
			',tblbooking_tel=\''.$this->make_string_safe($txttel).'\' '.
			',tblbooking_arrival=\''.$this->make_string_safe($txtarrival).'\' '.
			',tblbooking_startdate=\''.date('Y/m/d',$temp_startdate).'\' '.
			',tblbooking_enddate=\''.date('Y/m/d',$temp_enddate).'\' ';
			
			if(($transfer_price > 0) || $airport_transfer_included > 0){
				$strSQL .='
					,airport_arrival_flight_no=\''.$this->make_string_safe($txtArrivalFlight).'\' ' .
					',airport_arrival_time=\''.$this->make_string_safe($txtArrivalTime).'\' ' .
					',airport_departure_flight_no=\''.$this->make_string_safe($txtDepartureFlight).'\' ' .
					',airport_departure_time=\''.$this->make_string_safe($txtDepartureTime).'\'';	
			}
			
			
			$strSQL .='
			,tblbooking_sts=3 '.
			',tblbooking_createdate=now() '.
			',tblbooking_updatedate=now() '.
			',tblbooking_creditcard_holder=\''.$this->make_string_safe($txtcardholder).'\' '.
			',tblbooking_creditcard=\''.$this->make_string_safe($txtcreditcard).'\' '.
			',tblbooking_cardtype=\''.$this->make_string_safe($cbocardtype).'\' '.
			',tblbooking_expiredate=\''.$this->make_string_safe($cboexpmonth) . '/' . $this->make_string_safe($cboexpyear).'\' '.
			',tblbooking_coupon_code=\''.$this->make_string_safe($txtcouponcode).'\' ';
			
			
			/* if there are multiple rooms being booked */
			if(sizeOf($booking_info) > 1){
				$strSQL .=',is_multiroom=\'T\'';
			}
			
			
			mysql_query($strSQL, $this->db);

			return $bId = mysql_insert_id();
			
			
		}
		
		private function save_transction_detail($strdetail = "", $bId=""){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$roomtype = $strdetail['tblbooking_roomtype'];
			$bedding = $strdetail['tblbooking_bedding'];
			$average_price_per_night = $strdetail['tblbooking_rate'];
			$gross_after_tax = $strdetail['tblbooking_summary'];
			$breakfast = $strdetail['tblbooking_breakfast'];
			$transfer = $strdetail['tblbooking_transfer'];
			$rooms = $strdetail['tblbooking_room'];
			$room_id = $strdetail['tblroom_id'];
			
			$traDetail = 'INSERT INTO tblbooking_transaction_detail SET '.
				'tblbooking_id = '.$bId.' '.
				',tblbooking_roomtype = \''.$roomtype.'\' '.
				',tblbooking_bedding = \''.$bedding.'\' '.
				',tblbooking_rate = \''.$average_price_per_night.'\' '.
				',tblbooking_summary = \''.$gross_after_tax.'\' '.
				',tblbooking_breakfast = 0 '.
				',tblbooking_transfer = 0 '.
				',tblbooking_room = \''.$this->make_string_safe($rooms).'\' '.
				',tblroom_id = '.$room_id.' '.
				',tblbooking_createdate = now() '.
				',tblbooking_updatedate = now() ';

			mysql_query($traDetail, $this->db);	
			
		}
		
		private function save_transction_room($strRooms = "", $bId=""){
		
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$room_id = $strRooms['tblroom_id'];	
			$adults = $strRooms['tblroom_adult'];	
			$children = $strRooms['tblroom_child'];	
			
			
			$traRooms='INSERT INTO tblbooking_transaction_rooms SET '.
					'tblbooking_id = ' . $bId . ' ' .
					',tblroom_id = ' . $room_id . ' ' .
					',tblroom_adult = \'' . $this->make_string_safe($adults).'\'' .
					',tblroom_child = \'' . $this->make_string_safe($children). '\'' .
					',tblroom_order = 1';

			
			mysql_query($traRooms, $this->db);	
			
		}
		
		private function booking_complete(){
			$tblroom_id = "";
			$bId = $_REQUEST['bid'];
			$room_id = $_REQUEST['room_type'];
			$chk_in = $_REQUEST['chk_in'];
			$chk_out = $_REQUEST['chk_out'];
			$adults = $_REQUEST['adults'];
			$children = $_REQUEST['children'];
			$strSQL = 'UPDATE tblbooking_transaction SET tblbooking_sts=0 WHERE tblbooking_id='.$bId;
			mysql_query($strSQL, $this->db);
			
			$transaction = $this->get_transaction($bId);
			$transaction_detail = $this->get_transaction_deatail($bId);
			$tblroom_id = $transaction_detail['tblroom_id'];
			$transaction_room = $this->get_transaction_room($bId, $tblroom_id);
			$incentives = $this->get_incentives($room_id, $chk_in,$chk_out,$adults,$children);
			
			$price = $this->get_price($room_id, $chk_in,$chk_out);
			
			$response = array(
							'transaction' =>$transaction,
							'transaction_detail' =>$transaction_detail,
							'transaction_room' =>$transaction_room,
							'incentives' =>$incentives,
							'price' =>$price
						);
			//echo '<pre>';
			//print_r($response);
			//echo '</pre>';
			
			$this->response($this->json($response), 200);
		}
		
		private function get_transaction($bId=""){
			
			$strSQLs = 'SELECT * FROM tblbooking_transaction WHERE tblbooking_id='.$bId;
			$query=mysql_query($strSQLs);
			$r=mysql_fetch_array($query);
			return $r;
		}
		
		private function get_transaction_deatail($bId=""){
			
			$strDetail = 'SELECT * FROM tblbooking_transaction_detail WHERE tblbooking_id='.$bId;
			$rquery=mysql_query($strDetail);
			$rdetail=mysql_fetch_array($rquery);
			return $rdetail;
		}
		
		private function get_transaction_room($bId="", $tblroom_id = ""){
			
			$sqlroom = 'SELECT * FROM tblbooking_transaction_rooms t WHERE tblbooking_id = ' . $bId . ' AND tblroom_id = ' . $tblroom_id . ' ORDER BY tblbooking_id ASC, tblroom_id ASC, tblroom_order ASC';
			$queryroom = mysql_query($sqlroom);
			$result = mysql_fetch_array($queryroom);
			return $result;
		}
		
		private function get_price($room_id, $chk_in,$chk_out){
			
			$temp_nights=$this->calculate_nights_stay($chk_in,$chk_out);
			$room_type=$room_id;
			$checkin=explode('/',$chk_in);
			$startdate=mktime(0, 0, 0, $checkin[1], $checkin[0], $checkin[2]);
			
			$i = 0;
			$price = 0;
			$totalprice = 0;
			$prices = array();
			$totalprices = array();
			while ($i < $temp_nights) {
				$rundate = date('Y/m/d', mktime('00','00','00', date('m',$startdate), date('d',$startdate) + $i, date('Y',$startdate)));
				$qprice = mysql_query('SELECT tblroomtype_id, tblallotment_price FROM tblbooking_allotment WHERE tblallotment_date = \''.$rundate.'\' AND tblroomtype_id='.$room_type, $this->db);
				
				$rprice = mysql_fetch_array($qprice);
				$totalprice = $rprice['tblallotment_price'];
				
				$i++;
			}
			return $totalprice;
		}
		
		/*
		* tax calclulations
		* room rates
		*/

		private function tax_room_price($amount){
			return $amount * 1.187;
		}
		
		/*
		* tax clculations
		* services which include extra bed as well :  1.177
		*/
		private function tax_extra_services($amount){
			return $amount * 1.177;
		}
		
		private function make_string_safe($str){
			return addslashes($str);
		}
		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data, JSON_PRETTY_PRINT);
			}
		}
		
	}
	
	$api = new RoomsApi;
	$api->processApi();
?>