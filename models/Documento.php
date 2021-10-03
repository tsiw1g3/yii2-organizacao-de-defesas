<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento".
 *
 * @property int $id
 * @property string $path
 * @property int $id_banca
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
            [['path', 'id_banca', 'status', 'data_submissao'], 'required'],
            [['id_banca'], 'integer'],
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
            'id_banca' => 'Id Banca',
            'status' => 'Status',
            'data_submissao' => 'Data Submissao',
        ];
    }
}
