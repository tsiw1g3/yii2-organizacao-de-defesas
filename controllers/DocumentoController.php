<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use DateTime;
use DateTimeZone;

class DocumentoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetDoc($id_banca)
    {
        $banca = Banca::find()->where(['id' => $id_banca])->one();

        $orientador_id = UsuarioBanca::find()->select(['id_usuario', 'nota'])->where(['role' => 'orientador', 'id_banca' => $id_banca])->one();
        $orientador = Usuario::findOne($orientador_id)->nome;

        $discente_id = UsuarioBanca::find()->select(['id_usuario'])->where(['role' => 'discente', 'id_banca' => $id_banca])->one();
        $discente = Usuario::findOne($discente_id)->nome;

        $avaliadores_id = UsuarioBanca::find()->select(['id_usuario', 'nota'])->where(['role' => 'avaliador', 'id_banca' => $id_banca])->all();
        $avaliador_1 = Usuario::findOne($avaliadores_id[0])->nome;
        $avaliador_2 = Usuario::findOne($avaliadores_id[1])->nome;

        $avaliadores = [
            $avaliador_1 => $avaliadores_id[0]->nota,
            $avaliador_2 => $avaliadores_id[1]->nota
        ];

        $dtz = new DateTimeZone("America/Sao_Paulo");
        $dateTime = date_create_from_format("Y-m-d H:i:s", $banca->data_realizacao, $dtz);
        $data = $dateTime->format('d/m/Y');
        $horario = $dateTime->format('H:i');

        $tcc = $this->renderPartial('_tcc.php', [
            'titulo_trabalho' => $banca->titulo_trabalho,
            'orientador' => $orientador,
            'nota_orientador' => $orientador_id->nota,
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
