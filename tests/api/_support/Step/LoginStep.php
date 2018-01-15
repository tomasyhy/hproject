<?php
namespace Step;

use Codeception\Util\HttpCode;

class LoginStep extends \ApiTester
{
    public function imLogged($userName, $password) {
        $I = $this;
        $I->sendPOST('/login', ['username' => $userName, 'password' => $password]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $token = $I->grabDataFromResponseByJsonPath('token')[0];

        $I->amBearerAuthenticated($token);
    }
}
