<?php

/** @var yii\web\View $this */
/** @var yii\elasticsearch\ActiveDataProvider $dataProvider */
/** @var Model $searchModel */
/** @var bool $isConnected */
/** @var Tag[] $tags */

use app\models\Tag;
use yii\base\Model;
use yii\widgets\ListView;

$this->title = 'Search Project';
?>
<div class="site-index">

    <div class="content-header">
        <div class="container-fluid">
            <?php foreach ($tags as $tag): ?>
                <a href="?DocumentSearch[content]=<?= $tag->name ?>" class="col-md-2 btn btn-default"><?= $tag->name ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="list-group">
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => '_list',
                            'layout' => "{items}\n{pager}",
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
