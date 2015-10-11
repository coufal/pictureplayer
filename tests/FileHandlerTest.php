<?php

namespace coufal\PicturePlayer;

class FileHandlerTest extends \PHPUnit_Framework_TestCase
{

  protected static $working_dir = __DIR__ . '/..';
  protected static $cameras = ['testcam01', 'TestCam02', 'testcam 3'];
  protected static $pics = [['name' => '4.jpg', 'timestamp' => 1440511124],
                            ['name' => '3.jpg', 'timestamp' => 1442511024],
                            ['name' => '2 3.jpg', 'timestamp' => 1444511124],
                            ['name' => '5.jpg', 'timestamp' => 1444751124]];

  public static function setUpBeforeClass()
  {
    require_once(self::$working_dir.'/FileHandler.php');

    // create camera dirs and fill with images
    foreach(self::$cameras as $camera) {
      if (!mkdir(self::$working_dir . '/' . $camera)) {
        throw new \RuntimeException('Failed to create camera dir.');
      }
      foreach(self::$pics as $pic) {
        if (false === file_put_contents(self::$working_dir.'/'.$camera.'/'.$pic['name'],
                                        $pic['name'])
        || !touch(self::$working_dir.'/'.$camera.'/'.$pic['name'],
                  $pic['timestamp'])) {
          throw new \RuntimeException('Failed to create file.');
        }
        ;
      }
    }
    //throw new \RuntimeException('Failed to create file.');
  }

  public static function tearDownAfterClass()
  {
    // delete files and dirs
    foreach(self::$cameras as $camera) {
      if (!rmdir(self::$working_dir . '/' . $camera)) {
        throw new \RuntimeException('Failed to remove camera dir.');
      }
    }
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::ls_cameras
   */
  public function test_ls_cameras_working()
  {
    // Act
    $a = FileHandler::ls_cameras();

    // Assert
    $expected = '["TestCam02","testcam 3","testcam01"]';
    $this->assertEquals($expected, $a);
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::ls_pictures
   */
  public function test_ls_pictures_working()
  {
    // Assemble
    $a = array();
    $patterns = ['../testcam01', '../../testcam01'];
    $formatted = '[{"name":"testcam01\/4.jpg",'.
                    '"timestamp":"2015-08-25 13:58:44"},'.
                  '{"name":"testcam01\/3.jpg",'.
                    '"timestamp":"2015-09-17 17:30:24"},'.
                  '{"name":"testcam01\/2 3.jpg",'.
                    '"timestamp":"2015-10-10 21:05:24"},'.
                  '{"name":"testcam01\/5.jpg",'.
                    '"timestamp":"2015-10-13 15:45:24"}]';

    // Act
    foreach(self::$cameras as $camera) {
      $normal[] = FileHandler::ls_pictures($camera);
    }
    foreach($patterns as $pattern) {
      $malicious[] = FileHandler::ls_pictures($pattern);
    }

    // Assert
    for($i=0;$i<count($normal); ++$i) {
      $expected = str_replace('testcam01', self::$cameras[$i], $formatted);
      $this->assertEquals($expected, $normal[$i]);
    }
    for($i=0;$i<count($malicious); ++$i) {
      $this->assertEquals($formatted, $malicious[$i]);
    }
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::ls_pictures
   * @depends test_ls_pictures_working
   * @expectedException RuntimeException
   */
  public function test_ls_pictures_exception_working() {
    $a = FileHandler::ls_pictures('testcam01/..');
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::get_directory_size
   */
  function test_get_directory_size_working() {
    $a[] = FileHandler::get_directory_size(self::$cameras[0]);

    $this->assertEquals('[22,4]',$a[0]);
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::get_directory_size
   * @depends test_get_directory_size_working
   * @expectedException RuntimeException
   */
  function test_get_directory_size_exception_working() {
    FileHandler::get_directory_size('testcam01/..');
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::delete
   * @depends test_get_directory_size_working
   */
  function test_delete_working() {
    foreach(self::$cameras as $camera) {
      $response[] = FileHandler::delete($camera);
    }

    for($i=0; $i<count($response); ++$i) {
      $this->assertEquals(json_encode(self::$cameras[$i]),$response[$i]);
      $this->assertEquals('[0,0]',FileHandler::get_directory_size(self::$cameras[0]));
    }
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::delete
   * @depends test_delete_working
   * @depends test_get_directory_size_working
   */
  function test_delete_sanitization_working() {
    $patterns = ['../delsantest', '../../delsantest'];

    foreach($patterns as $pattern) {
      if (!mkdir(self::$working_dir . '/delsantest')) {
        throw new \RuntimeException('Failed to create test dir.');
      }
      if (!touch(self::$working_dir . '/delsantest/empty.jpg')) {
        throw new \RuntimeException('Failed to create test dir.');
      }

      $this->assertEquals('[0,1]',FileHandler::get_directory_size('delsantest'));
      $response = FileHandler::delete($pattern);
      $this->assertEquals(json_encode('delsantest'),$response);
      $this->assertEquals('[0,0]',FileHandler::get_directory_size('delsantest'));

      if (!rmdir(self::$working_dir . '/delsantest')) {
        throw new \RuntimeException('Failed to remove test dir.');
      }
    }
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::delete
   * @depends test_delete_working
   * @expectedException RuntimeException
   */
  public function test_delete_exception_working() {
    FileHandler::delete('testcam01/..');
  }
}
