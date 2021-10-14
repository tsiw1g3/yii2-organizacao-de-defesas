<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use Exception;
use Yii;

class UsuarioBancaController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\UsuarioBanca';

    public function beforeAction($action)
    {
        if($action->id == 'allow-cors') {
           $this->enableCsrfValidation = false;
           return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public function actionAllowCors() {
        return;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        unset($defaultActions['create']);
        unset($defaultActions['delete']);
        return $defaultActions;
    }

    public function actionAdd($id)
    {
        try {
            $model = new UsuarioBanca();

            $banca = $this->findBancaById($id);

            $data = Yii::$app->request->post();
            $model->attributes = $data;

            $user = $this->findUserById($model->id_usuario);
            $model->id_banca = $id;

            // $aux = $this->findUsuarioBancaByBanca($id, $model->id_usuario);
            // return empty($aux);
            // Valida se o usuário já esta na banca
            if($this->findUsuarioBancaByBanca($id, $model->id_usuario)) {
                throw new \yii\web\NotFoundHttpException('Usuario ja cadastrado na banca.', 404);
            }

            if ($model->validate() && $model->save()) {
                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionId($id_banca, $id_usuario) {
        return $this->findUbByUserAndBanca($id_usuario, $id_banca);
    }

    public function actionNota($id_banca) {
        $users = $this->findUsuariosBancaByBanca($id_banca);
        $cnt = 0;
        $sum = 0.0;
        foreach ($users as $user) {
            $nota = $user->nota;
            if($nota != null) {
                $sum += $user->nota;
                $cnt++;
            }
        }
        if($cnt == 0) {
            return "Nenhuma nota foi cadastrada";
        }
        return $sum / $cnt;
    }

    protected function findUbByUserAndBanca($idUsuario, $idBanca) {
        if(($ub = UsuarioBanca::findOne(['id_banca' => $idBanca, 'id_usuario' => $idUsuario]))) {
            return $ub;
        }

        throw new \yii\web\NotFoundHttpException('O usuário informado não existe.', 404);
    }

    protected function findUserById($id)
    {
        if (($user = Usuario::findOne($id)) !== null) {
            return $user;
        }

        throw new \yii\web\NotFoundHttpException('O usuário informado não existe.', 404);
    }

    protected function findBancaById($id)
    {
        if (($banca = Banca::findOne($id)) !== null) {
            return $banca;
        }

        throw new \yii\web\NotFoundHttpException('A banca informada não existe.', 404);
    }

    protected function findUsuariosBancaByBanca($banca)
    {
        if (($models = UsuarioBanca::find()->where(['id_banca' => $banca])->all()) !== null) {
            return $models;
        }

        throw new \yii\web\NotFoundHttpException('Nao existem usuarios para esta banca.', 404);
    }

    protected function findUsuarioBancaByBanca($banca, $user)
    {
        if (($models = UsuarioBanca::find()->where(['id_banca' => $banca, 'id_usuario' => $user])->all()) !== null) {
            return !empty($models);
        }

        return false;
    }
}
