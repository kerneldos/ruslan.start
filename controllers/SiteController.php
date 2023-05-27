<?php

namespace app\controllers;

use app\components\jobs\IndexingJob;
use app\helpers\FileConverter;
use app\models\Config;
use app\models\Document;
use app\models\DocumentSearch;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use Yii;
use yii\filters\AccessControl;
use yii\httpclient\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
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
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'isConnected' => !empty(Yii::$app->session['yandex_api_token']),
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
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
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string {
        return $this->render('about');
    }

    /**
     * @return Response
     */
    public function actionIndexing(): Response {
        Yii::$app->queue->push(new IndexingJob());

        Yii::$app->session->setFlash('indexingIsOk');
        return $this->redirect('index');
    }

    /**
     * @param string $code
     *
     * @return Response
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionGetToken(string $code): Response {
        $clientId = Config::findOne(['name' => 'yandex_client_id']);
        $clientSecret = Config::findOne(['name' => 'yandex_client_secret']);

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://oauth.yandex.ru/token')
            ->setData([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => $clientId->value,
                'client_secret' => $clientSecret->value,
            ])
            ->send();
        if ($response->isOk) {
            $configToken = Config::findOne(['name' => 'yandex_api_token']);
            if (empty($configToken)) {
                $configToken = new Config([
                    'title' => 'Yandex API Token',
                    'name'  => 'yandex_api_token',
                ]);
            }

            $configToken->value = $response->data['access_token'];
            $configToken->save();

            Yii::$app->session->set('yandex_api_token', array_merge($response->data, ['token_created' => time()]));
        }

        return $this->redirect('index');
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return Response
     * @throws Exception
     * @throws InvalidConfigException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionDownload(string $path, string $name): Response {
        $client = new Client(['baseUrl' => 'https://cloud-api.yandex.net/v1/']);
        $response = $client->createRequest()
            ->addHeaders(['Authorization' => Yii::$app->session['yandex_api_token']['access_token']])
            ->setUrl('disk/resources/download')
            ->setMethod('GET')
            ->setData(['path' => $path])
            ->send();

        if ($response->isOk) {
            return Yii::$app->response->sendContentAsFile(file_get_contents($response->data['href']), $name);
        }

        return $this->redirect('index');
    }

    /**
     * @return bool
     */
    protected function isTokenExpired(): bool {
        if (empty(Yii::$app->session['yandex_api_token'])) {
            return true;
        }

        $token = Yii::$app->session['yandex_api_token'];
        if (time() > $token['token_created'] + $token['expires_in']) {
            return true;
        }

        return false;
    }

    /**
     * @param string $redirectUri
     *
     * @return string
     * @throws NotFoundHttpException
     */
    protected function getAuthUrl(string $redirectUri): string {
        $clientId = Config::findOne(['name' => 'yandex_client_id']);
        $clientSecret = Config::findOne(['name' => 'yandex_client_secret']);

        if (!empty($clientId->value) && !empty($clientSecret->value)) {
            return sprintf('https://oauth.yandex.ru/authorize?%s', http_build_query([
                'response_type' => 'code',
                'client_id' => $clientId->value,
                'redirect_uri' => $redirectUri,
                'state' => 'yandex',
                'force_confirm' => true,
            ], '', '&', PHP_QUERY_RFC3986));
        }

        throw new NotFoundHttpException('Client Id or Client Secret is empty');
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConnectApi(): Response {
        $authUrl = $this->getAuthUrl('https://45.12.74.245/site/get-token');

        return $this->redirect($authUrl);
    }

    /**
     * @return Response
     */
    public function actionDisconnectApi(): Response {
        Yii::$app->session->remove('yandex_api_token');

        return $this->goBack();
    }

    /**
     * Displays a single Document model.
     * @param string $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(string $id): string {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'isConnected' => !empty(Yii::$app->session['yandex_api_token']),
        ]);
    }

    /**
     * @return Response
     * @throws InvalidConfigException
     * @throws \yii\elasticsearch\Exception
     */
    public function actionRecreateIndex(): Response {
        Document::deleteIndex();
        Document::createIndex();

        return $this->redirect('index');
    }

    /**
     * @param string $id
     *
     * @return Document|null
     * @throws NotFoundHttpException
     */
    protected function findModel(string $id): ?Document {
        if (($model = Document::findOne(['_id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
