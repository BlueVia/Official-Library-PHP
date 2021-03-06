<?php

// Set DEBUG true to see the response content
define('DEBUG',true);

// Set your pear and php local path. 
// You must edit this example and complete it with the path in your file system.
$oldpath = set_include_path('/usr/local/share/pear:/usr/share/php:.');

// The path shown above is only for Unix users. If you are using Windows comment the line
// and include this one instead.
// $oldpath = set_include_path('.;C:\php\pear');

// Set your Includes.php path
include_once "../sdk/Includes.php";


// BlueVia provides three environments to support the different development stages of your app.
// Sandbox for testing. Test Live for accessing the live network.
// Note that test mode is free of charge, you only need  SMS and MMS API credits.
// You can choose which of them to use depending on the API endpoint you need.
$mode=BV_Mode::SANDBOX;

// The Location constructor requires the application consumer key, consumer secret, 
// token and token secret
$consumerKey = "vw12012654505986"; 
$consumerSecret = "WpOl66570544"; 

$tokenAccess = "ad3f0f598ffbc660fbad9035122eae74"; 
$tokenSecret = "4340b28da39ec36acb4a205d3955a853"; 


try{
	// The First step for using the Location Api, is to create the Location object
	$location= new BV_Location($mode,$consumerKey,$consumerSecret,$tokenAccess,$tokenSecret);

	if($location) {
		print "<table border=\"1\">";

		// Get the location
		$response= $location->getLocation();
			
		// Show the returned information, only if DEBUG is set to true

		if (DEBUG && $response) {
			print "<h1>Location API</h1>";
			print "<h3>User Location </h3>";
			print "<tr><td>Report status</td><td>".$response->reportStatus."</td></tr>";
			print "<tr><td>Coordinates: Latitude</td><td>".$response->coordinatesLatitude."</td></tr>";
			print "<tr><td>Coordinates: Longitude</td><td>".$response->coordinatesLongitude."</td></tr>";
			print "<tr><td>Current location accuracy</td><td>".$response->accuracy."</td></tr>";
			print "<tr><td>Timestamp</td><td>".$response->timestamp."</td></tr>";
		}
	}

} catch(Exception $e) {
	print "<h1>Location</h1>";
	print "<h3>User Location </h3>";
	print "<p>Exception retrieving user location ". $e->getMessage(). "</p>";
}




