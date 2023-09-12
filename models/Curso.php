<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "curso".
 *
 * @property string $nome
 * @property string $sigla
 * @property string $coordenacao
 * @property string $cargo_coordenacao
 */
class Curso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'curso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'sigla', 'coordenacao', 'cargo_coordenacao'], 'required'],            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nome' => 'Nome',
            'sigla' => 'Sigla',
            'coordenacao' => 'Coordenação',
            'cargo_coordenacao' => 'Coordenação',
        ];
    }
}
