<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\AiCategory;
use app\models\AiTextCategory;
use app\models\Document;
use app\models\DocumentSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ProjectController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex(): string {
//        $categories = AiTextCategory::find()->all();
        $categories = AiCategory::find()->where(['parent_id' => 0])->all();
        $categories = array_filter($categories, function(AiCategory $category) {
            if (!empty($category->children)) {
                foreach ($category->children as $child) {
                    if (!empty(Document::findOne(['ai_category' => $child->id]))) {
                        return true;
                    }
                }

                return false;
            }

            return !empty(Document::findOne(['ai_category' => $category->id]));
        });

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
            'category' => AiCategory::findOne($categoryId),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
