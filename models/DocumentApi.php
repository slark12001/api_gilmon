<?php

namespace app\models;

use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Request;

class DocumentApi
{
    /**
     * @var Document
     */
    private Document $document;

    /**
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getData(): array
    {
        return [
            'id' => $this->document->id,
            'status' => Document::STATUSES[$this->document->status],
            'payload' => Json::decode($this->document->payload, false),
            'createAt' => (new \DateTime($this->document->createAt))->format('c'),
            'modifyAt' => (new \DateTime($this->document->modifyAt))->format('c'),
        ];
    }

    /**
     * @param array $document
     * @return bool
     * @throws BadRequestHttpException
     */
    public function edit(array $document): bool
    {
        if (isset($document['payload']) === false
            || $this->updatePayload($document['payload']) === false
        ) {
            throw new BadRequestHttpException();
        }
        return $this->document->save();
    }

    /**
     * @return bool
     */
    public function publish(): bool
    {
        $this->document->status = Document::STATUS_PUBLISH;
        return $this->document->save();
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $this->document->status = Document::STATUS_DRAFT;
        $this->document->payload = '{}';
        return $this->document->save();
    }

    /**
     * @param array $payload
     * @return bool
     */
    private function updatePayload(array $payload): bool
    {
        if ($this->document->status === Document::STATUS_PUBLISH) {
            return false;
        }
        $newPayload = Json::decode($this->document->payload, false);
        if ($newPayload === []) {
            $newPayload = new \stdClass();
        }
        foreach ($payload as $key => $value) {
            $newPayload->$key = array_filter((array)$value, function ($value) {
                return $value !== null;
            });
        }
        $this->document->payload = Json::encode($newPayload);
        return true;
    }
}