<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $payment_id
 * @property int $integration_id
 * @property string $status
 * @property string $external_id
 * @property string $error_message
 * @property string $created_at
 *
 * @property Payment $payment
 * @property Integration $integration
 */
class Attempt extends ActiveRecord
{
    const STATUS_PENDING = 'pending';    // Попытка создана, но еще не обработана
    const STATUS_SUCCEED = 'succeed';    // Попытка успешна
    const STATUS_FAILED = 'failed';      // Попытка неуспешна

    public static function tableName()
    {
        return 'attempts';
    }

    public function rules()
    {
        return [
            [['payment_id', 'integration_id', 'status'], 'required'],
            [['payment_id', 'integration_id'], 'integer'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_SUCCEED, self::STATUS_FAILED]],
            [['external_id', 'error_message'], 'string'],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::class, 'targetAttribute' => ['payment_id' => 'id']],
            [['integration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Integration::class, 'targetAttribute' => ['integration_id' => 'id']],
        ];
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::class, ['id' => 'payment_id']);
    }

    public function getIntegration()
    {
        return $this->hasOne(Integration::class, ['id' => 'integration_id']);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEED, self::STATUS_FAILED]);
    }

    public function markAsSucceed(string $externalId = null): bool
    {
        $this->status = self::STATUS_SUCCEED;
        $this->external_id = $externalId;
        return $this->save();
    }

    public function markAsFailed(string $errorMessage = null): bool
    {
        $this->status = self::STATUS_FAILED;
        $this->error_message = $errorMessage;
        return $this->save();
    }
}