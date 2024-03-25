<?php

namespace console\controllers;

use common\models\Portal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\controllers\MigrateController;
use yii\console\Exception;

class ConsumerMigrateController extends MigrateController {
    public $migrationPath = ['@console/migrations/consumer'];

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws Exception
     */
    public function actionUp($limit = 0) {
        $portals = Portal::find()->all();

        $this->interactive = false;

        /** @var Portal $portal */
        foreach ($portals as $portal) {
            $consumerConfig = dirname(__DIR__, 2) . '/consumer/clients/' . $portal->temp_name . '/config/main.php';

            if (file_exists($consumerConfig)) {
                $config = require $consumerConfig;

                $this->db->dsn = $config['components']['db']['dsn'];

                Yii::$app->params['sub_domain'] = $portal->temp_name;

                parent::actionUp($limit);
            }
        }
    }
}