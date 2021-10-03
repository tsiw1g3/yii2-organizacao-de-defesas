<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banca_documento".
 *
 * @property int $id
 * @property int $id_banca
 * @property string $path
 * @property string $descricao
 * @property string|null $url_repositorio
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
            [['id_banca', 'path', 'descricao'], 'required'],
            [['id_banca'], 'integer'],
            [['descricao'], 'string'],
            [['path', 'url_repositorio'], 'string', 'max' => 255],
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
            'path' => 'Path',
            'descricao' => 'Descricao',
            'url_repositorio' => 'Url Repositorio',
        ];
    }
}
