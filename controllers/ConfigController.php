<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\Config;
use app\models\ConfigSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ConfigController implements the CRUD actions for Config model.
 */
class ConfigController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Config models.
     *
     * @return string
     */
    public function actionIndex(): string {
        $searchModel = new ConfigSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Config model.
     *
     * @param int $id ID
     *
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Config model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Config();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Config model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id ID
     *
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Config model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id ID
     *
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete(int $id): Response {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Config model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id ID
     *
     * @return Config the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Config {
        if (($model = Config::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
