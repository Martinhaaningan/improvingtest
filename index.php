<!DOCTYPE html>
<html>
<head>
	<title>Improving opgave</title>
</head>
<body>
	
<div>
	<h1>Solopgang og solnedgang</h1>
</div>

<form method="get" action="index.php">
	<select name="location">
		<option value="copenhagen" name="city">København</option>
		<option value="kolding" name="city">Kolding</option>
	</select>
	<input name="dato" type="date">
	<input type="submit" name="submit" value="Submit">
</form>


</body>
</html>
<?php

$tz = new DateTimeZone('Europe/London');
$localtz = new DateTimeZone('Europe/Copenhagen');
$date = $_GET['dato'];

$wd = date("N", strtotime($date)); //Hvilken ugedag er den valgte dato

if(isset($_GET['location'])){
echo $_GET['location'];
}

if(!empty($date)){
	if(isset($_GET['submit'])){
		$selected = $_GET['location'];

		//API kald for længde- og breddegrad til den valgte by
		$api_url = 'http://geodb-free-service.wirefreethought.com/v1/geo/cities?limit=1&offset=0&namePrefix=' . $selected;
		
		//json data til variable og parses til PHP array
		$api_json = file_get_contents($api_url);
		$api_array = json_decode($api_json, true);

		//Data fra API lægges i variabler
		$lat = $api_array['data'][0]['latitude'];
 		$lng = $api_array['data'][0]['longitude'];
	}
 	
 	for ($wd; $wd <= 7; $wd++) {
 		//API kald for solopgang og solnedgang		
	 	$api_url = 'https://api.sunrise-sunset.org/json?lat='. $lat . '&lng='. $lng. '&date=' . $date .'&formatted=0';

	 	//json til variable og parses til PHP array
	 	$api_json = file_get_contents($api_url);
	 	$api_array = json_decode($api_json, true);

	 	//Konvertering fra UTZ til CEST
	 	$sunrise = new DateTime($api_array['results']['sunrise'], $tz);
	 	$sunset = new DateTime($api_array['results']['sunset'], $tz);
	 	$sunrise->setTimezone($localtz);
	 	$sunset->setTimezone($localtz);

	 	echo '<p>Dato: ', $date , '</p><br>' ;
	 	echo '<p>Solopgang: </p>', $sunrise->format('H:i:s') . "\n";
	 	echo '<p>Solnedgang: </p>', $sunset->format('H:i:s') . "\n";
	 	$date = date("Y-m-d", strtotime($date. '+ 1 days'));
 	}	
 }
?>