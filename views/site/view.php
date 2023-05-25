<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Document $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'created',
            'mime_type',
            [
                'attribute' => 'file',
                'format' => 'raw',
                'value' => function($model) use ($isConnected) {
                    return Html::a('Скачать', Url::to(['download', 'path' => $model->path, 'name' => $model->name]), [
                        'target' => '_blank',
                        'class' => 'btn btn-info ' . (!$isConnected ? 'disabled' : ''),
                    ]);
                },
            ],
            'media_type',
            'content',
        ],
    ]) ?>

</div>
