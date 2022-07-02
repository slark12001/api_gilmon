<?php

namespace app\models;

use Horat1us\Yii\UuidBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * @property string $id
 * @property string $status
 * @property string $payload
 * @property $createAt
 * @property $modifyAt
 */
class Document extends \yii\db\ActiveRecord
{
    public const STATUS_DRAFT = 1;
    public const STATUS_PUBLISH = 2;
    public const STATUSES = [
        self::STATUS_DRAFT => 'draft',
        self::STATUS_PUBLISH => 'publish'
    ];

    public static function tableName()
    {
        return '{{%document}}';
    }

    public function behaviors()
    {
        return [
            'id' => [
                'class' => UuidBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => 'id'
                ]
            ],
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['createAt', 'modifyAt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['modifyAt'],
                ],
                'value' => (new Query())->select(new Expression('CURRENT_TIMESTAMP()'))->scalar()
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['id', 'safe'],
            [['id', 'payload'], 'string'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISH]],
            ['payload', 'default', 'value' => "{}"],
            [['createAt', 'modifyAt'], 'safe'],
        ];
    }

    public function returnforApi(): array
    {
        return [
            'id' => $this->id,
            'status' => self::STATUSES[$this->status],
            'payload' => json_decode($this->payload),
            'createAt' => (new \DateTime($this->createAt))->format('c'),
            'modifyAt' => (new \DateTime($this->modifyAt))->format('c'),
        ];
    }

    public function updateDoc(array $payload): bool
    {
        $newPayload = json_decode($this->payload);
        foreach ($payload as $key => $value) {
            $newPayload->$key = array_filter($value, function ($value) {
                return $value !== null;
            });
        }
        $newPayload = json_encode($newPayload);
        $this->payload = $newPayload;
        return $this->save();
    }
}