<?php

namespace unit\models;

use yii\web\BadRequestHttpException;

class DocumentApiTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testCreateDocument()
    {
        $doc = new \app\models\Document();
        $documentApi = new \app\models\DocumentApi($doc);

        $this->assertTrue($documentApi->create());
    }

    public function testPublishDocument()
    {
        $doc = \app\models\Document::find()->one();
        $documentApi = new \app\models\DocumentApi($doc);

        $this->assertTrue($documentApi->publish());
    }

    public function testEditDocumentWithoutException()
    {
        $doc = \app\models\Document::find()->where(['status' => \app\models\Document::STATUS_DRAFT])->one();
        $documentApi = new \app\models\DocumentApi($doc);
        $res = $documentApi->edit(['payload' => [
            'meta' => [
                'age' => 10,
                'role' => 'hero'
            ],
            'name' => 'Peter Brown'
        ]]);
        $this->assertTrue($res);
    }

    public function testEditDocumentWithExceptionPublish()
    {
        $doc = \app\models\Document::find()->where(['status' => \app\models\Document::STATUS_PUBLISH])->one();
        $documentApi = new \app\models\DocumentApi($doc);
        $this->expectException(BadRequestHttpException::class);
        $documentApi->edit(['payload' => [
            'meta' => [
                'age' => 10,
                'role' => 'hero'
            ],
            'name' => 'Peter Brown'
        ]]);

    }

    public function testEditDocumentWithExceptionWithoutPayload()
    {
        $doc = \app\models\Document::find()->where(['status' => \app\models\Document::STATUS_PUBLISH])->one();
        $documentApi = new \app\models\DocumentApi($doc);
        $this->expectException(BadRequestHttpException::class);
        $documentApi->edit(['status' => 'test']);
    }

    public function testReturnDataForOutput() {
        $doc = \app\models\Document::find()->one();
        $documentApi = new \app\models\DocumentApi($doc);
        $this->assertArrayHasKey('id', $documentApi->getData());
        $this->assertArrayHasKey('status', $documentApi->getData());
        $this->assertEquals($documentApi->getData()['status'], 'draft');
        $this->assertArrayHasKey('payload', $documentApi->getData());
        $this->assertEquals($documentApi->getData()['payload'], (object)[]);
        $this->assertArrayHasKey('createAt', $documentApi->getData());
        $this->assertArrayHasKey('modifyAt', $documentApi->getData());
    }

}