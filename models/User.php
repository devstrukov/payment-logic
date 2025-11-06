<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @property int $id
     * @property string $email
     * @property string $token
     * @property float $balance
     * @property string $created_at
     * @property string $updated_at
     * @property string $password_hash
     */

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
            [['token'], 'unique'], // token должен быть уникальным
            [['password_hash'], 'string'],
        ];
    }

    // IdentityInterface methods
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->token; // ← используем поле token
    }

    public function validateAuthKey($authKey)
    {
        return $this->token === $authKey;
    }

    // Password methods
    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthToken()
    {
        $this->token = \Yii::$app->security->generateRandomString(64);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord || empty($this->token)) {
                $this->generateAuthToken();
            }
            return true;
        }
        return false;
    }
}