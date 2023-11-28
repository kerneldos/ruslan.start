<?php

namespace consumer\components\services;

use consumer\components\jobs\SambaIndexingJob;
use consumer\models\Config;
use Yii;

class Samba extends \yii\base\BaseObject implements ServiceInterface {
    public $id;

    /**
     * @return void
     */
    public function indexing(): void {
        $sambaUser = Config::find()->where(['name' => 'samba_user'])->select(['value'])->scalar();
        $sambaPassword = Config::find()->where(['name' => 'samba_password'])->select(['value'])->scalar();

        Yii::$app->queue->push(new SambaIndexingJob(['user' => $sambaUser, 'password' => $sambaPassword]));
    }

    /**
     * @return bool
     */
    public function needAuth(): bool {
        return false;
    }

    /**
     * @return string
     */
    public function buildAuthUrl(): string {
        return '';
    }
}