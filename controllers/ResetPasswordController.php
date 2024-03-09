<?php

namespace app\controllers;

use app\models\ResetPassword;
use app\models\Usuario;
use app\security\ValidatorRequest;
use Exception;
use Yii;
use yii\helpers\VarDumper;

/**
 * Controller que gerencia todas as rotas necessárias para a banca.
 */
class ResetPasswordController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\ResetPassword';

    public function beforeAction($action)
    {
        if($action->id == 'allow-cors' || $action->id == 'index') {
           $this->enableCsrfValidation = false;
           return parent::beforeAction($action);
        }

        // $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        // if (!$permission && $action->id != 'get-reset') {
        //     throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        // }
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

            $user = Usuario::findOne(['email' => $data['email']]);
            $data['user_id'] = $user->getId();
            $resetPassword = ResetPassword::findOne(['user_id' => $user->getId()]);
            $data['reset_password_hash'] = $data['email'] . $data['reset_password_hash'];
            if($resetPassword == null){
                $resetPassword = new ResetPassword();
            }            
            
            // Atribuindo os atributos da requição para o modelo
            $resetPassword->attributes = $data;
            
            if ($resetPassword->validate()) {
                $resetPassword->reset_password_hash = sha1($resetPassword->reset_password_hash);
                // Salva o reset password no banco de dados
                $resetPassword->save();
                $this->SendEmail($resetPassword->reset_password_hash, $data['email']);
                Yii::$app->response->statusCode = 201;
                Yii::$app->response->data = "O Email de redefinição de senha foi enviado";
                return Yii::$app->response->data;
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $resetPassword->errors;
            Yii::$app->response->statusCode = 422;
            return Yii::$app->response->data;
        } catch (Exception $e) {
            var_dump("Error: ", $e); die();
        }
    }

    public function actionGetResetHash($hash)
    {
        try {
            $reset_password = ResetPassword::findOne(['reset_password_hash' => $hash]);
            if($reset_password == null){
                return false;
            }
            else{
                return true;
            }
        } catch (Exception $e) {
            var_dump("Achei invite", $e); die();
        }
    }

    public function actionReset(){
        $data = Yii::$app->request->post();
        $resetPassword = ResetPassword::findOne(['reset_password_hash' => $data['hash']]);
        if($resetPassword->validate()){
            $model = Usuario::findOne(['id' => $resetPassword->user_id]);
            $model->password_has = $data['new_password'];
            if ($model->validate()) {
                //  Faz a criptografia da senha enviada
                $model->password_has = Yii::$app->getSecurity()->generatePasswordHash($data['new_password']);
                $resetPassword->delete();
                // Salva o modelo no banco de dados
                $model->save();

                return [];
            }
        }
        // Caso a validacao falhe, lançar erros para o front
        Yii::$app->response->data = $resetPassword->errors;
        Yii::$app->response->statusCode = 422;
        return Yii::$app->response->data;
    }

    public function SendEmail($hash, $email){
        try{
            $emails = explode(",", $email);
            $message = Yii::$app->mailer->compose('emailTemplateResetPass', 
            [
                'reset_hash' => $hash, 
            ]);
            $message->setFrom('sistemadedefesasufba@gmail.com');
            $message->setTo($emails);
            $message->setSubject("Pedido de redefinição de senha");
            $message->send();
            return "Email enviado com sucesso!";
        } catch (Exception $e) {
            // return "Ocorreu um erro ao tentar enviar o email, tente novamente mais tarde!";
            return $e->getMessage();
        }
    }
}
