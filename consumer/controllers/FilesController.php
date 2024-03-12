<?php

namespace consumer\controllers;

use consumer\components\BaseController;
use consumer\models\File;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * FilesController implements the CRUD actions for File model.
 */
class FilesController extends BaseController
{
    /**
     * @inheritdoc
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool {
        if ($action->id == 'upload') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all File models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $dataProvider = new ActiveDataProvider([
            'query' => File::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * @return void|Response
     * @throws Exception
     */
    public function actionUpload() {
        if (Yii::$app->request->isPost) {
            $files = UploadedFile::getInstancesByName('file');

            if (!empty($files)) {
                if (!is_dir(Yii::getAlias('@consumer/files/') . Yii::$app->params['sub_domain'])) {
                    FileHelper::createDirectory(Yii::getAlias('@consumer/files/') . Yii::$app->params['sub_domain']);
                }

                foreach ($files as $file) {
                    $path = uniqid();

                    if ($file->saveAs(Yii::getAlias('@consumer/files/') . Yii::$app->params['sub_domain'] . '/' . $path)) {
                        $model = new File([
                            'name' => $file->name,
                            'type' => $file->type,
                            'size' => $file->size,
                            'path' => $path,
                        ]);

                        $model->save(false);
                    }
                }

                return $this->asJson(['success' => true]);
            }
        }
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function actionDownload(int $id): Response {
        $file = File::findOne($id);

        $path = Yii::getAlias('@consumer/files/') . Yii::$app->params['sub_domain'] . '/' . $file->path;

        return Yii::$app->response->sendFile($path, $file->name);
    }
}
