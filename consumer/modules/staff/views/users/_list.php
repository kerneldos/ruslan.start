<?php

use common\models\User;
use yii\helpers\Html;

/** @var User $model */
?>
<div class="card bg-light d-flex flex-fill">
    <div class="card-header text-muted border-bottom-0">

    </div>
    <div class="card-body pt-0">
        <div class="row">
            <div class="col-7">
                <h2 class="lead"><b><?= $model->username ?></b></h2>
                <p class="text-muted text-sm"><b>About: </b> Web Designer / UX / Graphic Artist / Coffee Lover </p>
                <ul class="ml-4 mb-0 fa-ul text-muted">
                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-building"></i></span> Address: Demo Street 123, Demo City 04312, NJ</li>
                    <li class="small"><span class="fa-li"><i class="fas fa-lg fa-phone"></i></span> Email: <?= $model->email ?></li>
                </ul>
            </div>
            <div class="col-5 text-center">
                <img src="/img/no_avatar.png" alt="user-avatar" class="img-circle img-fluid">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="text-right">
            <a href="#" class="btn btn-sm bg-teal">
                <i class="fas fa-comments"></i>
            </a>

            <?= Html::a('<i class="fas fa-user"></i> View Profile', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>
</div>
