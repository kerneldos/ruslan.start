<?php

use app\models\Tag;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TagSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

/** @var Tag $model */

$this->title = 'Tags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index">
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="tags-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tags-tabs-list-tab" data-toggle="pill" href="#tags-tabs-list" role="tab" aria-controls="tags-tabs-list" aria-selected="true">List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tags-tabs-import-tab" data-toggle="pill" href="#tags-tabs-import" role="tab" aria-controls="tags-tabs-import" aria-selected="false">Import</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="tags-tabsContent">
                <div class="tab-pane fade show active" id="tags-tabs-list" role="tabpanel" aria-labelledby="tags-tabs-list-tab">
                    <p>
                        <?= Html::a('Create Tag', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
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
                        'columns' => [
                            'id',
                            'name',
                            [
                                'class' => ActionColumn::className(),
                                'urlCreator' => function ($action, Tag $model, $key, $index, $column) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                }
                            ],
                        ],
                    ]); ?>
                </div>

                <div class="tab-pane fade" id="tags-tabs-import" role="tabpanel" aria-labelledby="tags-tabs-import-tab">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

                    <div class="form-group">
                        <!-- <label for="customFile">Custom File</label> -->

                        <div class="custom-file">
                            <?= Html::activeFileInput($model, 'importFile', [
                                'id' => 'customFile',
                                'class' => 'custom-file-input',
                            ]) ?>
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Import</button>

                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>


</div>
