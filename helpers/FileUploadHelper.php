<?php

namespace app\helpers;

use Yii,
    yii\web\UploadedFile,
    yii\helpers\FileHelper;

class FileUploadHelper
{
    public static function saveAs(UploadedFile $instance, $dirPath)
    {
        // File name in server folder
        $fileName = md5($instance->name . time());
        //Sub directory path
        $subDirPath = substr($fileName, 0, 2) . DIRECTORY_SEPARATOR . substr($fileName, 2, 2);

        $data = [
            'basePath' => Yii::getAlias('@webroot'),
            'extension' => $instance->extension,
            'fileRealName' => $instance->name,
        ];

        if (FileHelper::createDirectory(Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . $dirPath . DIRECTORY_SEPARATOR . $subDirPath)) {
            $filePath = Yii::getAlias('@webroot') .
                    DIRECTORY_SEPARATOR . $dirPath .
                    DIRECTORY_SEPARATOR . $subDirPath .
                    DIRECTORY_SEPARATOR . $fileName . '.' . $instance->extension;

            if ($instance->saveAs($filePath)) {
                $data['filePath'] = DIRECTORY_SEPARATOR . $dirPath .
                        DIRECTORY_SEPARATOR . $subDirPath .
                        DIRECTORY_SEPARATOR . $fileName . '.' . $instance->extension;
            }
        }

        return $data;
    }

    public static function removeFile($filePath)
    {
        $filePath = Yii::getAlias('@webroot') . $filePath;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }
}
