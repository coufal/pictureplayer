<?php

if(isset($_GET['get'])) {
  switch($_GET['get']) {
    case 'ls_cameras':
      echo ls_cameras();
      break;
    case 'ls_pictures':
      echo ls_pictures($_GET['path'], $_GET['pps'], $_GET['t']);
      break;
    case 'get_directory_size':
      echo get_directory_size($_GET['path']);
      break;
      case 'delete':
        echo delete($_GET['path']);
        break;
  }
}

function ls_cameras() {
  $dirs = array_filter(glob('*'), 'is_dir');

  return json_encode($dirs);
}

// check if
// time[i+ppa]-time[i] < threshold + 1
// to skip false alerts
// Note: only works if 1 frame/sec is set
// Read like: how many pics within threshold time
// f=filelist, p=pics per alert, t=threshold, i=iterator
function exceeds_threshold($f, $p, $t, $i) {
  if (filemtime($f[$i+$p]) - filemtime($f[$i]) < $t+1) {
    return true;
  }
  return false;
}

function ls_pictures($dir, $pics_per_alert, $threshold) {
  $a = array();
  $i = 0;
  $k = 0;
  //$filelist = glob($dir."*.jpg"); // only for full path incl date dir
  $filelist = glob($dir."/*/*.jpg");
  // try different directory structure
  if (!$filelist) {
    $filelist = glob($dir."/*.jpg");
  }

  // sort by time, newest firsts
  //usort($filelist, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));

  while ($i < count($filelist)) {
    if ( $i+$threshold < count($filelist)
        && exceeds_threshold($filelist, $pics_per_alert, $threshold, $i) )
    {
      $a[$k]["name"] = $filelist[$k];
      $a[$k]["timestamp"] = date("Y-m-d H:i:s", filemtime($filelist[$k]));
      ++$k;
    }
    ++$i;
  }

  return json_encode($a);
}

function delete($path) {
  if (strstr($path, ".")) {
    die('nicetry.gif');
  }

  $path = dirname(__FILE__)."/".$path . "/*";

  $dirs = glob($path); // get all file names

  foreach($dirs as $dir){ // iterate dirs
    $files = glob($dir."/*");
    foreach ($files as $file) {
      if(is_file($file)) {
        //unlink($file); // delete file
      }
    }
  }

  echo json_encode($path);
}

function get_directory_size($path){
    $bytestotal = 0;
    $count = 0;
    $path = realpath($path);
    if($path!==false){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
            ++$count;
        }
    }
    $out = array();
    $out[]=$bytestotal;
    $out[]=$count;
    return json_encode($out);
}

// use KINT directly (which has been loaded automatically via Composer)
//d($dirs);

?>
