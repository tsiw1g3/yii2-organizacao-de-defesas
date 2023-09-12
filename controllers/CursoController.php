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
        if (!$permission && $action->id != 'create') {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public function actionAllowCors() {}

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
                $model->nome = $data['nome'];
                $model->sigla = $data['sigla'];
                $model->sigla = $data['disciplina'];
                $model->coordenacao = $data['coordenacao'];
                $model->cargo_coordenacao = $data['cargo_coordenacao'];
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
