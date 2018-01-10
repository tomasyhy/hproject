<?php

use Codeception\Util\HttpCode;
use AppBundle\Entity\User;
use Step\LoginStep;

class PasswordManagementCest
{
    private $username = 'username';
    private $password = 'test';
    private $email = 'test@example.com';
    private $confirmationToken = 'confirmation-token';

    private $username2 = 'username2';
    private $password2 = 'test2';
    private $email2 = 'test2@example.com';
    private $confirmationToken2 = 'confirmation-token2';

    public function changePassword(LoginStep $I)
    {
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $userId, ['current_password' => $this->password, 'plainPassword' => ['first' => 'newPassword', 'second' => 'newPassword']]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function cantChangePasswordWithoutToken(ApiTester $I)
    {
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendPOST('/password/change/' . $userId, ['current_password' => $this->password, 'plainPassword' => ['first' => 'newPassword', 'second' => 'newPassword']]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }

    public function cantChangePasswordWithWrongCurrentPassword(LoginStep $I)
    {
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $userId, ['current_password' => 'wrongPassword', 'plainPassword' => ['first' => 'newPassword', 'second' => 'newPassword']]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantChangePasswordWithMismatchedPassword(LoginStep $I)
    {
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $userId, ['current_password' => $this->password, 'plainPassword' => ['first' => 'newPassword', 'second' => 'newOtherPassword']]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantChangeOtherUserPassword(LoginStep $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $user2Id = $I->haveInRepository(User::class, ['username' => $this->username2, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password2), 'enabled' => 1, 'email' => $this->email2, 'confirmationToken' => $this->confirmationToken2]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $user2Id, ['current_password' => $this->password, 'plainPassword' => ['first' => 'newPassword', 'second' => 'newPassword']]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
        $I->seeResponseIsJson();
    }

    public function changePasswordAndLoginWithNewPassword(LoginStep $I)
    {
        $newPassword = 'newPassword';
        $user1Id = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);

        $I->sendPOST('/password/change/' . $user1Id, ['current_password' => $this->password, 'plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $newPassword]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function changePasswordAndViewProfile(LoginStep $I)
    {
        $newPassword = 'newPassword';
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $userId, ['current_password' => $this->password, 'plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->imLogged($this->username, $newPassword);
        $I->sendGET('/profile/' . $userId);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson(
            [
                'username' => $this->username,
                'email' => $this->email,
            ]
        );
    }

    public function changePasswordAndCantLoginWithOldPassword(LoginStep $I)
    {
        $newPassword = 'newPassword';
        $userId = $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->imLogged($this->username, $this->password);
        $I->sendPOST('/password/change/' . $userId, ['current_password' => $this->password, 'plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->sendPOST('/login', ['username' => $this->username, 'password' => $this->password]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }

    public function requestForResetPassword(ApiTester $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function canConfirmResetPassword(LoginStep $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendPOST('/password/reset/confirm', ['token' => $this->confirmationToken, 'plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->imLogged($this->username, $newPassword);
    }

    public function cantConfirmResetPasswordAfterResetActionAndWithoutConfirmation(ApiTester $I)
    {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    public function cantConfirmResetPasswordWithoutToken(ApiTester $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendPOST('/password/reset/confirm', ['plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantConfirmResetPasswordWithWrongToken(ApiTester $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/password/reset/confirm', ['token' => 'wrong-token', 'plainPassword' => ['first' => $newPassword, 'second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantConfirmResetPasswordWithMismatchedPasswords(ApiTester $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/password/reset/confirm', ['token' => 'wrong-token', 'plainPassword' => ['first' => $newPassword, 'second' => 'otherPassword']]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantConfirmResetPasswordWithoutFirstPassword(ApiTester $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/password/reset/confirm', ['token' => 'wrong-token', 'plainPassword' => ['second' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantConfirmResetPasswordWithoutSecondPassword(ApiTester $I)
    {
        $newPassword = 'newPassword';
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => $this->username]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->sendPOST('/password/reset/confirm', ['token' => 'wrong-token', 'plainPassword' => ['first' => $newPassword]]);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();
    }

    public function cantRequestPasswordResetForInvalidUser(ApiTester $I) {
        $I->haveInRepository(User::class, ['username' => $this->username, 'password' => $this->encodePasswordByBCryptSymfonyAlgorithm($this->password), 'enabled' => 1, 'email' => $this->email, 'confirmationToken' => $this->confirmationToken]);
        $I->sendPOST('/password/reset/request', ['username' => 'notRecognisedUser']);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    private function encodePasswordByBCryptSymfonyAlgorithm($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]);
    }
}