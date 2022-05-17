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
 * @property string $disciplina
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
            [['titulo_trabalho', 'resumo', 'abstract', 'palavras_chave', 'data_realizacao', 'curso', 'disciplina', 'autor', 'turma', 'user_id', 'ano', 'semestre_letivo', 'matricula'], 'required'],
            [['resumo', 'abstract', 'palavras_chave', 'tipo_banca'], 'string'],
            [['data_realizacao'], 'safe'],
            [['nota_final'], 'number'],
            [['titulo_trabalho', 'local', 'curso', 'disciplina', 'autor', 'turma', 'ano' , 'semestre_letivo', 'matricula'], 'string', 'max' => 255],
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
            'disciplina' => 'Disciplina',
            'autor' => 'Autor',
            'turma' => 'Turma',
            'user_id' => 'User Id',
            'ano' => 'Ano',
            'semestre_letivo' => 'Semestre Letivo',
            'matricula' => 'Matrícula',
        ];
    }
}
