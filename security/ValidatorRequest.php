<?php

namespace app\security;

use app\models\Session;
use app\models\User;
use app\models\Usuario;
use Yii;

class ValidatorRequest
{

    public static function validatorHeader($headers)
    {
        $token = $headers->get('Authorization');
        $session_db = Session::findOne($token);
        if(!$session_db) {
            return false;
        }
        $model = Usuario::findIdentityByAccessToken($session_db->token_access);
        if ($model === null) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        if(!Yii::$app->user->login($model)) {
            return false;
        }

        return true;
    }
}
