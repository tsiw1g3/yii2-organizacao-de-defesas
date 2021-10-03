<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Session;
use app\models\Usuario;
use app\security\ValidatorRequest;
use Exception;
use Yii;

class LoginController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\LoginForm';


    public function beforeAction($action)
    {
        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        return $defaultActions;
    }

    public function actionLogin()
    {
        try {
            $model = new LoginForm();

            // Coletando valores da requisição POST que foi recebida
            $data = Yii::$app->request->post();

            // Atribuindo os atributos da requição para o modelo
            $model->attributes = $data;

            $model->validate();

            // Validando o login
            if ($model->login()) {
                $session = Yii::$app->session;
                // $session->set('token_access', 'Rafael');
                // $session->save();
                if (!$session->isActive) {
                    $session->open();
                }
                $session_db = Session::findOne(Yii::$app->session->getId());
                $usario = Usuario::findOne(Yii::$app->user->getId());
                $session_db->token_access = $usario->auth_key;
                $session_db->save();

                return Yii::$app->session->getId();

            }

            // Caso a validacao falhe, lançar erros para o front
            return $model;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
