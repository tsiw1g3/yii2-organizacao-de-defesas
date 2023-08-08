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
use DateTime;
use DateTimeZone;
use yii\helpers\VarDumper;

/**
 * Controller que gerencia todas as rotas necessárias para a banca.
 */
class BancaController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\Banca';

    public function beforeAction($action)
    {
        $_POST['action2'] = $action->id;
        if($action->id == 'allow-cors' || $action->id == 'index') {
           $this->enableCsrfValidation = false;
           return parent::beforeAction($action);
        }
        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission && $action->id != "view") {
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
        unset($defaultActions['delete']);
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

            if ($banca->validate() && $banca->save()) {

                $usuario_banca = new UsuarioBanca();
                $usuario_banca->id_banca = $banca->id;
                $usuario_banca->id_usuario = Yii::$app->user->getId();
                if(Yii::$app->user->identity->getRole() == 1 || Yii::$app->user->identity->getRole() == 3){
                    $usuario_banca->role = 'orientador';
                }
                else if (Yii::$app->user->identity->getRole() == 2){
                    $usuario_banca->role = 'co-orientador';
                }
                $usuario_banca->save();

                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $banca->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionGetBancasByUser($user_id){
        $bancas = Banca::findAll(['user_id' => $user_id]);
        return $bancas;
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

            // if ($usuario->role == 'orientador') {
            //     throw new \yii\web\NotFoundHttpException('O orientador nao pode ser deletado', 403);
            // }

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

    public function actionDeleteBanca($id)
    {
        try {
            $banca = $this->findByBancaById($id);

            if ($banca->delete()) {
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

    public function actionGetReport($id_banca)
        {
            $_POST["tempo3"] = time();
            $tcc = $this->renderPartial('../documento/_tcc.php', [
                'curso' => $_POST['curso'],
                'disciplina' => $_POST['disciplina'],
                'turma' => $_POST['turma'],
                'titulo_trabalho' => $_POST['titulo_trabalho'],
                'orientador' => $_POST['orientador'],
                'nota_orientador' => isset($_POST['orientador_nota']) ? $_POST['orientador_nota'] : 0,
                'aluno' => $_POST['aluno'],
                'avaliadores' => json_decode($_POST['avaliadores']),
                'data' => $_POST['data'],
                'horario' => $_POST['horario'],
                'semestre' => $_POST['semestre'],
            ]);
            $_POST["tempo4"] = time();
            
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($tcc);
            $mpdf->Output();

            $_POST["tempo5"] = time();

        }

        public function actionReportInfo($id_banca){
            $_POST["tempo1"] = time();

            $banca = Banca::find()->where(['id' => $id_banca])->one();

            $membros_banca = (new \yii\db\Query())
            ->select(['IFNULL(usuario_banca.nota,0) as nota', 'usuario_banca.role','usuario.nome'])
            ->from('usuario_banca')
            ->innerJoin('usuario', 'usuario_banca.id_usuario = usuario.id')
            ->where("usuario_banca.id_banca = $id_banca")
            ->all();
            $orientador;
            $avaliadores = array();
            foreach($membros_banca as $membro){
                if($membro['role'] == "orientador"){
                    $orientador = $membro;
                }
                else if($membro['role'] == "avaliador"){
                    $avaliadores[$membro['nome']] = $membro['nota'];
                }
            }

            $dtz = new DateTimeZone("America/Sao_Paulo");
            $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
            $data = $dateTime->format('d/m/Y');
            $horario = $dateTime->format('H:i');

            $response = [
                'curso' => $banca->curso,
                'disciplina' => $banca->disciplina,
                'turma' => $banca->turma,
                'titulo_trabalho' => $banca->titulo_trabalho,
                'orientador' => $orientador["nome"],
                'nota_orientador' => isset($orientador["nota"]) ? $orientador["nota"] : 0,
                'aluno' => $banca->autor,
                'avaliadores' => $avaliadores,
                'data' => $data,
                'horario' => $horario,
                'semestre' => $banca->ano . "." . $banca->semestre_letivo,
                'tempo1' => time()
            ];
            $_POST["tempo2"] = time();

            return $response;
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
