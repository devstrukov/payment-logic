<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $email
 * @property string $token
 * @property float $balance
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return [
            [['email', 'token'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['balance'], 'number', 'min' => 0],
            [['token'], 'string', 'max' => 255],
        ];
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::class, ['user_id' => 'id']);
    }
}