<?php

#What I think this does:
#Should be able to run AJAX with url: url_base/joined.php.
#This should run the orm function in rsvp_orm.php that queries the tables to join them and filter out a single event.
#Then it makes it JSON, and the AJAX will send it along to be used on the client side. Hopefully everything will work this way.
require_once('orm/rsvp_orm.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

if ($_SERVER['REQUEST_METHOD'] == "GET") {

	$event_id = intval($path_components[1]);

	$result = RSVP::findRsvpByEventId($event_id);

	if ($result == null) {
  		// event not found.
		header("HTTP/1.0 404 Not Found");
		print("Rsvp id: " . $event_id . " not found.");
		exit();
	}

	header("Content-type: application/json");
    print($result);
    exit();
}

?>