<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property string $invite_hash
 * @property int $user_id
 */
class ResetPassword extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reset_password';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'reset_password_hash'], 'required'],
            [['reset_password_hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reset_password_hash' => 'Reset Password hash',
            'user_id' => 'User Id',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'reset_password_hash',
            'user_id'

        ];
    }

    /**
     * @return int|string o ID do convite atual
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string o hash do convite atual
     */
    public function getHash()
    {
        return $this->reset_password_hash;
    }
}
