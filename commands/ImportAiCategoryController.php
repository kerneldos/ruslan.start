<?php


namespace app\commands;


use app\models\AiCategory;
use yii\console\Controller;

class ImportAiCategoryController extends Controller
{
    public function actionIndex() {
        $fileImport = \Yii::getAlias('@runtime/categories.txt');
        $importCategories = array_map('trim', explode(PHP_EOL, file_get_contents($fileImport)));

        if (!empty($importCategories)) {
            foreach ($importCategories as $category) {
                $aiCategory = AiCategory::find()->where(['name' => $category])->one();

                if (empty($aiCategory)) {
                    $aiCategory = new AiCategory(['name' => $category]);
                    $aiCategory->save();
                }
            }
        }
    }
}