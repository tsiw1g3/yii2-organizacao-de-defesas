<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento".
 *
 * @property int $id
 * @property string $path
 * @property string $descricao
 * @property string $status
 * @property string $data_submissao
 */
class Documento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path', 'descricao', 'status', 'data_submissao'], 'required'],
            [['descricao'], 'string'],
            [['data_submissao'], 'safe'],
            [['path'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
            'descricao' => 'Descricao',
            'status' => 'Status',
            'data_submissao' => 'Data Submissao',
        ];
    }
}
