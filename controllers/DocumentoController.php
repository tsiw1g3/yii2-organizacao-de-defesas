<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use DateTime;
use DateTimeZone;
use Yii;

class DocumentoController extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        if ($action->id == 'allow-cors') {
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetDoc($id_banca)
    {
        $banca = Banca::find()->where(['id' => $id_banca])->one();

        $orientador_id = UsuarioBanca::find()->select(['id_usuario', 'nota'])->where(['role' => 'orientador', 'id_banca' => $id_banca])->one();
        $orientador = Usuario::findOne($orientador_id)->nome;

        $discente_id = UsuarioBanca::find()->select(['id_usuario'])->where(['role' => 'aluno', 'id_banca' => $id_banca])->one();
        $discente = empty($discente_id) ? '--' : Usuario::findOne($discente_id)->nome;

        $avaliadores_id = UsuarioBanca::find()->select(['id_usuario', 'IFNULL(nota, 0) as nota'])->where(['role' => 'avaliador', 'id_banca' => $id_banca])->all();

        if (!empty($avaliadores_id)) {
            $avaliador_1 = isset($avaliadores_id[0]) ? Usuario::findOne($avaliadores_id[0])->nome : '--';
            $avaliador_2 = isset($avaliadores_id[1]) ? Usuario::findOne($avaliadores_id[1])->nome : '--';

            $avaliadores = [
                $avaliador_1 => isset($avaliadores_id[0]) ? $avaliadores_id[0]->nota : 0,
                $avaliador_2 => isset($avaliadores_id[1]) ? $avaliadores_id[1]->nota : 0
            ];
        } else {
            $avaliador_1 = '--';
            $avaliador_2 = '--';

            $avaliadores = [
                $avaliador_1 => 0,
                $avaliador_2 => 0
            ];
        }

        $dtz = new DateTimeZone("America/Sao_Paulo");
        $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
        $data = $dateTime->format('d/m/Y');
        $horario = $dateTime->format('H:i');

        $tcc = $this->renderPartial('_tcc.php', [
            'titulo_trabalho' => $banca->titulo_trabalho,
            'orientador' => $orientador,
            'nota_orientador' => isset($orientador_id->nota) ? $orientador_id->nota : 0,
            'discente' => $discente,
            'avaliadores' => $avaliadores,
            'data' => $data,
            'horario' => $horario,
        ]);

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($tcc);
        $mpdf->Output();
    }
}
