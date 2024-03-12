<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */
/** @var Tag[] $tags */
/** @var Category[] $categories */
/** @var yii\data\Sort $sort */

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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?= Html::a('Пересоздать индекс', ['/site/recreate-index'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Проиндексировать ПК', ['/site/indexing', 'service' => 'samba'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Проиндексировать Yandex диск', ['/site/indexing', 'service' => 'yandex'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Проиндексировать Bitrix диск', ['/site/indexing', 'service' => 'bitrix'], [
                            'class' => 'btn btn-primary',
                        ]) ?>
                        <?= Html::a('Проиндексировать загрузки', ['/site/indexing', 'service' => 'local'], [
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

                        <div class="row">
                            <?php foreach ($categories ?? [] as $category): ?>
                                <div class="col-md-12">
                                    <div class="card card-primary collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <a href="<?= Url::to(['/site/search', 'DocumentSearch[category]' => $category->id]) ?>">
                                                    <i class="far fa-folder"></i>
                                                    <?= $category->name ?>
                                                </a>
                                            </h3>

                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <!-- /.card-tools -->
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body" style="display: none;">
                                            Теги связанные с этой категорией
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="list-group">
                            <?php if ($dataProvider->totalCount): ?>
                                <p>
                                    Сортировать:
                                    <?= $sort->link('name', ['label' => 'Наименование']) ?> |
                                    <?= $sort->link('created', ['label' => 'Дата создания']) ?>
                                </p>
                            <?php endif; ?>

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
