<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\Session;
use app\models\Usuario;
use app\security\ValidatorRequest;
use Exception;
use Yii;
use yii\web\Response;

/**
 * Controller que gerencia toda a parte de login/logout do sistema.
 */
class LoginController extends \yii\rest\ActiveController
{

    public $modelClass = 'app\models\LoginForm';
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $defaultActions = parent::actions();
        unset($defaultActions['create']);
        return $defaultActions;
    }

    public function behaviors() {
    	$behaviors = parent::behaviors();

		$behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['http://localhost:3000', 'https://sistema-de-defesas.app.ic.ufba.br'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]
        ];

		$behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['http://localhost:3000', 'https://sistema-de-defesas.app.ic.ufba.br'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]

        ];

		$behaviors['authenticator'] = [
			'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
			'except' => [
				'refresh-token',
				'options',
				'login',
			],
		];

		return $behaviors;
	}

    public function actionLogin()
    {
        try {
            $model = new LoginForm();
            $data = Yii::$app->request->post();

            $model->attributes = $data;
            $model->validate();

            if ($model->login()) {
                $user = Yii::$app->user->identity;
                
                $token = $this->generateJwt($user);
                $refresh_token = $this->generateRefreshToken($user);
                
                $id = Yii::$app->user->getId();                

                return \Yii::createObject([
                    'class' => 'yii\web\Response',
                    'format' => \yii\web\Response::FORMAT_JSON,
                    'data' => [
                        'id' => $id,
                        'token' => (string) $token,
						'refresh_token' => $refresh_token->urf_token,
                        'role' => $user->role,
                        'name' => $user->nome,
                    ],
                ]);
            }

            Yii::$app->response->data = $model->errors;
            Yii::$app->response->statusCode = 403;

            return Yii::$app->response->data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        Yii::$app->response->data = $user->errors;
        Yii::$app->response->statusCode = 204;
        return Yii::$app->response->data;
    }

    public function actionRefreshToken() {
		$headers = Yii::$app->request->headers;
		$refresh_token = $headers['refreshtoken'];
		
		if (!$refresh_token) {
			return new \yii\web\UnauthorizedHttpException('No refresh token found.');
		}

		$userRefreshToken = \app\models\UserRefreshToken::findOne(['urf_token' => $refresh_token]);

		if (Yii::$app->request->getMethod() == 'POST') {
			if (!$userRefreshToken) {
				return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
			}

			$user = \app\models\User::find() 
				->where(['id' => $userRefreshToken->urf_userID])
				->andWhere(['not', ['usr_status' => 'inactive']])
				->one();

			if (!$user) {
				$userRefreshToken->delete();
				return new \yii\web\UnauthorizedHttpException('The user is inactive.');
			}

			$token = $this->generateJwt($user);

			return [
				'status' => 'ok',
				'token' => (string) $token,
			];

		} elseif (Yii::$app->request->getMethod() == 'DELETE') {
			if ($userRefreshToken && !$userRefreshToken->delete()) {
				return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
			}

			return ['status' => 'ok'];
		} else {
			return new \yii\web\UnauthorizedHttpException('The user is inactive.');
		}
	}

    private function generateJwt(\app\models\User $user) {
		$jwt = Yii::$app->jwt;
		$signer = $jwt->getSigner('HS256');
		$key = $jwt->getKey();
		$time = time();

		$jwtParams = Yii::$app->params['jwt'];

		return $jwt->getBuilder()
			->issuedBy($jwtParams['issuer'])
			->permittedFor($jwtParams['audience'])
			->identifiedBy($jwtParams['id'], true)
			->issuedAt($time)
			->expiresAt($time + $jwtParams['expire'])
			->withClaim('uid', $user->id)
			->getToken($signer, $key);
	}

	/**
	 * @throws yii\base\Exception
	 */
	private function generateRefreshToken(\app\models\User $user, \app\models\User $impersonator = null): \app\models\UserRefreshToken {
		$refreshToken = Yii::$app->security->generateRandomString(200);

		$userRefreshToken = new \app\models\UserRefreshToken([
			'urf_userID' => $user->id,
			'urf_token' => $refreshToken,
			'urf_ip' => Yii::$app->request->userIP,
			'urf_user_agent' => Yii::$app->request->userAgent,
			'urf_created' => gmdate('Y-m-d H:i:s'),
		]);
		if (!$userRefreshToken->save()) {
			throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $userRefreshToken->getErrorSummary(true));
		}

		Yii::$app->response->cookies->add(new \yii\web\Cookie([
			'name' => 'refresh-token',
			'value' => $refreshToken,
			'httpOnly' => true,
			'sameSite' => 'none',
			'secure' => true,
			'path' => '/login/refresh-token',  
		]));

		return $userRefreshToken;
	}
}
