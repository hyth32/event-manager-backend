<?php

namespace backend\models;
use yii\db\ActiveRecord;

class Event extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%events}}';
    }

    public function rules(): array
    {
        return [
            [['userId'], 'required'],
            [['userId', 'startTime', 'endTime', 'createdAt', 'updatedAt'], 'integer'],
            [['name', 'content'], 'string'],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->updatedAt = time();
            if ($this->isNewRecord) {
                $this->createdAt = time();
            }
            return true;
        }
        return false;
    }
}