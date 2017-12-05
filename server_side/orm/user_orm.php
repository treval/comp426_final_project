<?php
date_default_timezone_set('America/New_York');

class User {
	private $id;
	private $first;
	private $last;
	private $email;

	public static function connect() {
		return new mysqli("classroom.cs.unc.edu", 
			"treval", 
			"blat426f17coharie", 
			"trevaldb");
	}

	public static function create($first, $last, $email) {
		$mysqli = User::connect();

		$result = $mysqli->query("INSERT INTO User VALUES (0, " .
			"'" . $mysqli->real_escape_string($first) . "', " .
			"'" . $mysqli->real_escape_string($last) . "', " .
			"'" . $mysqli->real_escape_string($email) . "', " . ")");

		if ($result) {
			$id = $mysqli->insert_id;
			return new User($id, $first, $last, $email);
		}
		return null;
	}
}
?>