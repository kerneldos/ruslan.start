<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */
/** @var Tag[] $tags */
/** @var yii\data\Sort $sort */

use app\models\Tag;
use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'Search Project';
?>
<div class="site-index">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?= Html::a('Пересоздать индекс', ['/site/recreate-index'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Проиндексировать ПК', ['/site/samba-indexing'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <?php echo $this->render('_search', ['model' => $searchModel, 'tags' => $tags]); ?>

                        <div class="list-group">
                            <p>
                                Сортировать:
                                <?= $sort->link('name', ['label' => 'Наименование']) ?> |
                                <?= $sort->link('created', ['label' => 'Дата создания']) ?>
                            </p>

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
            <div class="col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($tags as $tag): ?>
                            <a data-value="<?= $tag->name ?>" href="" class="js-tags btn btn-default mb-1"><?= $tag->name ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
