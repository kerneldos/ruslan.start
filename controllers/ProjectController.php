<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\AiTextCategory;
use app\models\DocumentSearch;
use Yii;
use yii\web\NotFoundHttpException;

class ProjectController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex(): string {
        $categories = AiTextCategory::find()->all();

        return $this->render('index', [
            'categories' => $categories
        ]);
    }

    /**
     * @param int $categoryId
     *
     * @return string
     */
    public function actionView(int $categoryId): string {
        $searchModel = new DocumentSearch();
        $searchModel->ai_category = $categoryId;

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
