<?php

namespace app\models;

use Yii;

class UserRefreshToken extends \yii\db\ActiveRecord {
    public static function tableName()
    {
        return 'user_refresh_tokens';
    }
}