<?php

namespace login\controllers;

use console\controllers\NewConsumerController;
use login\models\PasswordResetRequestForm;
use login\models\ResendVerificationEmailForm;
use login\models\ResetPasswordForm;
use login\models\SignupForm;
use login\models\VerifyEmailForm;
use common\models\LoginForm;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
        ];
    }

    /**
     * @return Response
     */
    public function actionIndex(): Response {
        return $this->redirect('https://demo.app.ru');
//        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect('https://' . Yii::$app->user->identity->temp_domain . '.app.ru');
        }

        $this->layout = 'login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('https://' . $model->redirectUrl . '.app.ru');
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
        }

        $this->layout = 'signup';

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return Response|string
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return Response|string
     * @throws BadRequestHttpException
     */
    public function actionResetPassword(string $token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (\yii\base\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionVerifyEmail(string $token): Response {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');

            if (empty($user->temp_domain)) {
                $str = '0123456789abcdefghijklmnopqrstuvwxyz';

                do {
                    $tempDomain = substr(str_shuffle($str), 0, 6);

                    $user->temp_domain = $tempDomain;
                } while (!$user->save());

                $consumerDbName = 'consumer_' . $user->temp_domain;

                Yii::$app->preInstallDb->createCommand('CREATE DATABASE ' . $consumerDbName)->execute();

                $oldApp = Yii::$app;

                $config = yii\helpers\ArrayHelper::merge(
                    require dirname(__DIR__, 2) . '/common/config/main.php',
                    require dirname(__DIR__, 2) . '/common/config/main-local.php',
                    require dirname(__DIR__, 2) . '/console/config/main.php',
                    require dirname(__DIR__, 2) . '/console/config/main-local.php'
                );
                $config['components']['db']['dsn'] = sprintf('mysql:host=mysql;dbname=%s', $consumerDbName);

                new \yii\console\Application($config);

                ob_start();
                    Yii::$app->runAction('new-consumer/init', [$user->temp_domain]);
                    Yii::$app->runAction('migrate/up', ['migrationPath' => '@console/migrations/consumer/', 'interactive' => false]);
                ob_get_clean();

                Yii::$app = $oldApp;
            }

            return $this->redirect('https://' . $user->temp_domain . '.app.ru');
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');

        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return Response|string
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
