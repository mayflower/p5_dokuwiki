<?php
// if available load the phprojekt config file
$phpr_config = fullpath(dirname(__FILE__)).'/../../../config.inc.php';
  if (@file_exists($phpr_config)) include_once($phpr_config);

