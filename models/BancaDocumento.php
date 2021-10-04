<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banca_documento".
 *
 * @property int $id
 * @property int $id_banca
 * @property int $id_documento
 */
class BancaDocumento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banca_documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_banca', 'id_documento'], 'required'],
            [['id_banca', 'id_documento'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_banca' => 'Id Banca',
            'id_documento' => 'Id Documento',
        ];
    }
}
