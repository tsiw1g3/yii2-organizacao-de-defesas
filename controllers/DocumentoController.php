<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use DateTime;
use DateTimeZone;
use Yii;
use app\controllers\common\filters\Cors;


/**
 * Controller que gerencia a geração de documentos.
 */
class DocumentoController extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        if ($action->id == 'allow-cors' || $action->id == 'get-doc') {
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public static function allowedDomains() {
        return [$_SERVER["REMOTE_ADDR"], 'http://localhost:3000/'];
    }
    
    function behaviors()
        {
            
            $behaviors = parent::behaviors();
            return array_merge($behaviors, [
                'corsFilter'  => [
                    'class' => Cors::className(),
                    'cors'  => [
                        // restrict access to domains:
                        'Origin'                           => static::allowedDomains(),
                        'Access-Control-Request-Method'    => ['POST', 'GET', 'OPTIONS'],
                        'Access-Control-Allow-Credentials' => true,
                        'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                        'Access-Control-Allow-Headers' => ['authorization','X-Requested-With','content-type', 'Access-Control-Allow-Origin'],
                        'Access-Control-Check' => true
                    ],
                ],
            ]);
            return $behaviors;
        }

    
        public function actionIndex()
        {
            return $this->render('index');
        }

        public function actionAllowCors() {}

        public function actionGetDoc($id_banca)
        {
            $_POST["tempo3"] = time();
            $tcc = $this->renderPartial('_tcc.php', [
                'curso' => $_POST['curso'],
                'disciplina' => $_POST['disciplina'],
                'turma' => $_POST['turma'],
                'titulo_trabalho' => $_POST['titulo_trabalho'],
                'orientador' => $_POST['orientador'],
                'nota_orientador' => isset($_POST['nota_orientador']) ? $_POST['nota_orientador'] : 0,
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

            throw new Error;
        }

        public function actionGetDocParticipacao($id_banca) {
            $banca = Banca::find()->where(['id' => $id_banca])->one();
            
            $orientador = (new \yii\db\Query())
            ->select(['IFNULL(usuario_banca.nota,0) as nota', 'usuario_banca.role','usuario.nome'])
            ->from('usuario_banca')
            ->innerJoin('usuario', 'usuario_banca.id_usuario = usuario.id')
            ->where("usuario_banca.id_banca = $id_banca AND usuario_banca.role = 'orientador'")
            ->one();

            $dtz = new DateTimeZone("America/Sao_Paulo");
            $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
            $data = $dateTime->format('m/d/Y');

            $participacao = $this->renderPartial('_participacao.php', [
                'titulo_trabalho' => $banca->titulo_trabalho,
                'orientador' => $orientador["nome"],
                'aluno' => $banca->autor,
                'data' => $data,
            ]);
                        
            $mpdf = new \Mpdf\Mpdf();            
            $mpdf->WriteHTML($participacao);
            $mpdf->Output();
        }
        
        public function actionGetDocOrientacao($id_banca) {
            $banca = Banca::find()->where(['id' => $id_banca])->one();
            
            $orientador = (new \yii\db\Query())
            ->select(['IFNULL(usuario_banca.nota,0) as nota', 'usuario_banca.role','usuario.nome', 'usuario.pronoun'])
            ->from('usuario_banca')
            ->innerJoin('usuario', 'usuario_banca.id_usuario = usuario.id')
            ->where("usuario_banca.id_banca = $id_banca AND usuario_banca.role = 'orientador'")
            ->one();

            $dtz = new DateTimeZone("America/Sao_Paulo");
            $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
            $data = $dateTime->format('m/d/Y');

            $orientacao = $this->renderPartial('_orientacao.php', [
                'pronome_orientador' => $orientador["pronoun"],
                'titulo_trabalho' => $banca->titulo_trabalho,
                'orientador' => $orientador["nome"],
                'aluno' => $banca->autor,
                'data' => $data,
            ]);
                        
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($orientacao);
            $mpdf->Output();
        }

        public function actionDocumentoInfo($id_banca){
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

            return json_encode($response);
        }
}
