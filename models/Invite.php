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
class Invite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'invite_hash'], 'required'],
            [['invite_hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invite_hash' => 'Invite hash',
            'user_id' => 'User Id',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'invite_hash',
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
        return $this->invite_hash;
    }
}
