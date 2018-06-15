<?php


class UsersApiCest
{
    // todo: setUp, tearDown
    public function tryListUsers(ApiTester $I)
    {

        $I->sendGET('/users');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        // todo: how many.
    }

    public function tryGetUser(ApiTester $I)
    {

        $I->sendGET('/users/4');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/users/4');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('tiger');
    }

    public function tryGetUserDialogs(ApiTester $I)
    {
        $I->sendGET('/users/1/dialogs');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/users/1/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseEquals('[]');

        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        // case sensitive
        $I->canSeeResponseContains('Hello, Zebra!');
        $I->canSeeResponseContains('zebra');
        // sender
        $I->canSeeResponseContains('panda');

        // todo: getUserDialogs
        // todo: 201
        // todo: explicit json fields check
    }
}
