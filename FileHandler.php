<?php

namespace coufal\PicturePlayer;

class FileHandler
{
  private static $exclude_dirs=['tests'];

  public static function ls_cameras()
  {
    $dirs = array();

    foreach (new \DirectoryIterator(__DIR__) as $file) {
      if ($file->isDir() && !$file->isDot() //only dirs
      && !in_array($file->getFilename(), self::$exclude_dirs) //skip dirs on exclude list
      && $file->getFilename()[0] != '.') { //skip hidden dirs
        $dirs[] = $file->getFilename();
      }
    }

    sort($dirs); // important for tests to work

    return json_encode($dirs);
  }

  public static function ls_pictures($dir) {
    $dir = basename($dir); //sanitize user input

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
      $a[$i]["name"] = $filelist[$i];
      $a[$i]["timestamp"] = date("Y-m-d H:i:s", filemtime($filelist[$i]));
      ++$i;
    }

    return json_encode($a);
  }

  public static function delete($path) {
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

  public static function get_directory_size($path){
    $bytestotal = 0;
    $count = 0;
    $path = realpath($path);
    if($path!==false){
      foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path,
      \FilesystemIterator::SKIP_DOTS)) as $object) {
        $bytestotal += $object->getSize();
        ++$count;
      }
    }
    $out = array();
    $out[]=$bytestotal;
    $out[]=$count;
    return json_encode($out);
  }
}
?>
