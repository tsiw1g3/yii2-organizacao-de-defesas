<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banca".
 *
 * @property int $id
 * @property int $user_id
 * @property string $titulo_trabalho
 * @property string $resumo
 * @property string $tipo_banca
 * @property string $curso
 * @property string $autor
 * @property int $pronome_autor
 * @property string $abstract
 * @property string $palavras_chave
 * @property string $data_realizacao
 * @property float|null $nota_final
 * @property string $local
 * @property string $ano
 * @property string $semestre_letivo
 */
class Banca extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo_trabalho', 'resumo', 'abstract', 'palavras_chave', 'data_realizacao', 'curso', 'autor', 'pronome_autor', 'turma', 'user_id', 'ano', 'semestre_letivo', 'matricula', 'visible'], 'required'],
            [['resumo', 'abstract', 'palavras_chave', 'tipo_banca'], 'string'],
            [['curso', 'nota_final', 'visible', 'ano'], 'number'],
            [['data_realizacao'], 'safe'],
            [['titulo_trabalho', 'local', 'autor', 'turma', 'semestre_letivo', 'matricula'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo_trabalho' => 'Titulo Trabalho',
            'resumo' => 'Resumo',
            'abstract' => 'Abstract',
            'palavras_chave' => 'Palavras Chave',
            'data_realizacao' => 'Data Realizacao',
            'nota_final' => 'Nota Final',
            'tipo_banca' => 'Tipo Banca',
            'local' => 'Local',
            'curso' => 'Curso',
            'autor' => 'Autor',
            'pronome_autor' => 'Pronome Autor',
            'turma' => 'Turma',
            'user_id' => 'User Id',
            'ano' => 'Ano',
            'semestre_letivo' => 'Semestre Letivo',
            'matricula' => 'Matrícula',
        ];
    }
}
