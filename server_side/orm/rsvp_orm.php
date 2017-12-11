<?php
date_default_timezone_set('America/New_York');

class Rsvp
{
  private $id;
  private $uid;
  private $eid;

  public static function connect() {
    return new mysqli("classroom.cs.unc.edu", 
      "treval", 
      "blat426f17coharie", 
      "trevaldb");
  }

  public static function create($uid, $eid) {
    $mysqli = Rsvp::connect();

    $result = $mysqli->query("INSERT INTO Rsvp VALUES (0, "."'".$uid."', "."'".$eid."');");

    if ($result) {
      $id = $mysqli->insert_id;
      return new Rsvp($id, $uid, $eid);
    }
    return null;
  }

  public static function findByID($id) {
    $mysqli = Rsvp::connect();

    $result = $mysqli->query("SELECT * FROM Rsvp WHERE id = " . $id);
    if ($result) {
      if ($result->num_rows == 0) {
       return null;
     }

     $rsvp_info = $result->fetch_array();

     return new Todo(intval($rsvp_info['id']),
      intval($rsvp_info['uid']),
      intval($rsvp_info['eid']));
   }
   return null;
  }

  public static function getAllIDs() {
    $mysqli = Rsvp::connect();

    $result = $mysqli->query("SELECT id FROM Rsvp");
    $id_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
       $id_array[] = intval($next_row['id']);
      }
    }
      return $id_array;
  }

  public static function findRsvpByEventId($event_id) {
    $mysqli = Rsvp::connect();

    $result = $mysqli->query("SELECT * FROM User U, Event E, Rsvp R WHERE U.id=R.uid and E.id=R.eid and E.id=" . $event_id);

    return json_encode($result);
  }

  private function __construct($id, $uid, $eid) {
    $this->id = $id;
    $this->uid = $uid;
    $this->eid = $eid;
  }

  public function getID() {
    return $this->id;
  }

  public function getUid() {
    return $this->uid;
  }

  public function getEid() {
    return $this->eid;
  }

  public function setUid($uid) {
    $this->uid = $uid;
    return $this->update();
  }

  public function setEid($eid) {
    $this->eid = $eid;
    return $this->update();
  }

  private function update() {
    $mysqli = Rsvp::connect();

    $result = $mysqli->query("UPDATE Rsvp SET " .
      "guest=" .
      "'" . $guest . "', " .
      "uid=" .
      "'" . $uid . "', " .
      "eid=" .
      "'" . $eid . "'" .
      " where id=" . $this->id);
    return $result;
  }

  public function delete() {
    $mysqli = Rsvp::connect();
    $mysqli->query("DELETE FROM Rsvp WHERE id = " . $this->id);
  }

  public function getJSON() {

    $json_obj = array('id' => $this->id,
      'uid' => $this->uid,
      'eid' => $this->eid
    );
    return json_encode($json_obj);
  }
}
?>