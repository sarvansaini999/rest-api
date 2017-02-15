<?php
    
error_reporting( E_ALL & ~E_DEPRECATED );
	
	require_once("Rest.inc.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "serenity";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				
			$this->dbConnect();					
		}
		
		/*
		 *  Database connection 
		*/
		private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
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
		
		private function rooms(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$format = $_REQUEST['format'];
			
			$sql = mysql_query("SELECT rooms.id, rooms.name, rooms.description, rooms.img_url, booking.room_id, booking.chkin, booking.chkout FROM rooms JOIN booking ON(booking.room_id = rooms.id)", $this->db);
			
			switch ($format) {
				case 'json': 
					
					if(mysql_num_rows($sql) > 0){
						$result = array();
						while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
							$result[] = $rlt;
						}
						
						$this->response($this->json($result), 200);
					}
					break;
				case 'xml': 
					if(mysql_num_rows($sql) > 0){
						$xml = new XMLWriter();

						$xml->openURI("php://output");
						$xml->startDocument();
						$xml->setIndent(true);
						
						$xml->startElement('rooms');

						while ($row = mysql_fetch_assoc($sql)) {
						  $xml->startElement("id");
						  $xml->writeRaw($row['id']);
						  $xml->endElement();
						  $xml->startElement("name");
						  $xml->writeRaw($row['name']);
						  $xml->endElement();
						  $xml->startElement("image");
						  $xml->writeRaw($row['img_url']);
						  $xml->endElement();
						  $xml->startElement("checkin");
						  $xml->writeRaw($row['chkin']);
						  $xml->endElement();
						  $xml->startElement("checkout");
						  $xml->writeRaw($row['chkout']);
						  $xml->endElement();
						}
						
						$xml->endElement();

						header('Content-type: text/xml');
						return $xml->flush();
					}
						
					break;
				}
			$this->response('',204);	// If no records "No Content" status
		}
		
		
		private function roomsbook(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$format = $_REQUEST['format'];
			
			$getRoom = mysql_query("SELECT id, name, description, img_url FROM rooms", $this->db);
			$getAval = mysql_query("SELECT room_id, chkin, chkout FROM booking", $this->db);
			
			switch ($format) {
				case 'json': 
					$result = array();
					if(mysql_num_rows($getRoom) > 0){
						
						while($rltRoom = mysql_fetch_array($getRoom,MYSQL_ASSOC)){
							$result[] = $rltRoom;
						}
					}
					if(mysql_num_rows($getAval) > 0){
						
						while($rltAval = mysql_fetch_array($getAval,MYSQL_ASSOC)){
							$result[] = $rltAval;
						}
					}
					$this->response($this->json($result), 200);
					break;
				case 'xml': 
					if(mysql_num_rows($getRoom) > 0 && mysql_num_rows($getAval) > 0){
						$xml = new XMLWriter();

						$xml->openURI("php://output");
						$xml->startDocument();
						$xml->setIndent(true);
						
						$xml->startElement('rooms');

						while ($row = mysql_fetch_assoc($getRoom)) {
						  $xml->startElement("id");
						  $xml->writeRaw($row['id']);
						  $xml->endElement();
						  $xml->startElement("name");
						  $xml->writeRaw($row['name']);
						  $xml->endElement();
						  $xml->startElement("image");
						  $xml->writeRaw($row['img_url']);
						  $xml->endElement();
						}
						
						while ($row = mysql_fetch_assoc($getAval)) {
						  $xml->startElement("roomid");
						  $xml->writeRaw($row['room_id']);
						  $xml->endElement();
						  $xml->startElement("checkin");
						  $xml->writeRaw($row['chkin']);
						  $xml->endElement();
						  $xml->startElement("checkout");
						  $xml->writeRaw($row['chkout']);
						  $xml->endElement();
						}
						
						$xml->endElement();

						header('Content-type: text/xml');
						return $xml->flush();
					}	
					break;
				}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function room(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$_REQUEST['rquest'];
			$sql = mysql_query("SELECT id, name, description, img_url FROM rooms WHERE id = ".$_REQUEST['id'], $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result[] = $rlt;
				}
				
				$this->response($this->json($result), 200);
			}
			$this->response('',204);	
		}
		
		private function bookroom(){	
			
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$room_id = $_REQUEST['id'];
			$room_name = $_REQUEST['name'];
			$chkin = $_REQUEST['chkin'];
			$chkout = $_REQUEST['chkout'];
			
			$sql = "INSERT INTO booking (room_id, name, chkin, chkout) VALUES ('$room_id', '$room_name', '$chkin', '$chkout')";
			if (mysql_query($sql) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: ";
			}
			
		}
		
		
		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
		
	}
	
	$api = new API;
	$api->processApi();
?>