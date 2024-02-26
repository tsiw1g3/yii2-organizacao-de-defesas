<?php

namespace app\controllers;

use app\controllers\common\filters\Cors;
use app\security\ValidatorRequest;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Curso;
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

    public function actions() {
        $defaultActions = parent::actions();
        
        unset($defaultActions['create']);
    }

    public function actionAllowCors() {}

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
}
