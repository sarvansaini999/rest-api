<?php 
$format = "json";
$api = 'http://localhost:8080/restfull/api/v1/roomsbook?format='.$format;
$sUrl = $api;

$sData = array(
				'id' => 1
			);    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sUrl);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $vRes = curl_exec($ch);
    curl_close($ch);
	if($format == 'xml'){
		header('Content-Type: text/xml');
		//echo '<pre>';
		echo $vRes;
	}else{
		echo $vRes;
		$decoded = json_decode($vRes, true);
	}
	$i = 1;
	foreach($decoded as $key => $value)
	{
		$id = $value['id'];
		$name = $value['name'];
		$url = $value['img_url'];
		$desc = $value['description'];
	  $html = "";
	  $html .= "<div style='width: 400px'><a href='$url'>
				<img src='$url' style='width: 50%'></a>
				<form action='book.php' method='POST'>
				<input type='hidden' name='id' value='$id'>
				<input type='hidden' name='name' value='$name'>
				<input type='text' name='chkin' value=''>
				<input type='text' name='chkout' value=''>
				<input type='submit' name='submit' value='Book Now'>
				</form>
				<span><b>$name</b></span>
				<br/><div>$desc</div>";
	  $html .= "</div>";
	  echo $html;
	  if($i == 8){
		die();
	  }
	  $i++;
	}
	echo "Test";
	
  ?>