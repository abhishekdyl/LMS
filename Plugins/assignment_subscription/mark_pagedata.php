<?php 
require_once('../../config.php');

global $DB, $USER, $PAGE;

$_SESSION['markdata'] = $_POST;
echo json_encode($_SESSION['markdata']);