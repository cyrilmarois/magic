<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 18:34
 */

namespace app\models;

use app\components\Utilities;
use app\models\User;
use \yii\db\ActiveRecord;
use Yii;

class Deck extends ActiveRecord
{
    /**
     * @inherit
     *
     * @return string
     */
    public static function tableName()
    {
        return 'decks';
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['deckName'],
            'create' => ['deckName', 'deckColor'],
            'update' => ['deckName', 'deckColor'],
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
            [['deckName'], 'string'],
            [['deckName', 'deckColor'], 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * @inherit
     *
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) === true) {

            $currentDateTime = Yii::$app->formatter->asDateTime(strtotime('NOW'), date('Y-m-d H:i:s'));
            if ($this->isNewRecord === true) {
                $this->deckDateCreate = $currentDateTime;
            } else {
                $this->deckDateUpdate = $currentDateTime;
            }
            return true;
        } else {
            return false;
        }

    }

    /**
     * get deck's owner
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['userId' => 'userId']);
    }

    /**
     * get cards of the deck
     *
     * @return static
     */
    public function getCards()
    {
        return $this->hasMany(Card::className(), ['cardId' => 'cardId'])
            ->viaTable('decksCards', ['deckId' => 'deckId']);
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'deckName' => 'Nom',
            'deckColor' => 'Couleur'
        ];
    }

    /**
     * get magic deck color
     *
     * @return array
     */
    public static function getColors()
    {
        $colors = ['b', 'g', 'r', 'u', 'w'];
        $manas = Utilities::getMana();
        $res = [];
        foreach($colors as $color) {
            if (isset($manas[$color]) === true) {
                $res[$color] = $manas[$color];
            }
        }

        return $res;
    }
}