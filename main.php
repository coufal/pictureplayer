<?php
require_once(__DIR__ . '/FileHandler.php');

if(isset($_GET['get'])) {
  switch($_GET['get']) {
    case 'ls_cameras':
      echo FileHandler::ls_cameras();
      break;
    case 'ls_pictures':
      echo FileHandler::ls_pictures($_GET['path'], $_GET['pps'], $_GET['t']);
      break;
    case 'get_directory_size':
      echo FileHandler::get_directory_size($_GET['path']);
      break;
      case 'delete':
        echo delete($_GET['path']);
        break;
  }
}

?>
