<?php
require_once('user_orm.php');

$test_var = User::create("Trevor", "Levey", "treval@live.unc.edu");

$test_json = $test_var::getJSON();


?>