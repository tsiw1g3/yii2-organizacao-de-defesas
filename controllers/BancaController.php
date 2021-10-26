<?php

namespace app\controllers;

use app\models\Banca;
use app\models\BancaDocumento;
use app\models\Documento;
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
        if($action->id == 'allow-cors' || $action->id == 'index') {
           $this->enableCsrfValidation = false;
           return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
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

    public function actionGetDocuments($id)
    {
        try {
            $banca = $this->findByBancaById($id);

            return $this->findDocsByBanca($banca->id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionGetDocument($id, $doc)
    {
        try {
            $banca = $this->findByBancaById($id);
            $documento = $this->findDocByBancaAndDoc($banca->id, $doc);
            if (empty($documento)) {
                throw new \yii\web\NotFoundHttpException('O documento requisitado não existe.', 404);
            }
            return $documento;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionAddDocument($id)
    {
        try {
            $banca = $this->findByBancaById($id);

            $documento = new Documento();
            $data = Yii::$app->request->post();

            $documento->attributes  = $data;
            $file = $_FILES['documento'];

            $type = $file['type'];
            if ($type !== 'application/pdf') {
                throw new \yii\web\MethodNotAllowedHttpException('O tipo do documento nao eh suportado.');
            }

            $name_file = $file['name'];
            $temp_folder = explode('\\', $file['tmp_name']);
            $temp_folder = $temp_folder[count($temp_folder) - 1];
            $temp_folder = str_replace(".tmp", "", $temp_folder);

            $fileTarget = '../documents_banca/' . $temp_folder;

            // If the directory does not exist, create a new
            if (!file_exists($fileTarget)) {
                mkdir($fileTarget, 0700, true);
            }

            $fileTarget = '../documents_banca/' . $temp_folder . '/' . $name_file;
            $fileTarget = $this->stripAccents($fileTarget);

            $result = move_uploaded_file($file['tmp_name'], $fileTarget);

            if (!$result) {
                return 'ERROR';
                die();
            }

            $documento->path = '../documents_banca/' . $temp_folder . '/' . $name_file;
            if ($documento->validate() && $documento->save()) {
                $doc_banca = new BancaDocumento();
                $doc_banca->id_banca = $banca->id;
                $doc_banca->id_documento = $documento->id;
                $doc_banca->save();

                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $documento->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionDeleteDocument($id, $doc)
    {
        try {
            $banca = $this->findByBancaById($id);
            $documento = $this->findDocByBancaAndDoc($banca->id, $doc);
            if (empty($documento)) {
                throw new \yii\web\NotFoundHttpException('O documento requisitado não existe.', 404);
            }

            if ($documento->delete()) {
                Yii::$app->response->statusCode = 204;
                return Yii::$app->response->data;
            }
            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $documento->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionViewDocument($id, $doc)
    {
        try {
            $banca = $this->findByBancaById($id);
            $documento = $this->findDocByBancaAndDoc($banca->id, $doc);
            if (empty($documento)) {
                throw new \yii\web\NotFoundHttpException('O documento requisitado não existe.', 404);
            }

            // Header content type
            header('Content-type: application/pdf');

            header('Content-Disposition: inline; filename="' . $documento->path . '"');

            header('Content-Transfer-Encoding: binary');

            header('Accept-Ranges: bytes');

            @readfile($documento->path);

            return $documento->path;

            // return Yii::$app->response->data;
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

    protected function findDocsByBanca($banca)
    {
        $models = Documento::find()
            ->select('documento.*')
            ->leftJoin('banca_documento', '`banca_documento`.`id_documento` = `documento`.`id`')
            ->where(['banca_documento.id_banca' => $banca])
            // ->with('orders')
            ->all();

        return $models;
    }

    protected function findDocByBancaAndDoc($banca, $doc)
    {
        $model = Documento::find()
            ->select('documento.*')
            ->leftJoin('banca_documento', '`banca_documento`.`id_documento` = `documento`.`id`')
            ->where(['banca_documento.id_banca' => $banca, 'banca_documento.id_documento' => $doc])
            // ->with('orders')
            ->one();

        return $model;
    }

    protected function stripAccents($string)
    {

        $string = strtr(
            utf8_decode($string),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        );

        return $string;
    }
}
