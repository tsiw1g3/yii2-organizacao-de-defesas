<?php

namespace app\controllers;

use app\controllers\common\filters\Cors;
use app\security\ValidatorRequest;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Curso;
use app\models\Banca;
use DateTimeZone;
use Exception;
use DateTime;
use Yii;


/**
 * Controller que gerencia a lista de cursos
 */
class CursoController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Curso';
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['http://localhost:3000', 'https://sistema-de-defesas.app.ic.ufba.br'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
            'except' => [
                'options',
            ],
        ];

        return $behaviors;
    }

    public function actions() {
        $defaultActions = parent::actions();
        
        unset($defaultActions['create']);

        return $defaultActions;
    }

    public function actionCreateCurso() {
        $curso = new Curso();

        // Coletando valores da requisição POST que foi recebida
        $data = Yii::$app->request->post();

        // Atribuindo os atributos da requição para o modelo
        $curso->attributes = $data;
        
        if($curso->validate()) {
            $curso->save();
            return $curso;
        }

        // Caso a validacao falhe, lançar erros para o front
        Yii::$app->response->data = $curso->errors;
        Yii::$app->response->statusCode = 422;
        
        return Yii::$app->response->data;
    }

    public function actionGetCursos() {
        try {
            return Curso::find()->all();
        } catch(Exception $e) {
            throw $e;
        }
    }
    
    public function actionEditCursos($id) {
        try {
            $data = Yii::$app->request->post();
            $model = Curso::findOne($id);
            if ($model !== null) {
                foreach ($data as $key => $value) {
                    if ($model->hasAttribute($key)) {
                        $model->$key = $value;
                    }
                }
                
                if ($model->validate()) {
                    $model->save();
                    return $model;
                }
            }
            return $model;
        } catch(Exception $e) {
            throw $e;
        }
    }

    public function actionDeleteCurso($id) {
        try {
            $model = Curso::findOne($id);
            if($model !== null) {
                $banca = Banca::find()->where(['curso' => $id])->one();
                if(!$banca && $model->delete()) return $model;
                else throw new \yii\web\ForbiddenHttpException('Este curso já pertence a uma banca e não pode ser excluído!', 403);
            }
            return $model;

        } catch (Exception $e) {
            throw $e;
        }
    } 
}
