<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\User;

class AccessToken extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%accessTokens}}';
    }

    public function rules()
    {
        return [
            [['userId', 'token', 'expiresAt'], 'required'],
            [['userId', 'expiresAt'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    public static function generateAccessToken(int $userId): ?AccessToken
    {
        AccessToken::deleteAll(['userId' => $userId]);
        $accessToken = new AccessToken();
        $accessToken->userId = $userId;
        $accessToken->token = Yii::$app->security->generateRandomString();
        $accessToken->expiresAt = time() + 3600 * 24 * 2;

        return $accessToken->save() ? $accessToken : null;
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public static function isTokenValid(AccessToken $token): bool
    {
        return $token->expiresAt >= time();
    }

    public static function getUserFromToken(string $token): ?User
    {
        $tokenRecord = AccessToken::findOne(['token' => $token]);
        if ($tokenRecord && static::isTokenValid($tokenRecord)) {
            return $tokenRecord->user;
        }

        return null;
    }

    public static function getUserIdFromToken(string $token): ?int
    {
        $user = static::getUserFromToken($token);
        return $user ? $user->id : null;
    }
}