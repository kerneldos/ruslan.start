<?php

namespace console\controllers;

use common\models\Portal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\Exception;
use yii\console\Response;

class ConsumerMigrateController extends Controller {
    /**
     * @return void
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws Exception
     */
    public function actionIndex() {
        $portals = Portal::find()->all();

        /** @var Portal $portal */
        foreach ($portals as $portal) {
            $consumerConfig = require dirname(__DIR__, 2) . '/consumer/config/client/' . $portal->temp_name . '.php';

            Yii::$app->set('db', $consumerConfig['components']['db']);
            Yii::$app->params['sub_domain'] = $portal->temp_name;

            Yii::$app->runAction('migrate/up', ['migrationPath' => '@console/migrations/consumer/', 'interactive' => false]);
        }
    }

    /**
     * @param $name
     *
     * @return int|mixed|Response|null
     * @throws Exception
     * @throws InvalidRouteException
     */
    public function actionCreate($name) {
        return Yii::$app->runAction('migrate/create', ['migrationPath' => '@console/migrations/consumer', $name]);
    }
}