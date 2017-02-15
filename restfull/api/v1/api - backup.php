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
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
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
		
		/* 
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER EMAIL>
		 *  pwd : <USER PASSWORD>
		 */
		
		private function login(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			
			// Input validations
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM user WHERE user_email = '$email' AND user_password = '".$password."' LIMIT 1", $this->db);
					if(mysql_num_rows($sql) > 0){
						$result = mysql_fetch_array($sql,MYSQL_ASSOC);
						
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json($result), 200);
					}
					
					$this->response('', 204);	// If no records "No Content" status
				}
			}
			
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}
		
		private function rooms(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$format = $_REQUEST['format'];
			
			$sql = mysql_query("SELECT id, name, description, img_url FROM rooms", $this->db);
			
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
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$format = $_REQUEST['format'];
			
			$sql1 = mysql_query("SELECT id, name, description, img_url FROM rooms", $this->db);
			$sql2 = mysql_query("SELECT room_id, chkin, chkout FROM booking", $this->db);
			
			switch ($format) {
				case 'json': 
					$result = array();
					if(mysql_num_rows($sql1) > 0){
						
						while($rlt1 = mysql_fetch_array($sql1,MYSQL_ASSOC)){
							$result[] = $rlt1;
						}
					}
					if(mysql_num_rows($sql2) > 0){
						
						while($rlt2 = mysql_fetch_array($sql2,MYSQL_ASSOC)){
							$result[] = $rlt2;
						}
					}
					$this->response($this->json($result), 200);
					break;
				case 'xml': 
					if(mysql_num_rows($sql1) > 0 && mysql_num_rows($sql2) > 0){
						$xml = new XMLWriter();

						$xml->openURI("php://output");
						$xml->startDocument();
						$xml->setIndent(true);
						
						$xml->startElement('rooms');

						while ($row = mysql_fetch_assoc($sql1)) {
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
						
						while ($row = mysql_fetch_assoc($sql2)) {
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
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
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
				// If success everythig is good send header as "OK" and return list of users in JSON format
				$this->response($this->json($result), 200);
			}
			$this->response('',204);	// If no records "No Content" status
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
		
		private function users(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM user WHERE user_status = 1", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = array();
				while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)){
					$result[] = $rlt;
				}
				// If success everythig is good send header as "OK" and return list of users in JSON format
				$this->response($this->json($result), 200);
			}
			$this->response('',204);	// If no records "No Content" status
		}
		
		private function deleteUser(){
			// Cross validation if the request method is DELETE else it will return "Not Acceptable" status
			echo $this->get_request_method();
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				mysql_query("DELETE FROM users WHERE user_id = $id");
				$success = array('status' => "Success", "msg" => "Successfully one record deleted.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
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
	
	// Initiiate Library
	//echo $_REQUEST['rquest'];
	$api = new API;
	$api->processApi();
?>