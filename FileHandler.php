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
    //sanitize user input
    $dir = basename($dir);
    if(strcmp($dir[0], '.') == 0) {
      throw new \RuntimeException('Path must not end or begin with a dot.');
    }

    //prevent errors if no timezone was set
    date_default_timezone_set('UTC');

    $a = array();
    $i = 0;
    $k = 0;

    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
    $filelist = array();
    foreach ($rii as $file) {
        if ($file->isDir()){
            continue;
        }
        $k = ['display_name' => $file->getPathname(),
              'name' => $file->getPathname()];
        $filelist[] = $k;
    }

    // sort by time, oldest firsts
    usort($filelist, create_function('$a,$b', 'return filemtime($a["name"]) - filemtime($b["name"]);'));

    while ($i < count($filelist)) {
      $a[$i]["name"] = $filelist[$i]["display_name"];
      $a[$i]["timestamp"] = date("Y-m-d H:i:s", filemtime($filelist[$i]["name"]));
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
