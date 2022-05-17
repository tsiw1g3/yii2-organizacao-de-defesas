<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Session;
use app\models\Usuario;
use app\security\ValidatorRequest;
use Exception;
use Yii;
use yii\web\Response;

/**
 * Controller que gerencia toda a parte de login/logout do sistema.
 */
class LoginController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\LoginForm';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        unset($defaultActions['create']);
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
                if (!$session->isActive) {
                    $session->open();
                }
                $session_db = Session::findOne(Yii::$app->session->getId());

                if ($session_db !== null) {
                    $session_db->delete();
                }

                $session_db = new Session();
                $session_db->id = Yii::$app->session->getId();

                $id = Yii::$app->user->getId();
                $usuario = Usuario::findOne($id);
                $session_db->token_access = $usuario->auth_key;
                $session_db->validate();

                $session_db->save();

                return \Yii::createObject([
                    'class' => 'yii\web\Response',
                    'format' => \yii\web\Response::FORMAT_JSON,
                    'data' => [
                        'id' => $id,
                        'token' => Yii::$app->session->getId(),
                        'role' => $usuario->role,
                    ],
                ]);
            }

            // Caso o login falhe, lançar erros para o front
            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 403;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionLogout()
    {

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }

        $user = Usuario::findOne(Yii::$app->user->getId());
        $sessions = Session::find()->where(['token_access' => $user->auth_key])->all();
        foreach ($sessions as $session) {
            $session->delete();
        }

        Yii::$app->user->logout();

        Yii::$app->response->data = $user->errors;
        Yii::$app->response->statusCode = 204;
        return Yii::$app->response->data;
    }
}
