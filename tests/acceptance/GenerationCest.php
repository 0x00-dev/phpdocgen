<?php

/**
 * Тест сгенерированной документации.
 */
class GenerationCest
{
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeInTitle('Тестовая страница документации.');
        $I->see('GeneratorTestClass');
        $I->see('Phpdocgen');
        $I->see('Генератор статической документации на основании phpdoc');
        $I->see('GitHub');
        $I->see('Gmail');
        $I->canSeeLink('GeneratorTestClass');
        $I->click('GeneratorTestClass');
        $I->seeInTitle('GeneratorTestClass');
        $I->see('Пространство имен: TestComponent');
        $I->see('Тип: class');
        $I->see('Имя: GeneratorTestClass');
        $I->see('Описание: Тестовый класс для генерации документации.');
        /**
         * Search fields.
         */
        $I->see('Поля:');
        /**
         * Search #private_field
         */
        $I->seeElement('#private_field');
        $I->see('Закрытое поле.', '#private_field .about');
        $I->see('private', '#private_field .visibility');
        $I->see('private_field', '#private_field .name');
        $I->see('string', '#private_field .type');
        /**
         * Search #public_field
         */
        $I->seeElement('#public_field');
        $I->see('Открытое поле.', '#public_field .about');
        $I->see('public', '#public_field .visibility');
        $I->see('public_field', '#public_field .name');
        $I->see('int', '#public_field .type');
        /**
         * Search #protected_field
         */
        $I->seeElement('#protected_field');
        $I->see('Защищенное поле.', '#protected_field .about');
        $I->see('protected', '#protected_field .visibility');
        $I->see('protected_field', '#protected_field .name');
        $I->see('\Countable', '#protected_field .type');
        /**
         * Search methods.
         */
        $I->see('Методы:');
        /**
         * Search getPrivateField()
         */
        $I->seeElement('#method_getPrivateField');
        $I->seeLink('GeneratorTestClassInterface::getPrivateField', '/TestComponent/Interfaces/GeneratorTestClassInterface.html#method_getPrivateField');
        $I->click('GeneratorTestClassInterface::getPrivateField');
        $I->seeInTitle('GeneratorTestClassInterface');
        $I->seeElement('#method_getPrivateField');
        $I->see('Получить закрытое поле.', '#method_getPrivateField .about');
        $I->amOnPage('/TestComponent/GeneratorTestClass.html');
        $I->see('public', '#method_getPrivateField .visibility');
        $I->see('Возвращаемый тип не указан.', '#method_getPrivateField .type');
        /**
         * Search getPublicField()
         */
        $I->seeElement('#method_getPublicField');
        $I->see('Получить открытое поле.', '#method_getPublicField .about');
        $I->see('public', '#method_getPublicField .visibility');
        $I->see('int', '#method_getPublicField .type');
        /**
         * Search getProtectedField()
         */
        $I->seeElement('#method_getProtectedField');
        $I->see('Получить защищенное поле.', '#method_getProtectedField .about');
        $I->see('public', '#method_getProtectedField .visibility');
        $I->see('\Countable', '#method_getProtectedField .type');
        /**
         * Search constants.
         */
        $I->see('Константы:');
        /**
         * Search TEST_PRIVATE_CONST
         */
        $I->seeElement('#TEST_PRIVATE_CONST');
        $I->see('Тестовая закрытая константа.', '#TEST_PRIVATE_CONST .about');
        $I->see('bool', '#TEST_PRIVATE_CONST .type');
        $I->see('false', '#TEST_PRIVATE_CONST .value');
        $I->see('private', '#TEST_PRIVATE_CONST .badge');
        /**
         * Search TEST_PUBLIC_CONST
         */
        $I->seeElement('#TEST_PUBLIC_CONST');
        $I->see('Тестовая открытая константа.', '#TEST_PUBLIC_CONST .about');
        $I->see('string', '#TEST_PUBLIC_CONST .type');
        $I->see('Тестовая константа.', '#TEST_PUBLIC_CONST .value');
        $I->see('public', '#TEST_PUBLIC_CONST .badge');
    }
}
