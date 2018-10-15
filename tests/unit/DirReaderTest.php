<?php
/**
 * Created by PhpStorm.
 * User: qwerty
 * Date: 9/1/18
 * Time: 8:03 PM
 */

use PDG\Component\DirReader;

class DirReaderTest extends \Codeception\Test\Unit
{
    /**
     * @var DirReader
     */
    private $object;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->object = new DirReader();
        $this->object->setDir(__DIR__ . '/../_data/files');
        parent::_before();
    }

    /**
     * Check for files find successfully.
     */
    public function testGetAllFiles()
    {
        /** Clear file pattern **/
        $this->object->setFilePattern('')->do();
        $dir = $this->object->getDir();
        $this->assertEquals([
            "$dir/TestClassTpl.php",
            "$dir/test1.php",
            "$dir/test2.php",
            "$dir/test3.json",
        ], $this->object->getFiles());
    }

    /**
     * Check for files find successfully.
     */
    public function testGetPhpOnly()
    {
        $this->object->do();
        $dir = $this->object->getDir();
        $this->assertEquals([
            "$dir/TestClassTpl.php",
            "$dir/test1.php",
            "$dir/test2.php",
        ], $this->object->getFiles());
    }
}
