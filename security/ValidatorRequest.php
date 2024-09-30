<?php

namespace app\security;

use app\models\UserRefreshToken;
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
        $raw_token = $headers->get('Authorization');
        list($bearer, $raw_token) = explode(' ', $raw_token, 2);

        if($raw_token) {
            $token = Yii::$app->jwt->getParser()->parse((string) $raw_token);        
            return Usuario::findIdentityByAccessToken($token);        
        }
        return null;
    }
}
