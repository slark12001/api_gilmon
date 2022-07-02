<?php

namespace app\modules\v1\controllers;

use app\models\Document;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class DocumentController extends \yii\rest\Controller
{

    protected function verbs()
    {
        return [
            'index' => ['POST'],
            'view' => ['GET'],
            'update' => ['PATCH'],

        ];
    }

    public function actionIndex(): \yii\web\Response
    {
        $doc = new Document();
        $doc->save();

        return $this->asJson($doc->returnForApi());

    }

    public function actionView($id): \yii\web\Response
    {
        $doc = Document::findOne($id);
        if ($doc === null) {
            throw new NotFoundHttpException();
        }

        return $this->asJson($doc->returnForApi());
    }

    public function actionUpdate($id): \yii\web\Response
    {
        $doc = Document::findOne($id);
        if ($doc === null) {
            throw new NotFoundHttpException();
        }

        $documentFields = $this->request->post('document');

        if (
            $documentFields === null
            || isset($documentFields['payload']) === false
            || $doc->updateDoc($documentFields['payload']) === false
        ) {
            throw new BadRequestHttpException();
        }

        return $this->asJson($doc->returnforApi());
    }

}