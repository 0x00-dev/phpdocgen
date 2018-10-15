<?php
/**
 * Created by PhpStorm.
 * User: qwerty
 * Date: 9/1/18
 * Time: 8:22 PM
 */

use PDG\Component\DocReader;

class DocReaderTest extends \Codeception\Test\Unit
{
    /**
     * @var DocReader
     */
    private $object;

    /**
     * {@inheritdoc}
     */
    protected function _before()
    {
        $this->object = new DocReader();
        parent::_after();
    }

    /**
     * Test for file read successfully.
     */
    public function testRead()
    {
        $file = __DIR__ . '/../_data/files/TestClassTpl.php';
        $this->object->setClassFile($file);
        $data = file_get_contents($file);
        $this->assertEquals($data, $this->object->read());
    }

    /**
     * Test exception.
     */
    public function testReadThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $file = 'notExistFile.php';
        $this->object->setClassFile($file);
        $this->object->read();
    }
}
