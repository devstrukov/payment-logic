<?php
namespace app\controllers;

use yii\web\Controller;
use yii\filters\VerbFilter;
use app\services\PaymentServiceInterface;
use app\services\UserServiceInterface;

class PaymentController extends Controller
{
    private $paymentService;
    private $userService;

    public function __construct(
        $id,
        $module,
        PaymentServiceInterface $paymentService,
        UserServiceInterface $userService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->paymentService = $paymentService;
        $this->userService = $userService;
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'balance' => ['get'],
                    'create' => ['post'],
                    'status' => ['get'],
                ],
            ],
        ];
    }

    public function actionBalance()
    {
        try {
            $balance = $this->userService->getCurrentUserBalance();

            return $this->asJson([
                'success' => true,
                'data' => [
                    'balance' => $balance
                ]
            ]);
        } catch (\Exception $e) {
            \Yii::error("Balance error: " . $e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'error' => 'Internal server error'
            ]);
        }
    }

    public function actionCreate()
    {
        try {
            $data = \Yii::$app->request->post();

            // Базовая валидация
            if (empty($data['amount'])) {
                \Yii::$app->response->statusCode = 400;
                return $this->asJson([
                    'success' => false,
                    'error' => 'amount is required'
                ]);
            }

            $payment = $this->paymentService->createPayment($data);

            return $this->asJson([
                'success' => true,
                'data' => $payment
            ]);
        } catch (\InvalidArgumentException $e) {
            \Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (\RuntimeException $e) {
            \Yii::$app->response->statusCode = 400;
            return $this->asJson([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            \Yii::error("Create payment error: " . $e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'error' => 'Payment creation failed'
            ]);
        }
    }

    public function actionStatus($id)
    {
        try {
            $status = $this->paymentService->getPaymentStatus($id);

            return $this->asJson([
                'success' => true,
                'data' => $status
            ]);
        } catch (\RuntimeException $e) {
            \Yii::$app->response->statusCode = 404;
            return $this->asJson([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            \Yii::error("Status error: " . $e->getMessage());
            \Yii::$app->response->statusCode = 500;
            return $this->asJson([
                'success' => false,
                'error' => 'Internal server error'
            ]);
        }
    }
}