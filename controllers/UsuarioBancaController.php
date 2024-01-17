<?php

namespace app\controllers;

use app\models\Banca;
use app\models\Usuario;
use app\models\UsuarioBanca;
use app\security\ValidatorRequest;
use yii\helpers\Url;
use Exception;
use Yii;
use yii\db\Query;
use bitcko\googlecalendar\GoogleCalendarApi;

/**
 * Controller que gerencia as relações dos usuários com as bancas.
 */
class UsuarioBancaController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\UsuarioBanca';

    protected $role_allowed = [
        'aluno',
        'orientador',
        'co-orientador',
        'avaliador'
    ];


    protected $role_participants = [
        'aluno' => 1,
        'orientador' => 1,
        'co-orientador' => 1,
        'avaliador' => 2
    ];

    public function beforeAction($action)
    {
        if ($action->id == 'allow-cors' || $action->id == 'usuarios-banca-by-banca') {
            $this->enableCsrfValidation = false;
            return parent::beforeAction($action);
        }

        $permission = ValidatorRequest::validatorHeader(Yii::$app->request->headers);
        if (!$permission) {
            throw new \yii\web\ForbiddenHttpException('Voce nao tem permissao para acessar esta pagina', 403);
        }
        return parent::beforeAction($action);
    }

    public function actionAllowCors() {}

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        unset($defaultActions['create']);
        unset($defaultActions['delete']);
        return $defaultActions;
    }

    public function actionAdd($id)
    {
        try {
            $model = new UsuarioBanca();

            $banca = $this->findBancaById($id);

            $data = Yii::$app->request->post();
            $model->attributes = $data;

            $user = $this->findUserById($model->id_usuario);
            $model->id_banca = $id;
            
            $alluser_bancas = UsuarioBanca::find()->where(["id_usuario" => $model->id_usuario])->all();
            
            //valida se o usuário ja possui uma banca 1 hora antes ou depois da banca atual
            $date = new \DateTime($banca['data_realizacao']);
            foreach($alluser_bancas as $ub){
                $user_banca = Banca::findOne($ub['id_banca']);
                $date_banca = new \DateTime($user_banca['data_realizacao']);
                $interval = $date->diff($date_banca);
                if($interval->format("%d") == 0 && $interval->format("%m") == 0 && $interval->format("%y") == 0 && 
                $interval->format("%i") == 0 && $interval->format("%s") == 0 && $interval->format("%h") <= 1){
                    throw new \yii\web\ForbiddenHttpException('O Usuário já está cadastrado em uma banca neste horário.', 403);
                }
            }

            // $aux = $this->findUsuarioBancaByBanca($id, $model->id_usuario);
            // return empty($aux);
            // Valida se o usuário já esta na banca
            if ($this->findUsuarioBancaByBanca($id, $model->id_usuario)) {
                throw new \yii\web\ForbiddenHttpException('Usuário já cadastrado na banca.', 403);
            }

            if (!$this->validateRole($model)) {
                throw new \yii\web\ForbiddenHttpException($model->role . ' nao permitida.', 403);
            }

            if (!$this->validateParticipants($model)) {
                throw new \yii\web\ForbiddenHttpException('Limite de ' . $model->role . ' atingido', 403);
            }


            if ($model->validate() && $model->save()) {
                return [];
            }

            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 422;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionId($id_banca, $id_usuario)
    {
        return $this->findUbByUserAndBanca($id_usuario, $id_banca);
    }

    public function actionNota($id_banca)
    {
        $users = $this->findUsuariosBancaByBanca($id_banca);
        $cnt = 0;
        $sum = 0.0;
        foreach ($users as $user) {
            $nota = $user->nota;
            if ($nota != null) {
                $sum += $user->nota;
                $cnt++;
            }
        }
        if ($cnt == 0) {
            return "Nenhuma nota foi cadastrada";
        }
        return $sum / $cnt;
    }

    public function actionGiveScore($id_banca, $id_user)
    {
        try{
            $user_id = $id_user != 0 ? $id_user : Yii::$app->user->getId();
            $model = UsuarioBanca::find()->where(['id_banca' => $id_banca, 'id_usuario' => $user_id])->one();
            $data = Yii::$app->request->post();
            if ($model !== null) {
                $model->nota = $data['nota'];
                if ($model->validate()) {
                    $model->save();
                    return "Nota atualizada com sucesso";
                }
            }
            // Caso a validacao falhe, lançar erros para o front
            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 422;
            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }

        
    }

    public function actionUsuariosBancaByBanca($id_banca) {
        $query = (new \yii\db\Query())
        ->select(['usuario_banca.id_usuario AS id', 'usuario_banca.role', 'usuario_banca.nota', 'usuario.nome', 'usuario.username'])
        ->from('usuario_banca')
        ->innerJoin('usuario', 'usuario_banca.id_usuario = usuario.id')
        ->innerJoin('banca', 'banca.id = usuario_banca.id_banca')
        ->where("usuario_banca.id_banca = $id_banca")
        ->andWhere(['<>','banca.visible', "0"])
        ->all();
        return $query;
    }
    
    public function CreateEvent($banca){
        $redirectUrl = Url::to(['/google-calendar/auth'], true);
        $calendarId = '1dimt5iv0ba88goaephucfrmqo@group.calendar.google.com';
        $username="any_name";
        $googleApi = new GoogleCalendarApi($username, $calendarId, $redirectUrl);
        $date = new \DateTime($banca['data_realizacao'], new \DateTimeZone("America/Sao_paulo"));
        $dateEnd = new \DateTime($banca['data_realizacao'], new \DateTimeZone("America/Sao_paulo"));
        $dateEnd->add(new \DateInterval('PT1H'));
        if(!$googleApi->checkIfCredentialFileExists()){
            $googleApi->generateGoogleApiAccessToken();
        }
        if($googleApi->checkIfCredentialFileExists()){
            $event = array(
                'summary' => $banca['titulo_trabalho'],
                'location' => $banca['local'],
                'description' => 'Defesa de TCC da banca ' . $banca['titulo_trabalho'] . ' feita pelo aluno ' . $banca['autor'],
                'start' => array(
                    'dateTime' => $date->format(\DateTime::RFC3339),
                    'timeZone' => 'America/Sao_Paulo',
                ),
                'end' => array(
                    'dateTime' => $dateEnd->format(\DateTime::RFC3339),
                    'timeZone' => 'America/Sao_Paulo',
                ),
                'recurrence' => array(
                    'RRULE:FREQ=DAILY;COUNT=2'
                ),
                'attendees' => array(
                    // array('email' => 'lpage@example.com'),
                    // array('email' => 'sbrin@example.com'),
                ),
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                        array('method' => 'email', 'minutes' => 24 * 60),
                        array('method' => 'popup', 'minutes' => 10),
                    ),
                ),
            );
            
            $calEvent = $googleApi->createGoogleCalendarEvent($event);            
            return $calEvent->htmlLink;
        }else{
            return $this->redirect(['auth']);
        }
    }

    public function actionSendEmail(){
        try{
            $data = Yii::$app->request->post();
            $banca = Banca::findOne($data['banca']);
            $user = Usuario::findOne($banca['user_id']);
            $users_banca = UsuarioBanca::find()->where(['id_banca' => $banca['id']])->all();
            $avaliadores = array();
            if($users_banca){
                foreach($users_banca as $avaliador){
                    $user = Usuario::findOne($avaliador['id_usuario']);
                    array_push($avaliadores, $user["nome"]);
                }
            }
            $inviteLink = $this->CreateEvent($banca);
            $emails = explode(",", $data['emails']);
            $message = Yii::$app->mailer->compose('emailTemplate', 
            [
                'banca' => $banca, 
                'content' => $data['mensagem'],
                'orientador' => $user->nome,
                'avaliadores' => $avaliadores,
                'invite_google' => $inviteLink, 
            ]);
            $message->setFrom('sistemadedefesasufba@gmail.com');
            $message->setTo($emails);
            $message->setSubject($data['assunto']);
            $message->send();
            return "Email enviado com sucesso!";
        } catch (Exception $e) {            
            throw $e;
        }
    }

    protected function validateRole($model)
    {
        if (!in_array($model->role, $this->role_allowed)) {
            return false;
        }

        return true;
    }

    protected function validateParticipants($model)
    {
        $count = UsuarioBanca::find()->where(['role' => $model->role, 'id_banca' => $model->id_banca])->count();
        return $this->role_participants[$model->role] <= $count ? false : true;
    }

    protected function findUbByUserAndBanca($idUsuario, $idBanca)
    {
        if (($ub = UsuarioBanca::findOne(['id_banca' => $idBanca, 'id_usuario' => $idUsuario]))) {
            return $ub;
        }

        throw new \yii\web\NotFoundHttpException('O usuário informado não existe.', 404);
    }

    protected function findUserById($id)
    {
        if (($user = Usuario::findOne($id)) !== null) {
            return $user;
        }

        throw new \yii\web\NotFoundHttpException('O usuário informado não existe.', 404);
    }

    protected function findBancaById($id)
    {
        if (($banca = Banca::findOne($id)) !== null) {
            return $banca;
        }

        throw new \yii\web\NotFoundHttpException('A banca informada não existe.', 404);
    }

    protected function findUsuariosBancaByBanca($banca)
    {
        if (($models = UsuarioBanca::find()->where(['id_banca' => $banca])->all()) !== null) {
            return $models;
        }

        throw new \yii\web\NotFoundHttpException('Nao existem usuarios para esta banca.', 404);
    }

    protected function findUsuarioBancaByBanca($banca, $user)
    {
        if (($models = UsuarioBanca::find()->where(['id_banca' => $banca, 'id_usuario' => $user])->all()) !== null) {
            return !empty($models);
        }

        return false;
    }
}
