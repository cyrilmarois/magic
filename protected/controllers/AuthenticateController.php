<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 11:55
 */

namespace app\controllers;

use app\models\User;
use app\components\XWebUser;
use \yii\filters\AccessControl;
use \yii\web\Controller;
use Yii;

class AuthenticateController extends Controller
{
    /**
     * register and log user in
     *
     * @return null|string|\yii\web\Response
     * @throws Exception
     * @throws \Exception
     */
    public function actionSignin()
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);
            $response = null;
            $formUser = new User(['scenario' => 'login']);

            if (($formUser->load($_POST) === true) && ($formUser->validate() === true)) {
                $status = XWebUser::authenticate($formUser->userEmail, $formUser->userPassword);
                if (($status === XWebUser::NO_ERROR) && (Yii::$app->user->login($formUser, 3600 * 24) === true)) {
                    $response = $this->redirect(['/card/index']);
                } elseif($status === XWebUser::UNKNOWN_USER_ERROR) {
                    $formUser->addError('userEmail', 'Utilisateur inconnu');
                } elseif($status === XWebUser::USER_PASSWORD_ERROR) {
                    $formUser->addError('userPassword', 'Mot de passe invalide');
                }
            }
            if ($response === null) {
                $response = $this->render('signin', [
                    'user' => $formUser,
                ]);
            }

            return $response;
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * register and log user in
     *
     * @return null|string|\yii\web\Response
     * @throws Exception
     * @throws \Exception
     */
    public function actionSignup()
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);
            $response = null;
            $formUser = new User(['scenario' => 'create']);

            if (($formUser->load($_POST) === true) && ($formUser->validate() === true)) {
                $user = new User();
                $user->attributes = $formUser->attributes;
                $user->hashPassword($user->userPassword);

                $now = Yii::$app->formatter->asDateTime('now', 'php:Y-m-d H:i:s');
                $user->userDateCreate = $now;
                $user->userDateLogin = $now;

                $status = $user->save(false);
                if ($status === true) {
                    $status = XWebUser::authenticate($user->userEmail, $formUser->userPassword);
                    if (($status === XWebUser::NO_ERROR) && (Yii::$app->user->login($user, 3600*24) === true)) {
                        $response = $this->redirect([Yii::$app->user->returnUrl]);
                    }
                }
            }
            if ($response === null) {
                $response = $this->render('signup', [
                    'user' => $formUser,
                ]);
            }

            return $response;
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * log the user out
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => false,
                        'actions' => ['index'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => false,
                        'actions' => ['logout'],
                        'roles' => ['?'],
                    ],
                ]
            ],
        ];
    }
}