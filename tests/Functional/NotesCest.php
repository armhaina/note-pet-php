<?php

declare(strict_types=1);


namespace App\Tests\Functional;

use App\DataFixtures\UserAuthorizedFixtures;
use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\DataProvider;
use Codeception\Example;
use Codeception\Util\HttpCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class NotesCest extends AbstractCest
{
    public function _before(FunctionalTester $I): void
    {
    }

    /**
     * @param FunctionalTester $I
     * @param Example $example
     */
    #[DataProvider('successProvider')]
    public function tryToTest(FunctionalTester $I, Example $example): void
    {
        $this->loadFixtures(I: $I, groups: $example['groups']);

        $I->sendGet(url: '/api/v1/notes');
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeResponseIsJson();
//        $I->seeResponseContainsJson(json: $example['response']);

        $data = json_decode($I->grabResponse(), true);

        $test = self::except(data: $data, excludeKeys: ['id']);

        $I->assertEquals(expected: $example['response'], actual: $data);
    }

    protected function successProvider(): array
    {
        return [
            [
                'groups' => ['note-list'],
                'response' => [
                    'ok' => true,
                    'result' => [
                        'from' => [
                            'id' => 8201982463,
                            'is_bot' => true,
                            'first_name' => 'XarmTest',
                            'username' => 'xarmtestbot'
                        ],
                        'chat' => [
                            'id' => 528480542,
                            'first_name' => 'Георгий',
                            'last_name' => 'Либиков',
                            'username' => 'armhaina',
                            'type' => 'private'
                        ],
                        'text' => "🤖 Привет, я бот сервиса BrainVPN.\n\nЯ помогу тебе обезопасить твои устройства и защитить данные в сети используя лучшие алгоритмы шифрования.\n\nС помощью BrainVPN вы можете:\n\n• Безопасно изменять свой IP адрес.\n• Конфиденциально просматривать различные веб-ресурсы.\n• Безопасно получать доступ к интернету через публичные и незащищенные сети Wi-Fi.\n\nID: 528480542\nПодписка: отключена\n\nВыберите действие:",
                        'entities' => [
                            [
                                'offset' => 25,
                                'length' => 9,
                                'type' => 'bold'
                            ],
                            [
                                'offset' => 153,
                                'length' => 8,
                                'type' => 'bold'
                            ],
                            [
                                'offset' => 353,
                                'length' => 9,
                                'type' => 'bold'
                            ],
                            [
                                'offset' => 373,
                                'length' => 9,
                                'type' => 'bold'
                            ]
                        ],
                        'reply_markup' => [
                            'inline_keyboard' => [
                                [
                                    [
                                        'text' => 'Подписка',
                                        'callback_data' => '/subscription'
                                    ],
                                    [
                                        'text' => 'Устройства',
                                        'callback_data' => '/devices'
                                    ]
                                ],
                                [
                                    [
                                        'text' => 'Сервера',
                                        'callback_data' => '/servers'
                                    ],
                                    [
                                        'text' => 'Цены',
                                        'callback_data' => '/prices'
                                    ]
                                ],
                                [
                                    [
                                        'text' => 'Инструкция',
                                        'callback_data' => '/manual'
                                    ],
                                    [
                                        'text' => 'F.A.Q.',
                                        'callback_data' => '/faq'
                                    ]
                                ],
                                [
                                    [
                                        'text' => 'Акции',
                                        'callback_data' => '/promotions'
                                    ],
                                    [
                                        'text' => 'Тех. поддержка',
                                        'callback_data' => '/support'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
