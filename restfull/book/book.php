<?php

$api = 'http://localhost:8080/restfull/api/v1/bookroom';
$sUrl = $api;
$id = $name = $email = $phone = $chkin = $chkout = $nameErr = $emailErr = $phoneErr = $chkinErr = $chkoutErr = "";


if(isset($_POST['booknow'])){
	$id = $_POST['id'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$chkin = $_POST['chkin'];
	$chkout = $_POST['chkout'];
	
	if($name == ""){
		$nameErr = "Please enter your name.";
	}
	if($email == ""){
		$emailErr = "Please enter your email.";
	}
	if($phone == ""){
		$phoneErr = "Please enter your phone.";
	}
	if($chkin == ""){
		$chkinErr = "Please enter in checkin.";
	}
	if($chkout == ""){
		$chkoutErr = "Please enter in checkout.";
	}
	
	
	if($name != "" && $email != "" && $phone != "" && $chkin != "" && $chkout != ""){
	$post = array(
				'id' => $id,
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'chkin' => $chkin,
				'chkout' => $chkout,
			);
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $sUrl);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec ($ch);

	curl_close ($ch);
	
	echo $result;
	}
	
}

if(isset($_POST['id'])){
	$id = $_POST['id'];
}
	?>
	
	<html>
	<head>
	</head>
	<body>
	<div style="width:500px; margin: auto">
	<form method="POST">
		<input type="hidden" name="id" value="<?php echo $id ?>" placeholder=""><br/><br/><br/>
		<input type="text" name="name" value="<?php echo $name ?>" placeholder="Name"><br/><span><?php echo $nameErr ?><span><br/><br/>
		<input type="email" name="email" value="<?php echo $email ?>" placeholder="Email"><br/><span><?php echo $emailErr ?><span><br/><br/>
		<input type="phone" name="phone" value="<?php echo $phone ?>" placeholder="Phone"><br/><span><?php echo $phoneErr ?><span><br/><br/>
		<input type="text" name="chkin" value="<?php echo $chkin ?>" placeholder="Check in"><br/><span><?php echo $chkinErr ?><span><br/><br/>
		<input type="text" name="chkout" value="<?php echo $chkout ?>" placeholder="Check out"><br/><span><?php echo $chkoutErr ?><span><br/><br/>
		<input type="submit" name="booknow" value="Book Now"><br/>
	</form>
	</div>
	</body>
	</html>
	



