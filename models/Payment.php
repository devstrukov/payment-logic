<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Attempt[] $attempts
 */
class Payment extends ActiveRecord
{
    const STATUS_PENDING = 'pending';    // Только что создан
    const STATUS_SUCCEED = 'succeed';    // Успешно завершен
    const STATUS_FAILED = 'failed';      // Завершен с ошибкой

    public static function tableName()
    {
        return 'payments';
    }

    public function rules()
    {
        return [
            [['user_id', 'amount'], 'required'],
            [['user_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['status'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_SUCCEED, self::STATUS_FAILED]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getAttempts()
    {
        return $this->hasMany(Attempt::class, ['payment_id' => 'id']);
    }

    public function getLastAttempt()
    {
        return $this->hasOne(Attempt::class, ['payment_id' => 'id'])->orderBy(['created_at' => SORT_DESC]);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_SUCCEED, self::STATUS_FAILED]);
    }

    public function markAsSucceed(): bool
    {
        $this->status = self::STATUS_SUCCEED;
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->status = self::STATUS_FAILED;
        return $this->save();
    }
}