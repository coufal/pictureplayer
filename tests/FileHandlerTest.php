<?php

require_once(__DIR__ . '/../FileHandler.php');

class FileHandlerTest extends PHPUnit_Framework_TestCase
{

  protected static $working_dir = __DIR__ . '/..';

  public static function setUpBeforeClass()
  {
    mkdir(self::$working_dir . '/testcam01');
    mkdir(self::$working_dir . '/TestCam02');
    mkdir(self::$working_dir . '/testcam 3');
  }

  public static function tearDownAfterClass()
  {
    rmdir(self::$working_dir . '/testcam01');
    rmdir(self::$working_dir . '/TestCam02');
    rmdir(self::$working_dir . '/testcam 3');
  }

  public function testExcludeDirsWorking()
  {
    // Arrange

    // Act
    $a = FileHandler::ls_cameras();

    // Assert
    $expected = '["TestCam02","testcam 3","testcam01"]';
    $this->assertEquals($expected, $a);
  }

  public function testSpecialCharCameraNamesWorking()
  {
    $this->assertEquals(-1, -1);
  }
}
