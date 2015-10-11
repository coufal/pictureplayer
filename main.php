<?php
require_once(__DIR__ . '/FileHandler.php');

if(isset($_GET['get'])) {
  switch($_GET['get']) {
    case 'get_version':
      echo file_get_contents(__DIR__.'/version');
      break;
    case 'ls_cameras':
      echo coufal\PicturePlayer\FileHandler::ls_cameras();
      break;
    case 'ls_pictures':
      echo coufal\PicturePlayer\FileHandler::ls_pictures($_GET['path']);
      break;
    case 'get_directory_size':
      echo coufal\PicturePlayer\FileHandler::get_directory_size($_GET['path']);
      break;
      case 'delete':
        echo coufal\PicturePlayer\FileHandler::delete($_GET['path']);
        break;
  }
}

?>
