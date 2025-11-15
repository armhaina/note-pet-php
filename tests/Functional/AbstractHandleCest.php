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

abstract class AbstractHandleCest
{
    public function _before(FunctionalTester $I): void
    {
        $this->loadFixtures(I: $I, groups: ['user-authorized']);

        $I->haveHttpHeader(name: 'Content-Type', value: 'application/json');
        $I->sendPost(
            url: '/api/login_check',
            params: [
                'username' => UserAuthorizedFixtures::EMAIL,
                'password' => UserAuthorizedFixtures::PASSWORD
            ]
        );
        $I->seeResponseCodeIs(code: 200);
        $I->seeResponseIsJson();

        $I->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . json_decode($I->grabResponse(), true)['token']);
    }

    protected function loadFixtures(FunctionalTester $I, array $groups): void
    {
        $I->runSymfonyConsoleCommand(
            command: 'doctrine:fixtures:load',
            parameters: [
                '--no-interaction' => '--no-interaction',
                '--purge-with-truncate' => true,
                '--group' => array_merge($groups),
                '--env' => 'test',
            ]
        );
    }
}
