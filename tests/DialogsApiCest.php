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

    public function tryCreateDialog(ApiTester $I)
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
    // todo: getDialog, deletedialog/dialogs, getDialogMessages, createDialogMessage.
}
