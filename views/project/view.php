<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */
/** @var Tag[] $tags */
/** @var Category[] $categories */
/** @var yii\data\Sort $sort */

use app\models\Category;
use app\models\Tag;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Search Project';
?>
<div class="site-index">
    <div class="container-fluid">
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
