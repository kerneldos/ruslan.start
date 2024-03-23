<?php

namespace consumer\modules\staff;

use common\models\Portal;
use Yii;

/**
 * staff module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'consumer\modules\staff\controllers';

    public Portal $portal;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->portal = Portal::findOne(['temp_name' => Yii::$app->params['sub_domain']]);
    }
}
