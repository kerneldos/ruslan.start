<?php

use common\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/** @var View $this */
/** @var UserSearch $searchModel */
/** @var ActiveDataProvider $dataProvider */

$this->title = 'Users:';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="staff-default-index">

    <div class="row">
        <div class="col-sm-3 mb-3">
            <?= Html::a('Пригласить пользователей', ['invite'], [
                'class' => 'btn btn-primary',
            ]) ?>
        </div>

        <div class="col-12">
            <?= $this->render('_search', ['model' => $searchModel]); ?>
        </div>

        <div class="col-sm-12">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['tag' => null],
                'itemOptions' => [
                    'class' => 'col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column',
                ],
                'itemView' => '_list',
                'layout' => '<div class="row">{items}</div><div class="card-footer">{pager}</div>',
                'pager' => [
                    'options' => [
                        'class' => 'pagination justify-content-center m-0',
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
