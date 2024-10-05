<?php

namespace backend\controllers;

use backend\models\AccessToken;
use backend\models\ApiResponse;
use Yii;
use yii\base\Controller;
use common\models\User;
use yii\web\Response;

class UserController extends Controller
{
    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = json_decode(Yii::$app->request->getRawBody(), true);

        $user = new User();
        $user->load($request, '');
        $user->setPassword($request['password'] ?? '');
        $user->generateAuthKey();

        if ($user->validate()) {
            if ($user->save()) {
                $accessToken = AccessToken::generateAccessToken($user->getId());
                return ApiResponse::successResponse('User created', ['accessToken' => $accessToken->token]);
            }

            return ApiResponse::errorResponse('Failed to save user', $user->getErrors());
        }

        return ApiResponse::errorResponse('Validation failed', $user->getErrors());
    }

    public function actionLogin(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = json_decode(Yii::$app->request->getRawBody(), true);

        $user = User::findByUsername($request['username'] ?? null);

        if ($user && $user->validatePassword($request['password'] ?? '')) {
            $accessToken = AccessToken::generateAccessToken($user->getId());

            if ($accessToken) {
                return ApiResponse::successResponse('Login successful', ['accessToken' => $accessToken->token]);
            }
        }

        return ApiResponse::errorResponse('Invalid username or password');
    }
}