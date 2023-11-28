<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */
/** @var Tag[] $tags */
/** @var Category[] $categories */
/** @var yii\data\Sort $sort */
/** @var AiCategory $category */

use consumer\models\AiCategory;
use consumer\models\Category;
use consumer\models\Tag;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Search Project';
?>
<div class="site-index">
    <div class="container-fluid">
        <?php if (!empty($category->children)): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($category->children as $categoryChild): ?>
                                    <div class="col-md-3" style="text-align: center; margin-bottom: 20px;">
                                        <a style="display: inline-block;" href="<?= Url::to(['/project/view', 'categoryId' => $categoryChild->id]) ?>">
                                            <i style="font-size: 100px;" class="far fa-folder"></i>
                                            <div><?= $categoryChild->name ?></div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        <div class="list-group">
                            <?= ListView::widget([
                                'dataProvider' => $dataProvider,
                                'itemView' => '_list',
                                'layout' => "{summary}\n{items}\n{pager}",
                                'pager' => [
                                    'options' => [
                                        'class' => 'pagination mt-2',
                                    ],

                                    // Customzing CSS class for pager link
                                    'linkOptions' => ['class' => 'page-link'],
                                    'pageCssClass' => 'paginate_button page-item',
                                    'activePageCssClass' => 'paginate_button page-item active',

                                    // Customzing CSS class for navigating link
                                    'prevPageCssClass' => 'paginate_button page-item previous',
                                    'nextPageCssClass' => 'paginate_button page-item next',
                                    'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
