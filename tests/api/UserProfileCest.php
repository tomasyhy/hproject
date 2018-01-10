<?php

use Codeception\Util\HttpCode;
use AppBundle\Entity\User;
use Step\LoginStep;

class UserProfileCest
{
    private $username = 'username';
    private $password = 'test';
    private $email = 'test@example.com';
    private $confirmationToken = 'confirmation-token';

    private $username2 = 'username2';
    private $password2 = 'test2';
    private $email2 = 'test2@example.com';
    private $confirmationToken2 = 'confirmation-token2';

    public function viewUserProfile(LoginStep $I)
    {
        $user1Id = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendGET('/profile/' . $user1Id);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => $this->username,
                'email' => $this->email,
            ]
        );
    }

    public function cantViewUserProfileWithoutToken(ApiTester $I)
    {
        $user1Id = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendGET('/profile/' . $user1Id);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function cantViewOtherUserProfile(LoginStep $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $user2Id = $I->haveInRepository(User::class, ['username' => $this->username2, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password2), 'enabled' => 1, 'email' => $this->email2, 'confirmationToken' => $this->confirmationToken2]);
        $I->sendGET('/profile/' . $user2Id);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function updateUserProfile(LoginStep $I)
    {
        $user1Id = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPUT('/profile/' . $user1Id, ['username' => 'updatedUsername', 'current_password' => $this->password, 'email' => 'updatedEmail@example.com']);
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }

    public function updateUserProfileWithoutCurrentPassword(LoginStep $I)
    {
        $user1Id = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPUT('/profile/' . $user1Id, ['username' => 'updatedUsername', 'email' => $this->email]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function updateOtherUserProfile(LoginStep $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $user2Id = $I->haveInRepository(User::class, ['username' => $this->username2, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password2), 'enabled' => 1, 'email' => $this->email2, 'confirmationToken' => $this->confirmationToken2]);
        $I->imLogged($this->username, $this->password);
        $I->sendPUT('/profile/' . $user2Id, ['username' => 'updatedUsername', 'current_password' => $this->password, 'email' => 'updatedEmail@example.com']);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    private function encodePasswordByBCryptSymfonyAlgorithm($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
    }
}