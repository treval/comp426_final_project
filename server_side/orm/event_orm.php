<?php
date_default_timezone_set('America/New_York');

class Event
{
  private $id;
  private $name;
  private $date;
  private $time;
  private $type;
  private $description;

  public static function connect() {
    return new mysqli("classroom.cs.unc.edu", 
      "treval", 
      "blat426f17coharie", 
      "trevaldb");
  }

  public static function create($name, $date, $time, $type, $description) {
    $mysqli = Event::connect();

    $dstr = "'" . $date->format('Y-m-d') . "'";

    $tstr = "'" . $time->format('H:i') . "'";

    $result = $mysqli->query("INSERT INTO Event VALUES (0, " .
     "'" . $mysqli->real_escape_string($name) . "', " .
     $dstr . ", " .
     $tstr . ", " .
     "'" . $mysqli->real_escape_string($type) . "', " .
     "'" . $mysqli->real_escape_string($description) . ")");
    
    if ($result) {
      $id = $mysqli->insert_id;
      return new Event($id, $name, $date, $time, $type, $description);
    }
    return null;
  }

  public static function findByID($id) {
    $mysqli = Event::connect();

    $result = $mysqli->query("SELECT * FROM Event WHERE id = " . $id);
    if ($result) {
      if ($result->num_rows == 0) {
        return null;
      }

      $event_info = $result->fetch_array();

      $date = new DateTime($event_info['date']);

      $time = new DateTime($event_info['time']);

      return new Event(intval($event_info['id']),
        $event_info['name'],
        $date,
        $time,
        $event_info['type'],
        $event_info['description']);
    }
    return null;
  }

  public static function getAllIDs() {
    $mysqli = Event::connect();

    $result = $mysqli->query("SELECT id FROM Event");
    $id_array = array();

    if ($result) {
      while ($next_row = $result->fetch_array()) {
        $id_array[] = intval($next_row['id']);
      }
    }
    return $id_array;
  }

  private function __construct($id, $name, $date, $time, $type, $description) {
    $this->id = $id;
    $this->name = $name;
    $this->date = $date;
    $this->time = $time;
    $this->type = $type;
    $this->description = $description;
  }

  public function getID() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function getDate() {
    return $this->date;
  }

  public function getTime() {
    return $this->time;
  }

  public function getType() {
    return $this->type;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setName($name) {
    $this->name = $name;
    return $this->update();
  }

  public function setDate($date) {
    $this->date = $date;
    return $this->update();
  }

  public function setTime($time) {
    $this->time = $time;
    return $this->update();
  }

  public function setType($type) {
    $this->type = $type;
    return $this->update();
  }

  public function setDescription($description) {
    $this->description = $description;
    return $this->update();
  }

  private function update() {
    $mysqli = Event::connect();

    $dstr = "'" . $this->date->format('Y-m-d') . "'";

    $tstr = "'" . $this->time->format('H:i') . "'";

    $result = $mysqli->query("UPDATE Event SET " .
     "name=" .
     "'" . $mysqli->real_escape_string($this->name) . "', " .
     "date=" .
     $dstr . ", " .
     "time=" .
     $tstr . ", ".
     "type=" .
     "'" . $mysqli->real_escape_string($this->type) . "', " .
     "description=" .
     "'" . $mysqli->real_escape_string($this->description) .
     " where id=" . $this->id);
    return $result;
  }

  public function delete() {
    $mysqli = Event::connect();
    $mysqli->query("DELETE FROM Event WHERE id = " . $this->id);
  }

  public function getJSON() {

    $dstr = $this->date->format('Y-m-d');

    $tstr = $this->date->format('H:i');

    $json_obj = array('id' => $this->id,
      'name' => $this->name,
      'date' => $dstr,
      'time' => $tstr,
      'type' => $this->type,
      'description' => $this->description);
    return json_encode($json_obj);
  }
}
?>