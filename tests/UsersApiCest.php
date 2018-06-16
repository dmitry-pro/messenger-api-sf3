<?php

class UsersApiCest
{
    public function tryListUsers(ApiTester $I)
    {

        $I->sendGET('/users');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/users');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $resp = json_decode($I->grabResponse());
        $I->assertCount(4, $resp);
    }

    public function tryTestMe(ApiTester $I)
    {
        $I->sendGET('/users/me');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/users/me');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $resp = json_decode($I->grabResponse(), true);
        $I->assertArraySubset([
            'id' => 1,
            'username' => 'panda',
        ], $resp);
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
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        // case sensitive
        $I->canSeeResponseContainsJSON(['text' => 'Hello, Zebra!']);
        $I->canSeeResponseContainsJSON(['username' => 'zebra']);

        $I->canSeeResponseJsonMatchesJsonPath('$.author');
        $resp = json_decode($I->grabResponse(), true);
        $I->assertArraySubset(        [
            'id' => 1,
            'username' => 'panda',
        ], $resp['author']);

        // todo: 201
    }
}
