<?php
namespace TestComponent\Interfaces;

/**
 * Тестовый класс для генерации документации.
 */
interface GeneratorTestClassInterface
{
    /**
     * Получить закрытое поле.
     *
     * @return string
     */
    public function getPrivateField(): string;
}