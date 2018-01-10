<?php

use Codeception\Util\HttpCode;
use AppBundle\Entity\User;
use Step\LoginStep;

class RegistrationCest
{
    public function canRegisterWithValidData(LoginStep $I)
    {
        $newUserName = 'newUser';
        $newUserEmail = 'newuser@example.com';
        $newUserPassword = 'password';
        $I->sendPOST('/register', ['username' => $newUserName,
            'plainPassword' => ['first' => $newUserPassword, 'second' => $newUserPassword],
            'email' => $newUserEmail]);
        $I->seeResponseCodeIs(HttpCode::CREATED);

        $I->imLogged($newUserName, $newUserPassword);
        $user = $I->grabEntityFromRepository(User::class, ['username' => $newUserName]);

        $I->sendGET('/profile/' . $user->getId());
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'username' => $newUserName,
                'email' => $newUserEmail,
            ]
        );
    }

    public function canRegisterWithValidDataAndReturnedToken(ApiTester $I)
    {
        $newUserName = 'newUser';
        $newUserEmail = 'newuser@example.com';
        $newUserPassword = 'password';
        $I->sendPOST('/register', ['username' => $newUserName,
            'plainPassword' => ['first' => $newUserPassword, 'second' => $newUserPassword],
            'email' => $newUserEmail]);
        $I->seeResponseCodeIs(HttpCode::CREATED);

        $profileUrl = $I->grabHttpHeader('Location');
        $token = $I->grabDataFromResponseByJsonPath('token')[0];
        $I->amBearerAuthenticated($token);
        $urlExploded = explode('/', $profileUrl);
        $userId = end($urlExploded);

        $I->sendGET('/profile/' . $userId);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(
            [
                'username' => $newUserName,
                'email' => $newUserEmail,
            ]
        );
    }

    public function cantRegisterWithExistingUserName(ApiTester $I) {
        $userName = 'newUser';
        $userEmail = 'newuser@example.com';
        $userPassword = 'password';
        $I->haveInRepository(User::class, ['username' => $userName, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($userPassword), 'enabled' => 1, 'email' => $userEmail]);
        $I->sendPOST('/register', ['username' => $userName,
            'plainPassword' => ['first' => 'test', 'second' => 'test'],
            'email' => 'some@email.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['The username is already used.']);
    }

    public function cantRegisterWithExistingEmail(ApiTester $I) {
        $userName = 'newUser';
        $userEmail = 'newuser@example.com';
        $userPassword = 'password';
        $I->haveInRepository(User::class, ['username' => $userName, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($userPassword), 'enabled' => 1, 'email' => $userEmail]);
        $I->sendPOST('/register', ['username' => 'username',
            'plainPassword' => ['first' => 'test', 'second' => 'test'],
            'email' => $userEmail]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['The email is already used.']);
    }

    public function cantRegisterWithMismatchedPassword(ApiTester $I) {
        $I->sendPOST('/register', ['username' => 'username',
            'plainPassword' => ['first' => 'password', 'second' => 'otherPassword'],
            'email' => 'newuser@example.com']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['The entered passwords don\'t match.']);
    }

    private function encodePasswordByBCryptSymfonyAlgorithm($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]);
    }
}