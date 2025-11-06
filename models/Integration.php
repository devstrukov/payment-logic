<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Integration extends ActiveRecord
{
    public static function tableName()
    {
        return 'integrations';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['is_active'], 'boolean'],
        ];
    }

    public function getAttempts()
    {
        return $this->hasMany(Attempt::class, ['integration_id' => 'id']);
    }
}