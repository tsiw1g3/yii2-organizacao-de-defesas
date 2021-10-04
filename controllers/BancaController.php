<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use Exception;
use Yii;

class BancaController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\Banca';

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
        // this will get what you did send as application/x-www-form-urlencoded params
        // note that if you are sending data as query params you can use Yii::$app->request->queryParams instead.

        unset($defaultActions['create']);
        return $defaultActions;
    }

    public function actionCreate()
    {
        try {
            $banca = new Banca();

            // Coletando valores da requisição POST que foi recebida
            $data = Yii::$app->request->post();


            // Atribuindo os atributos da requição para o modelo
            $banca->attributes = $data;

            if ($banca->validate()  && $banca->save()) {

                $usuario_banca = new UsuarioBanca();
                $usuario_banca->id_banca = $banca->id;
                $usuario_banca->id_usuario = Yii::$app->user->getId();
                $usuario_banca->role = 'orientador';
                $usuario_banca->save();

                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $banca->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
        }
    }

    public function actionGetUsers($id)
    {
        try {
            $banca = $this->findByBancaById($id);

            $usuarios = $this->findUsersByBanca($id);
            return $usuarios;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionDeleteUserBanca($id, $user)
    {
        try {
            $banca = $this->findByBancaById($id);

            $usuario = $this->findUserByBanca($id, $user);

            if ($usuario->role == 'orientador') {
                throw new \yii\web\NotFoundHttpException('O orientador nao pode ser deletado', 403);
            }

            if ($usuario->delete()) {
                Yii::$app->response->statusCode = 204;
                return Yii::$app->response->data;
            }

            // Caso a exclusao falhe, lançar erros para o front
            Yii::$app->response->data = $usuario->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    protected function findByBancaById($id)
    {
        if (($user = Banca::findOne($id)) !== null) {
            return $user;
        }

        throw new \yii\web\NotFoundHttpException('A banca informada não existe.', 404);
    }

    protected function findUsersByBanca($id)
    {
        $models = Usuario::find()
            ->select('usuario.*')
            ->leftJoin('usuario_banca', '`usuario_banca`.`id_usuario` = `usuario`.`id`')
            ->where(['usuario_banca.id_banca' => $id])
            // ->with('orders')
            ->all();

        return $models;
    }

    protected function findUserByBanca($banca, $user)
    {
        if (($model = UsuarioBanca::find()->where(['id_banca' => $banca, 'id_usuario' => $user])->one()) !== null) {
            return $model;
        }

        throw new \yii\web\NotFoundHttpException('O usuário informado não existe nesta banca.', 404);
    }
}
