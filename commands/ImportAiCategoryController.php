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
                $explodeCategories = explode(';', $category);

                $parentCategoryName = mb_strtolower($explodeCategories[0]);

                $aiCategory = AiCategory::find()->where(['name' => $parentCategoryName])->one();
                if (empty($aiCategory)) {
                    $aiCategory = new AiCategory(['name' => $parentCategoryName]);
                    $aiCategory->save();
                }

                if (count($explodeCategories) > 1) {
                    $childrenCategories = explode(',', $explodeCategories[1]);
                    foreach ($childrenCategories as $childrenCategory) {
                        $childrenCategoryName = mb_strtolower($childrenCategory);

                        $aiChildrenCategory = AiCategory::find()->where(['parent_id' => $aiCategory->id, 'name' => $childrenCategoryName])->one();

                        if (empty($aiChildrenCategory)) {
                            $aiChildrenCategory = new AiCategory(['parent_id' => $aiCategory->id, 'name' => $childrenCategoryName]);
                            $aiChildrenCategory->save();
                        }
                    }
                }
            }
        }
    }
}