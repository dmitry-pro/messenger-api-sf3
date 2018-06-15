<?php
class RootApiCest
{

    public function testApiRootIsEmpty(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

}