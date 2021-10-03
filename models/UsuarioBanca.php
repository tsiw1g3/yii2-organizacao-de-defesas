<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuario_banca".
 *
 * @property int $id
 * @property int $id_usuario
 * @property int $id_banca
 * @property string $role
 * @property float|null $nota
 */
class UsuarioBanca extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario_banca';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_usuario', 'id_banca', 'role'], 'required'],
            [['id_usuario', 'id_banca'], 'integer'],
            [['nota'], 'number'],
            [['role'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_usuario' => 'Id Usuario',
            'id_banca' => 'Id Banca',
            'role' => 'Role',
            'nota' => 'Nota',
        ];
    }
}
