<?php

namespace app\modules\api\models;

use yii\filters\auth\QueryParamAuth;

class CustomQueryAuth extends QueryParamAuth
{
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            if ($accessToken == \Yii::$app->params['token']) {
                return true;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}