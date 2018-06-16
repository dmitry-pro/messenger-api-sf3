<?php

class MessagesApiCest
{
    public function tryListEmptyMessages(ApiTester $I)
    {

        $I->sendGET('/messages');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseEquals('[]');
    }

    public function tryCreateMessage(ApiTester $I)
    {
        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Zebra!',
            'username' => 'zebra',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        // case sensitive
        $I->canSeeResponseContains('Hello, Zebra!');
        $I->canSeeResponseContains('zebra');
        // sender
        $I->canSeeResponseContains('panda');

        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseContainsJson([
            [
                'author' => [
                    'username' => 'panda',
                ],
            ]
        ]);
    }

    public function tryCreateInvalidDialog(ApiTester $I)
    {
        // send message to the same user
        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Me!',
            'username' => 'panda',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Me!',
            'username' => 'panda',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();

        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        // not added to database
        $I->canSeeResponseEquals('[]');
    }
}
