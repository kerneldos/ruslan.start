<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */

use app\models\Document;
use yii\base\Model;
use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="row">
        <div class="col-sm-3">
            <?= Html::a('Индексация', ['indexing'], ['class' => 'btn btn-success ' . (!$isConnected ? 'disabled' : '')]) ?>
        </div>
        <div class="col-sm-3 col-sm-offset-3">
            <?= !$isConnected
                ? Html::a('Подключить API Яндекс', ['connect-api'], ['class' => 'btn btn-success pull-right'])
                : Html::a('Отключить API', ['disconnect-api'], ['class' => 'btn btn-danger pull-right'])
            ?>
        </div>
        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="col-sm-3">
                <?= Html::a('Пересоздать индекс', ['recreate-index'], ['class' => 'btn btn-danger pull-right']) ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (Yii::$app->session->hasFlash('indexingIsOk')): ?>
        <div class="row">
            <div class="alert alert-success">
                Индексация прошла успешно!
            </div>
        </div>
    <?php endif; ?>

    <p>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function(Document $model) {
                    return Html::a($model->name, Url::toRoute(['view', 'id' => $model->_id]));
                },
            ],
            'created',
            'mime_type',
            'media_type',
            [
                'attribute' => 'file',
                'format' => 'raw',
                'value' => function(Document $model) use ($isConnected) {
                    return Html::a('Скачать', Url::to(['download', 'path' => $model->path, 'name' => $model->name, 'type' => $model->type]), [
                        'target' => '_blank',
                        'class' => 'btn btn-info',
                    ]);
                },
            ],
        ],
    ]); ?>
</div>
