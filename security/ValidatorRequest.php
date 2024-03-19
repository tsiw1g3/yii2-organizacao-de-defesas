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

        // Check if the session has expired
        if($session_db->expire < time()) {
            $session_db->delete();
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

    public static function getCurrentSessionOwner($headers) {
        $token = $headers->get('Authorization');
        $session = Session::findOne($token);
        
        if(!$session) return null;
        return Usuario::findIdentityByAccessToken($session->token_access);
    }
}
