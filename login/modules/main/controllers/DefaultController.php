<?php

namespace login\modules\main\controllers;

use common\models\LoginForm;
use common\models\Portal;
use login\models\PasswordResetRequestForm;
use login\models\ResendVerificationEmailForm;
use login\models\ResetPasswordForm;
use login\models\SignupForm;
use login\models\VerifyEmailForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `main` module
 */
class DefaultController extends Controller
{
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
        $userPortals = Yii::$app->user->identity->portals;

        /** @var Portal $userPortal */
        $userPortal  = reset($userPortals);
        $redirectUrl = sprintf('https://%s.%s', $userPortal->temp_name, Yii::$app->params['main_domain']);

        return $this->redirect($redirectUrl);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $userPortals = Yii::$app->user->identity->portals;

            /** @var Portal $userPortal */
            $userPortal  = reset($userPortals);
            $redirectUrl = sprintf('https://%s.%s', $userPortal->temp_name, Yii::$app->params['main_domain']);

            return $this->redirect($redirectUrl);
        }

        $this->layout = 'login';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $userPortals = Yii::$app->user->identity->portals;

            /** @var Portal $userPortal */
            $userPortal  = reset($userPortals);
            $redirectUrl = sprintf('https://%s.%s', $userPortal->temp_name, Yii::$app->params['main_domain']);

            return $this->redirect($redirectUrl);
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
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSignup(): string {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');

            $user = $model->getUser();

            $portal = Portal::findOne(['user_id' => null]);
            if (!empty($portal)) {
                $user->portals = [$portal->id];
                $user->save();
            } else {
                throw new NotFoundHttpException('Server Error');
            }
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

        $this->layout = 'login';

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

        $this->layout = 'login';

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
     */
    public function actionVerifyEmail(string $token): Response {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (($user = $model->verifyEmail()) && Yii::$app->user->login($user)) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');

            if (count($user->portals) == 1) {
                $userPortals = $user->portals;

                /** @var Portal $userPortal */
                $userPortal  = reset($userPortals);
                $redirectUrl = sprintf('https://%s.%s', $userPortal->temp_name, Yii::$app->params['main_domain']);

                return $this->redirect($redirectUrl);
            }
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

        $this->layout = 'login';

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }
}
