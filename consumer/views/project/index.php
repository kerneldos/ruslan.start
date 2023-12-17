<?php

/** @var yii\web\View $this */
/** @var AiTextCategory[] $categories */

use consumer\models\AiTextCategory;
use yii\helpers\Url;

$this->title = 'Search Project';
?>
<div class="site-index">
    <div class="container-fluid">
        <div class="row">
            <?php foreach ($categories ?? [] as $category): ?>
                <div class="col-md-3" style="text-align: center; margin-bottom: 20px;">
                    <a style="display: inline-block;" href="<?= Url::to(['/project/view', 'categoryId' => $category->id]) ?>">
                        <i style="font-size: 100px;" class="far fa-folder"></i>
                        <div><?= $category->name ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
