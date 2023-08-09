<?php

namespace app\controllers;

use app\models\Invite;
use app\security\ValidatorRequest;
use Exception;
use Yii;
use yii\helpers\VarDumper;

/**
 * Controller que gerencia todas as rotas necessárias para a banca.
 */
class InviteController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\Invite';

    public function beforeAction($action)
    {
        if($action->id == 'allow-cors' || $action->id == 'index') {
           $this->enableCsrfValidation = false;
           return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission && $action->id != 'get-invite') {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public function actionAllowCors() {}

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        // this will get what you did send as application/x-www-form-urlencoded params
        // note that if you are sending data as query params you can use Yii::$app->request->queryParams instead.

        unset($defaultActions['create']);
        return $defaultActions;
    }

    public function actionCreate()
    {
        try {
            // Coletando valores da requisição POST que foi recebida
            $data = Yii::$app->request->post();

            $invite = Invite::findOne(['user_id' => $data['user_id']]);
            $data['invite_hash'] = Yii::$app->user->identity->getEmail() . $data['invite_hash'];
            if($invite == null){
                $invite = new Invite();
            }


            // Atribuindo os atributos da requição para o modelo
            $invite->attributes = $data;

            if ($invite->validate()) {
                $invite->invite_hash = sha1($invite->invite_hash);
                // Salva o invite no banco de dados
                $invite->save();
                return $invite->invite_hash;
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $invite->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionGetInvite($hash)
    {
        try {
            $invite = Invite::findOne(['invite_hash' => $hash]);
            if($invite == null){
                return false;
            }
            else{
                return true;
            }
        } catch (Exception $e) {
            var_dump("Achei invite", $e); die();
        }
    }
}
