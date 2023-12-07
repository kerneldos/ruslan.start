<?php

namespace consumer\controllers;


use consumer\components\BaseController;
use consumer\components\services\ServiceInterface;
use consumer\components\services\Yandex;
use consumer\models\Category;
use consumer\models\Document;
use consumer\models\DocumentSearch;
use consumer\models\Tag;
use yii\authclient\OAuth2;
use yii\base\InvalidConfigException;
use yii\data\Sort;
use Yii;
use yii\elasticsearch\Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use consumer\models\LoginForm;
use consumer\models\ContactForm;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['error'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['portal_member'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup', 'get-token'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
//                    'logout' => ['post'],
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
     * @param string $service
     *
     * @return Response
     */
    public function actionIndexing(string $service): Response {
        /** @var ServiceInterface|OAuth2 $client */
        $client = Yii::$app->authClientCollection->getClient($service);

        if ($client->needAuth()) {
            return $this->redirect($client->buildAuthUrl());
        }

        $client->indexing(Yii::$app->params['subDomain']);

        return $this->redirect('index');
    }

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionIndex() {
        if (Yii::$app->request->isAjax) {
            $searchResult = Document::find()->addAggregate('documents_by_type', [
                'terms' => [
                    'field' => 'media_type',
                ],
            ])->limit(0)->search();

            $documentsByType = [
                'labels' => array_column($searchResult['aggregations']['documents_by_type']['buckets'], 'key'),
                'data'   => array_column($searchResult['aggregations']['documents_by_type']['buckets'], 'doc_count'),
            ];

            $searchResult = Document::find()->addAggregate('documents_by_date', [
                'date_histogram' => [
                    'field' => 'attachment.date',
                    'calendar_interval' => 'month',
                    'min_doc_count' => 1,
                ],
            ])->limit(0)->search();

            $documentsByDate = [
                'labels' => array_map(
                    function($date) {return date('d.m.Y', strtotime($date));},
                    array_column($searchResult['aggregations']['documents_by_date']['buckets'], 'key_as_string'),
                ),
                'data'   => array_column($searchResult['aggregations']['documents_by_date']['buckets'], 'doc_count'),
            ];

            return $this->asJson([
                'documentsByType' => $documentsByType,
                'documentsByDate' => $documentsByDate,
            ]);
        }

//        $this->view->registerJsFile(Yii::getAlias('@common/web/dist/js/pages/dashboard.js'), ['depends' => ['common\assets\AppAsset']]);

        return $this->render('index');
    }

    /**
     * @return string|Response
     */
    public function actionSearch() {
        $sort = new Sort([
            'attributes' => ['name', 'created'],
        ]);

        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['results' => $dataProvider->getModels(), 'total_count' => $dataProvider->totalCount]);
        }

        $tags = Tag::find()->all();
        $categories = Category::find()->where(['parent_id' => $searchModel->category ?? 0])->all();

        return $this->render('search', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'tags' => $tags,
            'categories' => $categories,
            'sort' => $sort,
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

        $this->layout = 'login';

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
     * @param string $code
     * @param string $service
     *
     * @return Response
     * @throws HttpException
     */
    public function actionGetToken(string $code, string $service): Response {
        /** @var OAuth2 $client */
        $client = Yii::$app->authClientCollection->getClient($service);
        $client->fetchAccessToken($code);

        return $this->redirect('search');
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionDownload(string $id): Response {
        $document = $this->findModel($id);

        if ($document->type == 'yandex') {
            /** @var OAuth2 $client */
            $client = Yii::$app->authClientCollection->getClient(Yandex::SERVICE_NAME);

            $response = $client->api('disk/resources/download', 'GET', ['path' => $document->file]);
            $content  = file_get_contents($response['href']);
        } else {
            $content = file_get_contents($document->path);
        }

        if (!empty($content)) {
            return Yii::$app->response->sendContentAsFile($content, $document->name);
        }

        return $this->redirect('index');
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
     * @throws Exception
     */
    public function actionRecreateIndex(): Response {
        Document::deleteIndex();
        Document::createIndex();

        $es = Yii::$app->elasticsearch;

        $es->delete('_ingest/pipeline/attachment');
        $es->put('_ingest/pipeline/attachment', [], json_encode([
            'description'  => 'Extract attachment information',
            'processors' => [
                [
                    'attachment' => [
                        'field' => 'content',
                        'target_field' => 'attachment',
                        'indexed_chars' => -1,
                        'ignore_failure' => true
                    ],
                ],
                [
                    'set' => [
                        'field' => 'content',
                        'value' => '{{{attachment.content}}}',
                        'ignore_failure' => true,
                    ],
                ],
//                [
//                    'remove' => [
//                        'field' => 'content',
//                        'ignore_failure' => true
//                    ],
//                ],
            ],
        ]));

        return $this->redirect('index');
    }

    /**
     * @return Response
     */
    public function actionTestIndexing(): Response {
        $documents = [
            [
                'name'       => 'Москва.jpg',
                'content'    => base64_encode('метро.мкад'),
                'created'    => time(),
                'mime_type'  => 'text/plain',
                'file'       => 'Москва.jpg',
                'media_type' => 'text',
                'path'       => 'Москва.jpg2',
                'type'       => 'image',
                'sha256'     => 'cfdvdvfdvcsdvgfewgf',
                'md5'        => 'vdvdvdvdv',
            ],
            [
                'name'       => 'Word document.docx',
                'content'    => base64_encode('рыба текст это хороший фариант'),
                'created'    => time(),
                'mime_type'  => 'text/plain',
                'file'       => 'Word document.docx',
                'media_type' => 'text',
                'path'       => 'Word document.docx3',
                'type'       => 'image',
                'sha256'     => 'cfdvdvfdvcsdvgfewgf',
                'md5'        => 'vdvdvdvdv',
            ],
        ];

        foreach ($documents as $document) {
            $file = new Document($document);
            $file->insert(true, array_keys($document), ['pipeline' => 'attachment']);
        }

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
