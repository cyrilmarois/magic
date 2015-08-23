<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 18/04/2015
 * Time: 11:40
 */

namespace app\models;

use app\components\Utilities;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use Yii;

class Card extends ActiveRecord
{
    const DEFAULT_PICTURE_CARD = 'defaultCard.jpg';
    public $cardNumber = 0;

    /**
     * @inherit
     *
     * @return string
     */
    public static function tableName()
    {
        return 'cards';
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => [
                'cardId', 'cardNameVO', 'cardNameVF', 'cardDescriptionVO', 'cardDescriptionVF', 'cardType', 'cardMonsterEnergy',  'cardCost', 'cardPictureVO', 'cardPictureVF', 'cardRarity', 'setId',
            ],
            'create' => [
                'cardNameVO', 'cardNameVF', 'cardDescriptionVO', 'cardDescriptionVF', 'cardType', 'cardMonsterEnergy', 'cardCost', 'cardPictureVO', 'cardPictureVF', 'cardRarity', 'setId',
            ],
            'update' => [
                'cardNameVO', 'cardNameVF', 'cardDescriptionVO', 'cardDescriptionVF', 'cardType', 'cardMonsterEnergy', 'cardCost', 'cardPictureVO', 'cardPictureVF', 'cardRarity', 'setId',
            ],
        ];
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function rules() {
        return [
            [['cardId', 'setId'], 'integer'],
            [['cardYear'], 'date'],
            [['cardId', 'cardNameVO', 'cardType', 'cardDescriptionVO', 'cardCost', 'cardPictureVO', 'cardRarity', 'setId'], 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * get the set by relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSet()
    {
        return $this->hasOne(\app\models\Set::className(), ['setId' => 'setId']);
    }

    /**
     * @return string
     */
    public function getCostPictures()
    {
        $str = '';
        $manaPictos = Utilities::getMana();
        $strlen = strlen($this->cardCost);
        for ($i = 0; $i < $strlen; $i++) {
            $str .= ($i === 0) ? Html::img($manaPictos[$this->cardCost[$i]]) : '.'.Html::img($manaPictos[$this->cardCost[$i]]);
        }
        return $str;
    }

    /**
     * return total cost of a card
     *
     * @return integer
     */
    public function getTotalCost()
    {
        $totalCost = 0;
        $mana = ['b', 'u', 'g', 'r', 'w'];
        $arrayCost = str_split($this->cardCost);
        foreach($arrayCost as $cost) {
            if ($cost === 'x') {
                $cost = 0;
            } elseif (in_array($cost, $mana) === false) {
                $cost = (int)$cost;
            } else {
                $cost = 1;
            }
            $totalCost += $cost;
        }

        return $totalCost;
    }

    /**
     * return picture path
     *
     * @return bool|string
     */
    public function getPicture()
    {
        $set = Set::findOne($this->setId);
        $setName = Utilities::specialClean($set->setName);
        return Yii::getAlias('@web/images/cards/'.$setName.'/'.$this->cardPictureVO);
    }

    /**
     * @return array
     */
    public static function getCardTypes()
    {
        return [
            'creatures' => [
                'artifact creature' => 'artifact creature',
                'creature' => 'creature',
                'enchantment creature' => 'enchantment creature',
                'land creature' => 'land creature',
                'legendary artifact creature' => 'legendary artifact creature',
                'legendary creature' => 'legendary creature',
                'legendary enchantment creature' => 'legendary enchantment creature',
                'planeswalker' => 'planeswalker',
                'snow creature' => 'snow creature',
            ],
            'lands' => [
                'artifact land' => 'artifact land',
                'basic land' => 'basic land',
                'basic snow land' => 'basic snow land',
                'land' => 'land',
                'legendary land' => 'legendary land',
                'legendary snow land' => 'legendary snow land',
                'snow land' => 'snow land',
            ],
            'spells' => [
                'artifact' => 'artifact',
                'enchantment' => 'enchantment',
                'instant' => 'instant',
                'legendary artifact' => 'legendary artifact',
                'legendary enchantment' => 'legendary enchantment',
                'legendary enchantment artifact' => 'legendary enchantment artifact',
                'snow artifact' => 'snow artifact',
                'snow artifact creature' => 'snow artifact creature',
                'snow enchantment' => 'snow enchantment',
                'sorcery' => 'sorcery',
                'tribal artifact' => 'tribal artifact',
                'tribal enchantment' => 'tribal enchantment',
                'tribal instant' => 'tribal instant',
                'tribal sorcery' => 'tribal sorcery',
                'world enchantment' => 'world enchantment',
            ],
        ];
    }

    /**
     * parse description to replace icon text by his symbol
     *
     * @param $str
     * @return mixed the string formatter
     */
    public function replaceDesriptionPicto($str)
    {
        $manas = Utilities::getMana();
        $result = preg_replace([
            '/\/handlers\/image\.ashx\?size=small&amp;name=x&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=tap&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=1&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=2&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=3&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=4&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=5&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=6&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=7&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=8&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=9&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=b&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=g&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=r&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=u&amp;type=symbol/',
            '/\/handlers\/image\.ashx\?size=small&amp;name=w&amp;type=symbol/',
        ], [
            $manas['x'],
            $manas['tap'],
            $manas['1'],
            $manas['2'],
            $manas['3'],
            $manas['4'],
            $manas['5'],
            $manas['6'],
            $manas['7'],
            $manas['8'],
            $manas['9'],
            $manas['b'],
            $manas['g'],
            $manas['r'],
            $manas['u'],
            $manas['w'],
        ],
        $str);

        return $result;
    }
}