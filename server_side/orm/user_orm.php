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
			"'" . $mysqli->real_escape_string($email) . "'" . ")");

		if ($result) {
			$id = $mysqli->insert_id;
			return new User($id, $first, $last, $email);
		}
		return null;
	}

	public static function findByID($id) {
		$mysqli = User::connect();

		$result = $mysqli->query("SELECT * FROM User where id = " . $id);
		if ($result) {
			if ($result->num_rows == 0) {
				return null;
			}

			$user_info = $result->fetch_array();
			
			return new User(intval($user_info['id']),
				$user_info['first'],
				$user_info['last'],
				$user_info['email']);
		}
		return null;
	}

	public static function getAllIDs() {
		$mysqli = User::connect();

		$result = $mysqli->query("SELECT id FROM User");
		$id_array = array();

		if ($result) {
			while ($next_row = $result->fetch_array()) {
				$id_array[] = intval($next_row['id']);
			}
		}
		return $id_array;
	}

	private function __construct($id, $first, $last, $email) {
		$this->id = $id;
		$this->first = $first;
		$this->last = $last;
		$this->email = $email;
	}

	public function getID() {
		return $this->id;
	}

	public function getFirst() {
		return $this->first;
	}

	public function getLast() {
		return $this->last;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setFirst($first) {
		$this->first = $first;
		return $this->update();
	}

	public function setLast($last) {
		$this->last = $last;
		return $this->update();
	}

	public function setEmail($email) {
		$this->email = $email;
		return $this->update();
	}

	private function update() {
		$mysqli = User::connect();

		$result = $mysqli->query("UPDATE User SET " .
			"first=" .
			"'" . $mysqli->real_escape_string($this->first) . "', " .
			"last=" .
			"'" . $mysqli->real_escape_string($this->last) . "', " .
			"email=" .
			"'" . $mysqli->real_escape_string($this->email) . "'" .
			" where id=" . $this->id);
		return $result;
	}

	public function delete() {
		$mysqli = User::connect();
		$mysqli->query("DELETE FROM User WHERE id = " . $this->id);
	}

	public function getJSON() {
		$json_obj = array('id' => $this->id,
			'first' => $this->first,
			'last' => $this->last,
			'email' => $this->email);
		return json_encode($json_obj);
	}
	
}
?>