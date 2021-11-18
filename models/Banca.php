<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banca".
 *
 * @property int $id
 * @property string $titulo_trabalho
 * @property string $resumo
 * @property string $tipo_banca
 * @property string $abstract
 * @property string $palavras_chave
 * @property string $data_realizacao
 * @property float|null $nota_final
 * @property string|null $local
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
            [['titulo_trabalho', 'resumo', 'abstract', 'palavras_chave', 'data_realizacao', 'tipo_banca'], 'required'],
            [['resumo', 'abstract', 'palavras_chave', 'tipo_banca'], 'string'],
            [['data_realizacao'], 'safe'],
            [['nota_final'], 'number'],
            [['titulo_trabalho', 'local'], 'string', 'max' => 255],
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
        ];
    }
}
