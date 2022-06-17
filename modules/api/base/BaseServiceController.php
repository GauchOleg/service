<?php

namespace app\modules\api\base;

use Yii,
    yii\rest\Controller,
    yii\web\Response,
    app\modules\api\models\CustomQueryAuth,
    yii\filters\VerbFilter;

abstract class BaseServiceController extends Controller
{
    public function init()
    {
        Yii::$app->response->on(Response::EVENT_BEFORE_SEND, function($event) {
            $response = $event->sender;
            if (200 !== $response->statusCode) {
                $response->data = [
                    'status' => 'error',
                    'code' => $response->statusCode,
                    'message' => $response->data['message'],
                    'data' => new \stdClass(),
                ];
                $response->statusCode = 200;
            }
        });

        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => CustomQueryAuth::class,
        ];
        $behaviors['verbs'] =
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ];
        return $behaviors;
    }

    public function response($message = null, $status = 'success', $data = null, $code = 200)
    {
        return ['status'=> $status, 'code' => $code, 'message' => $message, 'data' => $data];
    }

}