<?php

require_once('orm/event_orm.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  // GET means either instance look up, index generation, or deletion

  // Following matches instance URL in form
  // /event.php/<id>

  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    // Interpret <id> as integer
    $event_id = intval($path_components[1]);

    // Look up object via ORM
    $event = Event::findByID($event_id);

    if ($event == null) {
      // Event not found.
      header("HTTP/1.0 404 Not Found");
      print("Event id: " . $event_id . " not found.");
      exit();
    }

      // Check to see if deleting
    if (isset($_REQUEST['delete'])) {
      $event->delete();
      header("Content-type: application/json");
      print(json_encode(true));
      exit();
    } 

      // Normal lookup.
      // Generate JSON encoding as response
    header("Content-type: application/json");
    print($event->getJSON());
    exit();
  }

  // ID not specified, then must be asking for index
  header("Content-type: application/json");
  print(json_encode(Event::getAllIDs()));
  exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

  // Either creating or updating

  // Following matches /event_orm.php/<id> form
  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    //Interpret <id> as integer and look up via ORM
    $event_id = intval($path_components[1]);
    $event = Event::findByID($event_id);

    if ($event == null) {
      // Event not found.
      header("HTTP/1.0 404 Not Found");
      print("Event id: " . $event_id . " not found while attempting update.");
      exit();
    }

    // Validate values
    $new_name = false;
    if (isset($_REQUEST['name'])) {
      $new_name = trim($_REQUEST['name']);
    }

    $new_scheduled = false;
    if (isset($_REQUEST['scheduled'])) {
      $new_scheduled = trim($_REQUEST['scheduled']);
    }

    $new_type = false;
    if (isset($_REQUEST['type'])) {
      $new_type = trim($_REQUEST['type']);
    }

    $new_description = false;
    if (isset($_REQUEST['description'])) {
      $new_name = trim($_REQUEST['description']);
    }

    // Update via ORM
    if ($new_name) {
      $event->setName($new_name);
    }
    if ($new_scheduled != false) {
      $event->setScheduled($new_scheduled);
    }
    if ($new_type) {
      $event->setType($new_type);
    }
    if ($new_description) {
      $event->setPriority($new_description);
    }

    // Return JSON encoding of updated Event
    header("Content-type: application/json");
    print($event->getJSON());
    exit();
  } else {

    // Creating a new Event item

    // Validate values

    $name = "";
    if (isset($_REQUEST['name'])) {
      $name = trim($_REQUEST['name']);
    }


    $scheduled = "";
    if (isset($_REQUEST['scheduled'])) {
      $scheduled = trim($_REQUEST['scheduled']);
    }

    $type = "";
    if (isset($_REQUEST['type'])) {
      $type = trim($_REQUEST['type']);
    }

    $description = "";
    if (isset($_REQUEST['description'])) {
      $description = trim($_REQUEST['description']);
    }

    // Create new Event via ORM
    $new_event = Event::create($name, $scheduled, $type, $description);

    // Report if failed
    if ($new_event == null) {
      header("HTTP/1.0 500 Server Error");
      print("Server couldn't create new event.");
      exit();
    }

    //Generate JSON encoding of new Event
    header("Content-type: application/json");
    print($new_event->getJSON());
    exit();
  }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.

header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>