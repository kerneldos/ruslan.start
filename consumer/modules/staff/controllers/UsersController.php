<?php

namespace consumer\modules\staff\controllers;

use common\models\User;
use common\models\UserSearch;
use consumer\modules\staff\models\InviteForm;
use consumer\modules\staff\Module;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\Controller;

/**
 * Users controller for the `staff` module
 */
class UsersController extends Controller
{
    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function actionIndex(): string {
        /** @var Module $module */
        $module = $this->module;

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $module->portal->getUsers());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param int $id
     *
     * @return string
     */
    public function actionView(int $id): string {
        return $this->render('view', [
            'model' => User::findOne($id),
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionInvite(): string {
        /** @var Module $module */
        $module = $this->module;

        $model = new InviteForm();
        if ($model->load(Yii::$app->request->post()) && $model->invite()) {
            Yii::$app->session->setFlash('success', 'Users are invited');

            $user = $model->getUser();

            $user->portals = $module->portal->id;
            $user->save();
        }

        return $this->render('invite', [
            'model' => $model,
        ]);
    }
}
