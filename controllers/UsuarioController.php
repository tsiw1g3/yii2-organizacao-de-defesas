<?php

namespace app\controllers;

use app\models\Banca;
use app\models\LoginForm;
use app\models\User;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UsuarioController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\Usuario';
    private $user;


    public function beforeAction($action)
    {
        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission && $action->id != 'create') {
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
        unset($defaultActions['create']);
        unset($defaultActions['delete']);
        unset($defaultActions['update']);
        return $defaultActions;
    }


    public function actionCreate()
    {
        try {
            $model = new Usuario();

            // Coletando valores da requisição POST que foi recebida
            $data = Yii::$app->request->post();

            // Atribuindo os atributos da requição para o modelo
            $model->attributes = $data;


            /// Atribuindo horario/data para os atributos do modelo 
            $dtz = new DateTimeZone("America/Sao_Paulo");
            $now = new DateTime("now", $dtz);
            $now = $now->format("Y-m-d H:i:s");
            $model->created_at = $now;
            $model->updated_at = $now;


            // Coleta a senha enviada pelo formulário para fazer a validacao
            $model->password_has = $data['password'];

            // Gera uma chave aleatoria para usar como token, validando que vai ser unique
            $model->auth_key = Yii::$app->getSecurity()->generateRandomString();
            while ($this->findModelByToken($model->auth_key)) {
                $model->auth_key = Yii::$app->getSecurity()->generateRandomString();
            }

            // Validar os atributos do modelo
            if ($model->validate()) {
                //  Faz a criptografia da senha enviada
                $model->password_has = Yii::$app->getSecurity()->generatePasswordHash($data['password']);

                // Salva o modelo no banco de dados
                $model->save();

                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
        // throw new \yii\web\NotFoundHttpException('The requested page does not exist.', 403);
    }

    public function actionGetBanca($id)
    {
        try {
            $bancas = $this->findBancasByUser($id);
            return $bancas;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function findModelById($id)
    {
        if (($model = Usuario::findOne($id)) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('O usuario informado nao existe.', 404);
    }

    protected function findModelByUsername($username)
    {
        if (($model = Usuario::find()->where(['username' => $username])->one()) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('The requested page does not exist.', 404);
    }

    protected function findModelByToken($token)
    {
        if (($model = Usuario::find()->where(['auth_key' => $token])->one()) !== null) {
            return true;
        }

        return false;
    }

    protected function findBancasByUser($user)
    {
        if (($models = Banca::find()
        ->select('banca.*')
        ->innerJoin('usuario_banca', '`usuario_banca`.`id_banca` = `banca`.`id`')
        // ->leftJoin('usuario_banca', '`usuario_banca`.`id_banca` = `banca`.`id`')
        ->where(['usuario_banca.id_usuario' => $user])
        ->all()) !== null) {
            return $models;
        }

        throw new \yii\web\NotFoundHttpException('Nao existem bancas para este usuario.', 404);
    }
}
