<?php

use Codeception\Util\HttpCode;
use AppBundle\Entity\User;

class UserProfileCest
{
    private $username = 'username';
    private $password = 'test';
    private $email = 'test@example.com';
    private $username2 = 'username2';
    private $password2 = 'test2';
    private $email2 = 'test2@example.com';


    public function _before(ApiTester $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email]);
        $I->haveInRepository(User::class, ['username' => $this->username2, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password2), 'enabled' => 1, 'email' => $this->email2]);
    }

    public function viewUserProfile(ApiTester $I)
    {
        $user = $I->grabEntityFromRepository(User::class, ['username' => $this->username]);

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
        $I->sendGET('/profile/' . $user->getId());
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => $this->username,
                'email' => $this->email,
            ]
        );
    }

    public function viewUserProfileWithoutToken(ApiTester $I)
    {
        $user = $I->grabEntityFromRepository(User::class, ['username' => $this->username]);

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendGET('/profile/' . $user->getId());
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function viewOtherUserProfile(ApiTester $I)
    {
        $user2 = $I->grabEntityFromRepository(User::class, ['username' => $this->username2]);


        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
        $I->sendGET('/profile/' . $user2->getId());
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function updateUserProfile(ApiTester $I)
    {
        $user = $I->grabEntityFromRepository(User::class, ['username' => $this->username]);

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
        $I->sendPUT('/profile/' . $user->getId(), ['username' => 'updatedUsername', 'current_password' => $this->password, 'email' => 'updatedEmail@example.com']);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }

    public function updateUserProfileWithoutCurrentPassword(ApiTester $I)
    {
        $user = $I->grabEntityFromRepository(User::class, ['username' => $this->username]);

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
        $I->sendPUT('/profile/' . $user->getId(), ['username' => 'updatedUsername', 'email' => $this->email]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function updateOtherUserProfile(ApiTester $I)
    {
        $user2 = $I->grabEntityFromRepository(User::class, ['username' => $this->username2]);

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
        $I->sendPUT('/profile/' . $user2->getId(), ['username' => 'updatedUsername', 'current_password' => $this->password, 'email' => 'updatedEmail@example.com']);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    private function encodePasswordByBCryptSymfonyAlgorithm($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
    }
}