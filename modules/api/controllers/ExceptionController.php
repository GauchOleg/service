<?php

namespace app\modules\api\controllers;

use app\modules\api\base\BaseServiceController;

class ExceptionController extends BaseServiceController
{
    public function actionError()
    {
        return $this->response(
            \Yii::t('app', 'Wrong Request')
        );
    }
}
