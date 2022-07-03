<?php

namespace app\modules\v1\controllers;

use app\models\Document;
use app\models\DocumentApi;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class DocumentController extends \yii\rest\Controller
{
    /** @inheritdoc  */
    protected function verbs(): array
    {
        return [
            'index' => ['GET'],
            'create' => ['POST'],
            'view' => ['GET'],
            'update' => ['PATCH'],
            'publish' => ['POST']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionIndex(int $page = 0, int $perPage = 20): \yii\web\Response
    {
        $query = Document::find();

        $pagination = new Pagination([
            'page' => $page - 1,
            'pageSize' => $perPage,
            'totalCount' => $query->count()
        ]);

        if ($page > $pagination->getPageCount()) {
            throw new NotFoundHttpException();
        }

        $docs = $query->offset($pagination->getOffset())
            ->orderBy(['createAt' => SORT_DESC])
            ->limit($pagination->getLimit())
            ->all();

        $returnDocs = [];
        foreach ($docs as $doc) {
            $returnDocs[] = (new DocumentApi($doc))->getData();
        }

        return $this->asJson([
            'document' => $returnDocs,
            'pagination' => [
                'page' => $pagination->getPage() + 1,
                'perPage' => $pagination->getPageSize(),
                'total' => $pagination->totalCount
            ]
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionCreate(): \yii\web\Response
    {
        $doc = new Document();
        $documentApi = new DocumentApi($doc);
        $documentApi->create();

        return $this->asJson($documentApi->getData());

    }

    /**
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionView($id): \yii\web\Response
    {
        $doc = $this->findModel($id);
        $documentApi = new DocumentApi($doc);
        return $this->asJson($documentApi->getData());
    }

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws \Exception
     */
    public function actionUpdate($id): \yii\web\Response
    {
        $doc = $this->findModel($id);

        $documentApi = new DocumentApi($doc);
        $documentData = $this->request->post('document');
        if ($documentData === null) {
            throw new BadRequestHttpException();
        }
        $documentApi->edit($documentData);

        return $this->asJson($documentApi->getData());
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionPublish($id): \yii\web\Response
    {
        $doc = $this->findModel($id);

        $documentApi = new DocumentApi($doc);
        $documentApi->publish();

        return $this->asJson($documentApi->getData());
    }

    /**
     * @param $id
     * @return Document
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Document
    {
        $doc = Document::findOne($id);
        if ($doc === null) {
            throw new NotFoundHttpException();
        }
        return $doc;
    }

}