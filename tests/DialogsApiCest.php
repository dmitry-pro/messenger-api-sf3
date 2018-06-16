<?php

class DialogsApiCest
{
    public function tryListEmptyDialogs(ApiTester $I)
    {

        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->canSeeResponseEquals('[]');
    }

    public function tryCreateDialogAndCreateDialogMessage(ApiTester $I)
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

        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        // one dialog
        $resp = json_decode($I->grabResponse());
        $I->assertCount(1, $resp);

        // two users in dialog
        $I->canSeeResponseContainsJson([
            'username' => 'panda',
        ]);
        $I->canSeeResponseContainsJson([
            'username' => 'zebra',
        ]);

        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        // one message
        $resp = json_decode($I->grabResponse());
        $I->assertCount(1, $resp);

        // same dialog for same user pair
        $I->sendPOST('/messages/create', [
            'text' => 'Hello again, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);
        // authorize as "zebra" and reply to panda
        $I->haveHttpHeader('Authorization', 'Bearer abcde');
        $I->sendPOST('/messages/create', [
            'text' => 'Hi Panda, this is Zebra!',
            'username' => 'panda',
        ]);
        $I->seeResponseCodeIs(201);

        // now we have 3 messages
        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());
        $I->assertCount(3, $resp);
        // ... but still one dialog.
        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());
        $I->assertCount(1, $resp);
    }

    public function tryCreateAndGetDialogMessages(ApiTester $I)
    {
        // test security
        $I->sendGET('/dialogs/1/messages');
        $I->seeResponseCodeIs(401);

        $I->sendPOST('/dialogs/1/messages/create');
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        // create message via "messages" endpoint
        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        // create message via "dialogs/messages" endpoint
        $I->sendPOST('/dialogs/1/messages/create', [
            'text' => 'Hello again, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();

        // try get dialog
        $I->sendGET('/dialogs/1');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());

        // get two dialog messages
        $I->sendGET('/dialogs/1/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());
        $I->assertCount(2, $resp);
    }

    public function tryGetAndDeleteDialogs(ApiTester $I)
    {
        // test security
        $I->sendGET('/dialogs/2');
        $I->seeResponseCodeIs(401);

        $I->sendDELETE('/dialogs/2');
        $I->seeResponseCodeIs(401);

        $I->sendDELETE('/dialogs');
        $I->seeResponseCodeIs(401);

        $I->haveHttpHeader('Authorization', 'Bearer 12345');

        // create 3 dialogs w/ 2 messages each
        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/messages/create', [
            'text' => 'Hello again, Zebra!',
            'username' => 'zebra',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Tiger!',
            'username' => 'tiger',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/messages/create', [
            'text' => 'Hello again, Tiger!',
            'username' => 'tiger',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/messages/create', [
            'text' => 'Hello, Lion!',
            'username' => 'lion',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendPOST('/messages/create', [
            'text' => 'Hello again, Lion!',
            'username' => 'lion',
        ]);
        $I->seeResponseCodeIs(201);

        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());
        $I->assertCount(3, $resp);

        // try get dialog
        $I->sendGET('/dialogs/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $resp = json_decode($I->grabResponse());
        $I->assertCount(2, $resp->users);
        $I->canSeeResponseContains('panda');
        $I->canSeeResponseContains('tiger');

        // try delete dialog
        $I->sendDELETE('/dialogs/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->sendGET('/dialogs/2');
        $I->seeResponseCodeIs(404);
        // idempotency test
        $I->sendDELETE('/dialogs/2');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        // try delete all dialogs
        $I->sendDELETE('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->sendGET('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('[]');
        // test cascade messages removing
        $I->sendGET('/messages');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('[]');
        // idempotency test
        $I->sendDELETE('/dialogs');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
