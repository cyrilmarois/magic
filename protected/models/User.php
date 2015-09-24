<?php

namespace app\models;

use \yii\db\ActiveRecord;
use Yii;
use app\models\Deck;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $userPasswordCheck;

    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $userEmail
     * @return static|null
     */
    public static function findByUserEmail($userEmail)
    {
        return static::find()->where(['userEmail' => $userEmail])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->userAuthKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($userAuthKey)
    {
        return $this->userAuthKey === $userAuthKey;
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['userFirstname', 'userLastname', 'userNickname', 'userEmail', 'userPassword', 'userAuthKey'],
            'create' => ['userNickname', 'userEmail', 'userPassword', 'userPasswordCheck'],
            'login' => ['userEmail', 'userPassword'],
            'facebook' => ['userFirstname', 'userLastname', 'userNickname', 'userEmail', 'userPassword', 'userAuthKey'],
        ];
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['userEmail', 'email'],
            ['userEmail', 'unique', 'on' => ['create']],
            [['userPassword', 'userPasswordCheck'], 'string', 'min' => 6],
            [['userFirstname', 'userLastname', 'userNickname', 'userPassword', 'userPasswordCheck', 'userToken', 'userAuthKey'], 'string'],
            [['userNickname', 'userEmail', 'userPassword', 'userPasswordCheck'], 'required', 'on' => ['create']],
            ['password', 'compare', 'compareAttribute' => 'passwordCheck', 'when' => function($model) {
                return $model->userPassword !== null;
            }],

            //login scenario
            [['userEmail', 'userPassword'], 'required', 'on' => ['login']],
            ['userEmail', 'required', 'on' => ['facebook']],
        ];
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'userFirstname' => 'Prénom',
            'userLastname' => 'Nom',
            'userNickname' => 'Pseudo',
            'userEmail' => 'Email',
            'userPassword' => 'Mot de passe',
            'userPasswordCheck' => 'Confirmation du mot de passe',
        ];
    }

    /**
     * hash password to store
     *
     * @return void
     */
    public function hashPassword()
    {
        $this->userPassword = Yii::$app->getSecurity()->generatePasswordHash($this->userPassword);
    }

    /**
     * validate password input with stored
     *
     * @param $password
     * @param $hash
     * @throws \yii\base\InvalidConfigException
     *
     * @return bool
     */
    public function validatePassword($password, $hash)
    {
        if (Yii::$app->getSecurity()->validatePassword($password, $hash) === false) {
            return false;
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) === true) {
            $currentDateTime = Yii::$app->formatter->asDateTime(strtotime('NOW'), date('Y-m-d H:i:s'));

            if ($this->isNewRecord === true) {
                $this->userDateCreate = $currentDateTime;
                $this->userStatus = self::STATUS_ACTIVE;
            } else {
                $this->userDateUpdate = $currentDateTime;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the deck of the user
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDecks()
    {
        return $this->hasMany(Deck::className(), ['deckId' => 'deckId']);
    }
}
