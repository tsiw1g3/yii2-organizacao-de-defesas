<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Curso;
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
    
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionOptions() {
        return true;
    }

    public function actionGetDoc($id_banca)
    {
        $tcc = $this->renderPartial('_tcc.php', [
            'curso' => $_POST['curso'],
            'nome_curso' => $_POST['nome_curso'],
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
        
        $mpdf = $this->getMpdf();
        $mpdf->WriteHTML($tcc);
        $mpdf->Output();
    }

    public function actionGetDocParticipacao($id_banca) {
        $banca = Banca::find()->where(['id' => $id_banca])->one();            
        $curso = Curso::find()->where(['id' => $banca->curso])->one();
        
        $orientador = (new \yii\db\Query())
        ->select(['IFNULL(usuario_banca.nota,0) as nota', 'usuario_banca.role','usuario.nome', 'usuario.pronoun'])
        ->from('usuario_banca')
        ->innerJoin('usuario', 'usuario_banca.id_usuario = usuario.id')
        ->where("usuario_banca.id_banca = $id_banca AND usuario_banca.role = 'orientador'")
        ->one();

        $dtz = new DateTimeZone("America/Sao_Paulo");
        $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
        $data = $dateTime->format('m/d/Y');

        $participacao = $this->renderPartial('_participacao.php', [
            'pronome_orientador' => $orientador["pronoun"],
            'titulo_trabalho' => $banca->titulo_trabalho,
            'pronome_aluno' => $banca->pronome_autor,
            'orientador' => $orientador["nome"],
            'nome_curso' => $curso->nome,
            'aluno' => $banca->autor,
            'curso' => $banca->curso,
            'data' => $data,
            'coordenacao' => $curso->coordenacao,
            'cargo_coordenacao' => $curso->cargo_coordenacao,
        ]);
                    
        $mpdf = $this->getMpdf();        
        $mpdf->showImageErrors = true;

        $mpdf->WriteHTML($participacao);
        $mpdf->Output();
    }
    
    public function actionGetDocOrientacao($id_banca) {
        $banca = Banca::find()->where(['id' => $id_banca])->one();
        $curso = Curso::find()->where(['id' => $banca->curso])->one();
        
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
            'pronome_aluno' => $banca->pronome_autor,
            'orientador' => $orientador["nome"],
            'nome_curso' => $curso->nome,
            'aluno' => $banca->autor,
            'curso' => $banca->curso,
            'data' => $data,
            'coordenacao' => $curso->coordenacao,
            'cargo_coordenacao' => $curso->cargo_coordenacao,
        ]);
                    
        $mpdf = $this->getMpdf();
        $mpdf->showImageErrors = true;
        
        $mpdf->WriteHTML($orientacao);
        $mpdf->Output();
    }

    public function actionDocumentoInfo($id_banca){
        $banca = Banca::find()->where(['id' => $id_banca])->one();
        $curso = Curso::find()->where(['id' => $banca->curso])->one();

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
            'nome_curso' => $curso->nome,
            'disciplina' => $curso->disciplina,
            'turma' => $banca->turma,
            'titulo_trabalho' => $banca->titulo_trabalho,
            'orientador' => $orientador["nome"],
            'nota_orientador' => isset($orientador["nota"]) ? $orientador["nota"] : 0,
            'aluno' => $banca->autor,
            'avaliadores' => $avaliadores,
            'data' => $data,
            'horario' => $horario,
            'semestre' => $banca->ano . "." . $banca->semestre_letivo,
        ];

        return json_encode($response);
    }

    function getMpdf() {
        return new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf']);
    }
}
