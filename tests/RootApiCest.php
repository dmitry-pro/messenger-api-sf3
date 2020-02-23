<?php

class RootApiCest
{
    public function testApiRootIsEmpty(ApiTester $I)
    {
        $I->sendGET('/');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function testRandomApiResourseReturns404(ApiTester $I)
    {
        $I->sendGET('/i/am/random/URI');
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }
}
