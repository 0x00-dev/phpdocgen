<?php
namespace tests;

/**
 * This is the class description.
 */
class TestClassTpl
{
    /**
     * Some field.
     *
     * @var string
     */
    private $some_field;

    /**
     * And some field.
     *
     * @var TestClassTpl
     */
    public $over_field;

    /**
     * @return string
     */
    public function getSomeField(): string
    {
        return $this->some_field;
    }
}