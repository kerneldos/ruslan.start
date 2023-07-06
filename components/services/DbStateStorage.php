<?php

namespace app\components\services;

use app\models\StageStorage;
use yii\authclient\StateStorageInterface;
use yii\base\Component;

class DbStateStorage extends Component implements StateStorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $stateStorage = StageStorage::findOne(['key' => $key]);
        if (empty($stateStorage)) {
            $stateStorage = new StageStorage(['key' => $key]);
        }

        $stateStorage->value = serialize($value);
        $stateStorage->save();
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $stateStorage = StageStorage::findOne(['key' => $key]);
        if (!empty($stateStorage)) {
            return unserialize($stateStorage->value);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key): bool {
        StageStorage::deleteAll(['key' => $key]);

        return true;
    }
}