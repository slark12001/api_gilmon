<?php

class CreateDocumentCest
{
    public function _before(ApiTester $I)
    {
    }

    // tests
    public function createDocument(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');
        $I->sendGet('/');
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
    }
}
