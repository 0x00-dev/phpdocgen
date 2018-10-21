<?php
namespace TestComponent;

use TestComponent\Interfaces\GeneratorTestClassInterface;

/**
 * Тестовый класс для генерации документации.
 */
class GeneratorTestClass implements GeneratorTestClassInterface
{
    /**
     * Закрытое поле.
     *
     * @var string
     */
    private $private_field;

    /**
     * Открытое поле.
     *
     * @var int
     */
    public $public_field = 1;

    /**
     * Защищенное поле.
     *
     * @var \Countable
     */
    protected $protected_field;

    /**
     * {@inheritdoc}
     */
    public function getPrivateField(): string
    {
        return $this->private_field;
    }

    /**
     * Получить открытое поле.
     *
     * @return int
     */
    public function getPublicField(): int
    {
        return $this->public_field;
    }

    /**
     * Получить защищенное поле.
     *
     * @return \Countable
     */
    public function getProtectedField(): \Countable
    {
        return $this->protected_field;
    }
}