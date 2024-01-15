<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuario".
 *
 * @property int $id
 * @property string $username
 * @property string $password_has
 * @property string $auth_key
 * @property string $email
 * @property string $nome
 * @property string $school
 * @property string $academic_title
 * @property string|null $lattesUrl
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $role
 * @property int $pronoun
 */
class Usuario extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password_has', 'auth_key', 'email', 'school', 'academic_title', 'status', 'created_at', 'updated_at', 'nome', 'role'], 'required'],
            [['created_at', 'updated_at', 'pronoun', 'registration_id'], 'safe'],
            [['username', 'email', 'auth_key'], 'unique'],
            [['password_has', 'username', 'email'], 'default'],
            [['username', 'password_has', 'auth_key', 'nome'], 'string', 'max' => 255],
            [['email', 'school', 'academic_title', 'lattesUrl'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password_has' => 'Password Has',
            'auth_key' => 'Auth Key',
            'email' => 'Email',
            'nome' => 'Nome',
            'school' => 'School',
            'academic_title' => 'Academic Title',
            'lattesUrl' => 'Lattes Url',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'role' => 'Role',
            'pronoun' => 'Pronoun',
            'registration_id' => 'Registration ID'
        ];
    }

    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'nome',
            'universidade' => 'school',
            'titulo_academico' => 'academic_title',
            'link_latters' => 'lattesUrl',
            'role' => 'role',
            'pronoun' => 'pronoun',
            'status',
            'registration_id'
        ];
    }

    /**
     * Localiza uma identidade pelo ID informado
     *
     * @param string|int $id o ID a ser localizado
     * @return IdentityInterface|null o objeto da identidade que corresponde ao ID informado
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Localiza uma identidade pelo token informado
     *
     * @param string $token o token a ser localizado
     * @return IdentityInterface|null o objeto da identidade que corresponde ao token informado
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * @return int|string o ID do usuário atual
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|string a Role do usuário atual
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return int|string o Email do usuário atual
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string a chave de autenticação do usuário atual
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool se a chave de autenticação do usuário atual for válida
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
