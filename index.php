<!DOCTYPE html>
<html>
<head>
	<title>Improving opgave</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	
<header>
	<h1>Solopgang og solnedgang</h1>
	<form method="get" action="index.php">
		<select name="location">
			<option selected="selected" value="Copenhagen">Copenhagen</option>
			<option value="Kolding">Kolding</option>
			<option value="Aarhus">Aarhus</option>
			<option value="Herning">Herning</option>
		</select>
		<input name="dato" type="date">
		<input type="submit" name="submit" value="Submit">
	</form>
</header>

</body>
</html>
<?php


function getDates() {

	if (empty($_GET['dato'])) {
		//sætter default når der ikke er valgt en dato
		$date = date("Y-m-d");
		return $date;
	} 
	elseif (!empty($_GET['dato'])) {
		//Valgte dato returneres
		$date = $_GET['dato'];
		return $date;
	}
}

function getCity() {
	if (empty($_GET['location'])) {
		//sætter default når der ikke er valgt en by
		$selected = "Copenhagen";
		return $selected;
	} 
	elseif (!empty($_GET['location'])) {
		//Valgte by returneres
		$selected = $_GET['location'];
		return $selected;
	}
}


function getLocation() {
	//API kald for længde- og breddegrad til den valgte by
	$api_url = 'http://geodb-free-service.wirefreethought.com/v1/geo/cities?limit=1&offset=0&namePrefix=' . getCity();
				
	//json data til variable og parses til PHP array
	$api_json = file_get_contents($api_url);
	$api_array = json_decode($api_json, true);

	//Data fra API lægges i variabler
	$lat = $api_array['data'][0]['latitude'];
	$lng = $api_array['data'][0]['longitude'];
	$loc = array($lat, $lng);
	return $loc;
}

echo '<div id="wrap">';

function postDays($date, $city, $lat, $lng) {
	$wd = date("N", strtotime($date)); //Hvilken ugedag er den valgte dato
	echo '<h1>', $city, '</h1>';

	//for hver uge dag indtil næste søndag udføres koden
	for ($wd; $wd <= 7; $wd++) {
 	//API kald for solopgang og solnedgang		
	$api_url = 'https://api.sunrise-sunset.org/json?lat='. $lat . '&lng='. $lng . '&date=' . $date .'&formatted=0';

	//json til variable og parses til PHP array
	$api_json = file_get_contents($api_url);
	$api_array = json_decode($api_json, true);

	//Konvertering fra UTZ til CEST
	$tz = new DateTimeZone('Europe/London');
	$localtz = new DateTimeZone('Europe/Copenhagen');
	$sunrise = new DateTime($api_array['results']['sunrise'], $tz);
	$sunset = new DateTime($api_array['results']['sunset'], $tz);
	$sunrise->setTimezone($localtz);
	$sunset->setTimezone($localtz);

	echo '<div class="container">';
	echo '<p>Dato: ', $date , '</p>' ;
	echo '<p>Solopgang: ', $sunrise->format('H:i:s') . "\n", '</p>';
	echo '<p>Solnedgang: ', $sunset->format('H:i:s') . "\n", '</p>';
	echo '</div>';
	$date = date("Y-m-d", strtotime($date. '+ 1 days'));
 }
}

postDays(getDates(), getCity(), getLocation()[0], getLocation()[1]);

echo '</div>';	
?>