<?php

class FileUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testIsAbsoluteProvider
     */
    function testIsAbsolute($path, $isAbsolute)
    {
        $this->assertEquals($isAbsolute, FileUtils::isAbsolute($path));
    }

    function testIsAbsoluteProvider()
    {
        return array(
            array("foo/bar", false),
            array("C:\\Windows", true),
            array("C:\\\\Windows", true),
            array("/etc", true),
        );
    }

    function testWithCwd()
    {
        $testCwd = getcwd();
        $dir = realpath('/tmp');

        $cwd = FileUtils::chdir($dir, function() {
            return getcwd();
        });

        $this->assertEquals($testCwd, getcwd(), "Does not influence the CWD outside of the callback");
        $this->assertEquals($dir, $cwd);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    function testWithCwdThrowsExceptionWhenDirectoryDoesNotExist()
    {
        FileUtils::chdir('/foo', function() {});
    }

    /**
     * @dataProvider testRelativizeProvider
     */
    function testRelativize($expected, $absolute)
    {
        $this->assertEquals($expected, FileUtils::relativize($absolute, __DIR__));
    }

    function testRelativizeProvider()
    {
        return array(
            array('bootstrap.php', realpath(__DIR__.'/bootstrap.php')),
        );
    }
}
