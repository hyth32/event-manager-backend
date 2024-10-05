<?php

namespace backend\controllers;

use backend\models\AccessToken;
use backend\models\ApiResponse;
use Yii;
use yii\base\Controller;
use yii\web\Response;
use backend\models\Event;

class EventController extends Controller
{
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = json_decode(Yii::$app->request->getRawBody(), true);

        $accessToken = $request['accessToken'] ?? null;
        if (!$accessToken) {
            return ApiResponse::errorResponse('accessToken is required');
        }

        $userId = AccessToken::getUserIdFromToken($request['accessToken']);
        if (!$userId) {
            return ApiResponse::errorResponse('User not found');
        }

        $event = new Event();
        $event->load($request, '');
        $event->userId = $userId;

        if ($event->validate()) {
            if ($event->save()) {
                return ApiResponse::successResponse('Post created', ['event' => $event]);
            }
        }

        return ApiResponse::errorResponse('Validation failed', $event->getErrors());
    }
}