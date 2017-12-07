<?php

require_once('orm/user_orm.php');

$path_components = explode('/', $_SERVER['PATH_INFO']);

// Note that since extra path info starts with '/'
// First element of path_components is always defined and always empty.

if ($_SERVER['REQUEST_METHOD'] == "GET") {
  // GET means either instance look up, index generation, or deletion

  // Following matches instance URL in form
  // /User_orm.php/<id>

  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    // Interpret <id> as integer
    $user_id = intval($path_components[1]);

    // Look up object via ORM
    $user = User::findByID($user_id);

    if ($user == null) {
      // User not found.
      header("HTTP/1.0 404 Not Found");
      print("User id: " . $user_id . " not found.");
      exit();
    }

      // Check to see if deleting
    if (isset($_REQUEST['delete'])) {
      $user->delete();
      header("Content-type: application/json");
      print(json_encode(true));
      exit();
    } 

    // Normal lookup.
    // Generate JSON encoding as response
    header("Content-type: application/json");
    print($user->getJSON());
    exit();
  }

  // ID not specified, then must be asking for index
  header("Content-type: application/json");
  print(json_encode(User::getAllIDs()));
  exit();

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

  // Either creating or updating

  // Following matches /User_orm.php/<id> form
  if ((count($path_components) >= 2) && ($path_components[1] != "")) {

    //Interpret <id> as integer and look up via ORM
    $user_id = intval($path_components[1]);
    $user = User::findByID($user_id);

    if ($user == null) {
      // User not found.
      header("HTTP/1.0 404 Not Found");
      print("User id: " . $user_id . " not found while attempting update.");
      exit();
    }

    // Validate values
    $new_first = false;
    if (isset($_REQUEST['first'])) {
      $new_first = trim($_REQUEST['first']);
    }

    $new_last = false;
    if (isset($_REQUEST['last'])) {
      $new_last = trim($_REQUEST['last']);
    }

    $new_email = false;
    if (isset($_REQUEST['email'])) {
      $new_email = trim($_REQUEST['email']);
    }

    // Update via ORM
    if ($new_first) {
      $user->setFirst($new_first);
    }
    if ($new_last != false) {
      $user->setLast($new_last);
    }
    if ($new_email != false) {
      $user->setEmail($new_email);
    }

    // Return JSON encoding of updated User
    header("Content-type: application/json");
    print($user->getJSON());
    exit();
  } else {

    // Creating a new User item

    // Validate values

    $first = "";
    if (isset($_REQUEST['first'])) {
      $first = trim($_REQUEST['first']);
    }

    $last = "";
    if (isset($_REQUEST['last'])) {
      $last = trim($_REQUEST['last']);
    }

    $email = "";
    if (isset($_REQUEST['email'])) {
      $email = trim($_REQUEST['email']);
    }

    // Create new User via ORM
    $new_user = User::create($first, $last, $email);

    // Report if failed
    if ($new_user == null) {
      header("HTTP/1.0 500 Server Error");
      print("Server couldn't create new User.");
      exit();
    }

    //Generate JSON encoding of new User
    header("Content-type: application/json");
    print($new_user->getJSON());
    exit();
  }
}

// If here, none of the above applied and URL could
// not be interpreted with respect to RESTful conventions.
header("HTTP/1.0 400 Bad Request");
print("Did not understand URL");

?>