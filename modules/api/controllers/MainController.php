<?php

namespace app\modules\api\controllers;

use app\helpers\FileUploadHelper,
    app\modules\api\base\BaseServiceController,
    app\services\MainService,
    yii\web\UploadedFile;

class MainController extends BaseServiceController
{
    private $service;

    public function verbs()
    {
        return [
            'index' => ['post'],
        ];
    }

    public function __construct($id, $module, MainService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionIndex()
    {
        try {
            $instance = UploadedFile::getInstanceByName('file');

            if (!$instance) {
                return $this->send400Response('Please upload a file');
            }

            if ($instance->getExtension() != 'xml') {
                return $this->send400Response('Allowed only xml file type');
            }

            $file = FileUploadHelper::saveAs($instance,'uploads/temp');
            $string = $this->service->getData($file['filePath']);
            FileUploadHelper::removeFile($file['filePath']);

            return $this->response('Client Route', 'success', $string);
        } catch (\Exception $exception) {
            return $this->response('Something wrong', 'error', null, 500);
        }
    }

    private function send400Response($message)
    {
        return $this->response(
            $message,
            'error',
            null,
            400
        );
    }
}
