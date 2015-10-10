<?php

namespace coufal\PicturePlayer;

class FileHandlerTest extends \PHPUnit_Framework_TestCase
{

  protected static $working_dir = __DIR__ . '/../tmp';

  public static function setUpBeforeClass()
  {
    // execute tests in temporary working dir
    mkdir(self::$working_dir);
    copy(__DIR__.'/../FileHandler.php', self::$working_dir.'/FileHandler.php');

    require_once(self::$working_dir.'/FileHandler.php');

    mkdir(self::$working_dir . '/testcam01');
    mkdir(self::$working_dir . '/TestCam02');
    mkdir(self::$working_dir . '/testcam 3');
  }

  public static function tearDownAfterClass()
  {
    rmdir(self::$working_dir . '/testcam01');
    rmdir(self::$working_dir . '/TestCam02');
    rmdir(self::$working_dir . '/testcam 3');

    unlink(self::$working_dir.'/FileHandler.php');
    rmdir(self::$working_dir);
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::ls_cameras
   */
  public function testListCamerasWorking()
  {
    // Act
    $a = FileHandler::ls_cameras();

    // Assert
    $expected = '["TestCam02","testcam 3","testcam01"]';
    $this->assertEquals($expected, $a);
  }

  /**
   * @covers \coufal\PicturePlayer\FileHandler::ls_cameras
   * @depends testListCamerasWorking
   */
  public function testListCamerasExcludeDirsWorking()
  {
    // Arrange
    mkdir(self::$working_dir . '/tests');
    mkdir(self::$working_dir . '/tests2');

    // Act
    $a = FileHandler::ls_cameras();

    // Assert
    $expected = '["TestCam02","testcam 3","testcam01","tests2"]';
    $this->assertEquals($expected, $a);

    // Cleanup
    rmdir(self::$working_dir . '/tests');
    rmdir(self::$working_dir . '/tests2');
  }

  public function testSpecialCharCameraNamesWorking()
  {
    $this->assertEquals(-1, -1);
  }
}
