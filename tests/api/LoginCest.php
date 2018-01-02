<?php

use Codeception\Util\HttpCode;
use AppBundle\Entity\User;

class LoginCest
{    
    public function loginWithCorrectPasswordByUsername(ApiTester $I)
    {
        $password = 'test';
        $symfonyBCryptPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

        $I->haveInRepository(User::class, ['username' => 'test', 'password' => $symfonyBCryptPassword, 'enabled' => 1, 'email' => 'test@example.com']);
        $I->sendPOST('/login', ["username" => "test", "password" => $password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'token' => 'string',
            ]
        );
    }

    public function loginWithCorrectPasswordByEmail(ApiTester $I)
    {
        $password = 'test';
        $username = 'test@example.com';
        $symfonyBCryptPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

        $I->haveInRepository(User::class, ['username' => 'test', 'password' => $symfonyBCryptPassword, 'enabled' => 1, 'email' => $username]);
        $I->sendPOST('/login', ['username' => $username, "password" => $password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType(
            [
                'token' => 'string',
            ]
        );
    }

    public function loginWithWrongPassword(ApiTester $I)
    {
        $password = 'test';
        $symfonyBCryptPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

        $I->haveInRepository(User::class, ['username' => 'test', 'password' => $symfonyBCryptPassword, 'enabled' => 1, 'email' => 'test@example.com']);
        $I->sendPOST('/login', ["username" => "test", "password" => 'wrongpassword']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(
            [
                'code' => HttpCode::UNAUTHORIZED,
                'message' => 'Bad credentials',
            ]
        );
    }

    public function loginWithWrongUsername(ApiTester $I)
    {
        $password = 'test';
        $username = 'username';
        $symfonyBCryptPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);

        $I->haveInRepository(User::class, ['username' => $username, 'password' => $symfonyBCryptPassword, 'enabled' => 1, 'email' => 'test@example.com']);
        $I->sendPOST('/login', ["username" => 'wrongUsername', "password" => $symfonyBCryptPassword]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
        $I->seeResponseIsJson(
            [
                'code' => HttpCode::UNAUTHORIZED,
                'message' => 'Bad credentials',
            ]
        );
    }
}