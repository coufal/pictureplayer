<?php

namespace coufal\PicturePlayer;

class FileHandler
{
  private static $exclude_dirs=['tests'];

  private static function sanitize_path($dir) {
    //sanitize user input
    $dir = basename($dir);
    if(strcmp($dir[0], '.') == 0) {
      throw new \RuntimeException('Path must not end or begin with a dot.');
    }
    return $dir;
  }

  private static function get_all_files($dir) {
    $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
    $filelist = array();
    foreach ($rii as $file) {
        if ($file->isDir()){
            continue;
        }
        $filelist[] = $file->getPathname();
    }
    return $filelist;
  }

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
    $dir = self::sanitize_path($dir);

    //prevent errors if no timezone was set
    date_default_timezone_set('UTC');

    $a = array();
    $i = 0;

    $filelist = self::get_all_files($dir);

    // sort by time, oldest firsts
    usort($filelist, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));

    while ($i < count($filelist)) {
      $a[$i]["name"] = $filelist[$i];
      $a[$i]["timestamp"] = date("Y-m-d H:i:s", filemtime($filelist[$i]));
      ++$i;

    }

    return json_encode($a);
  }

  public static function delete($path) {
    $path = self::sanitize_path($path);

    $filelist = self::get_all_files($path);

    foreach ($filelist as $file) {
      if (!unlink($file)) {
        $path = 'Failed to unlink file: '.$file;
        break;
      }
    }

    return json_encode($path);
  }

  public static function get_directory_size($path){
    $path = self::sanitize_path($path);
    
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
