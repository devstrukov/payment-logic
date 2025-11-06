<?php
namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\services\AuthServiceInterface;

class AuthController extends Controller
{
    private $authService;

    public function __construct($id, $module, AuthServiceInterface $authService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->authService = $authService;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['me'], // только для actionMe
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'login' => ['post'],
            ],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        try {
            $data = Yii::$app->request->post();

            if (empty($data['email']) || empty($data['password'])) {
                Yii::$app->response->statusCode = 400;
                return $this->asJson([
                    'success' => false,
                    'error' => 'Email and password are required'
                ]);
            }

            $result = $this->authService->login($data['email'], $data['password']);

            if ($result['success']) {
                return $this->asJson([
                    'success' => true,
                    'data' => [
                        'token' => $result['token'],
                        'user' => $result['user']
                    ]
                ]);
            } else {
                Yii::$app->response->statusCode = 401;
                return $this->asJson([
                    'success' => false,
                    'error' => 'Invalid credentials',
                    'errors' => $result['errors'] ?? null
                ]);
            }

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'error' => 'Login failed'
            ]);
        }
    }

    public function actionMe()
    {
        try {
            $user = $this->authService->getCurrentUser();

            if (!$user) {
                Yii::$app->response->statusCode = 401;
                return $this->asJson([
                    'success' => false,
                    'error' => 'Not authenticated'
                ]);
            }

            return $this->asJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'balance' => $user->balance
                ]
            ]);

        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'error' => 'Internal server error'
            ]);
        }
    }
}