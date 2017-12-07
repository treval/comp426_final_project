<?php

require_once('orm/rsvp_orm.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  // GET means either instance look up, index generation, or deletion

  // Following matches instance URL in form
  // /Rsvp_orm.php/<id>

  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    // Interpret <id> as integer
    $rsvp_id = intval($path_components[1]);

    // Look up object via ORM
    $rsvp = Rsvp::findByID($rsvp_id);

    if ($rsvp == null) {
      // Rsvp not found.
      header("HTTP/1.0 404 Not Found");
      print("Rsvp id: " . $rsvp_id . " not found.");
      exit();
    }

      // Check to see if deleting
    if (isset($_REQUEST['delete'])) {
      $rsvp->delete();
      header("Content-type: application/json");
      print(json_encode(true));
      exit();
    } 

    // Normal lookup.
    // Generate JSON encoding as response
    header("Content-type: application/json");
    print($rsvp->getJSON());
    exit();
  }

  // ID not specified, then must be asking for index
  header("Content-type: application/json");
  print(json_encode(Rsvp::getAllIDs()));
  exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

  // Either creating or updating

  // Following matches /Rsvp_orm.php/<id> form
  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    //Interpret <id> as integer and look up via ORM
    $rsvp_id = intval($path_components[1]);
    $rsvp = Rsvp::findByID($rsvp_id);

    if ($rsvp == null) {
      // Rsvp not found.
      header("HTTP/1.0 404 Not Found");
      print("Rsvp id: " . $rsvp_id . " not found while attempting update.");
      exit();
    }

    // Validate values
    $new_guest = false;
    if (isset($_REQUEST['guest'])) {
      $new_guest = trim($_REQUEST['guest']);
    }

    $new_uid = false;
    if (isset($_REQUEST['uid'])) {
      $new_uid = trim($_REQUEST['uid']);
    }

    $new_eid = false;
    if (isset($_REQUEST['eid'])) {
      $new_eid = trim($_REQUEST['eid']);
    }

    // Update via ORM
    if ($new_guest) {
      $rsvp->setGuest($new_guest);
    }
    if ($new_uid != false) {
      $rsvp->setUid($new_uid);
    }
    if ($new_eid != false) {
      $rsvp->setEid($new_eid);
    }

    // Return JSON encoding of updated Rsvp
    header("Content-type: application/json");
    print($rsvp->getJSON());
    exit();
  } else {

    // Creating a new Rsvp item

    // Validate values

    $guest = "";
    if (isset($_REQUEST['guest'])) {
      $guest = trim($_REQUEST['guest']);
    }

    $uid = "";
    if (isset($_REQUEST['uid'])) {
      $uid = trim($_REQUEST['uid']);
    }

    $eid = "";
    if (isset($_REQUEST['eid'])) {
      $eid = trim($_REQUEST['eid']);
    }

    // Create new Rsvp via ORM
    $new_rsvp = Rsvp::create($guest, $uid, $eid);

    // Report if failed
    if ($new_rsvp == null) {
      header("HTTP/1.0 500 Server Error");
      print("Server couldn't create new Rsvp.");
      exit();
    }

    //Generate JSON encoding of new Rsvp
    header("Content-type: application/json");
    print($new_rsvp->getJSON());
    exit();
  }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.
header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>