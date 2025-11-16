<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;

final class NotesCest extends AbstractCest
{
    public function _before(FunctionalTester $I): void {}

    #[DataProvider('successProvider')]
    public function tryToTest(FunctionalTester $I, Example $example): void
    {
        $this->loadFixtures(I: $I, groups: $example['groups']);

        $I->sendGet(url: '/api/v1/notes');
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();

        $data = json_decode($I->grabResponse(), true);
        $data = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function successProvider(): array
    {
        return [
            [
                'groups' => ['note-list'],
                'response' => [
                    [
                        'name' => 'Заметка_0',
                        'description' => 'Описание заметки_0',
                        'isPrivate' => false,
                        'user' => ['email' => 'test_0@mail.ru'],
                    ],
                    [
                        'name' => 'Заметка_1',
                        'description' => 'Описание заметки_1',
                        'isPrivate' => false,
                        'user' => ['email' => 'test_1@mail.ru'],
                    ],
                ],
            ],
        ];
    }
}
