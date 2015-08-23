<?php

namespace app\components;

use yii\helpers\Html;
use Yii;

class Utilities
{
    const GATHERED_WIZARD_URL = 'http://gatherer.wizards.com/';

    /**
     * trim and strtolower
     *
     * @param $str
     *
     * @return string
     */
    public static function cleanStr($str, $hasToLower = true)
    {
        $str = trim($str);
        if ($hasToLower === true) {
            $str = strtolower($str);
        }
        return $str;
    }

    /**
     * clean quotes (for urzas sets especially)
     *
     * @param $str
     *
     * @return string
     */
    public static function cleanQuotes($str)
    {
        $htmlSetName = str_replace('&rsquo;', '\'', $str);
        return self::cleanStr($htmlSetName);
    }

    /**
     * get mana and picture associate
     *
     * @return array
     */
    public static function getMana()
    {
        return [
            'b' => Yii::getAlias('@web/images/mana/black.png'),
            'u' => Yii::getAlias('@web/images/mana/blue.png'),
            'g' => Yii::getAlias('@web/images/mana/green.png'),
            'r' => Yii::getAlias('@web/images/mana/red.png'),
            'w' => Yii::getAlias('@web/images/mana/white.png'),
            '0' => '',
            '1' => Yii::getAlias('@web/images/mana/1.gif'),
            '2' => Yii::getAlias('@web/images/mana/2.gif'),
            '3' => Yii::getAlias('@web/images/mana/3.gif'),
            '4' => Yii::getAlias('@web/images/mana/4.gif'),
            '5' => Yii::getAlias('@web/images/mana/5.gif'),
            '6' => Yii::getAlias('@web/images/mana/6.gif'),
            '7' => Yii::getAlias('@web/images/mana/7.gif'),
            '8' => Yii::getAlias('@web/images/mana/8.gif'),
            '9' => Yii::getAlias('@web/images/mana/9.gif'),
            'x' => Yii::getAlias('@web/images/mana/X.gif'),
            'bg' => Yii::getAlias('@web/images/mana/BG.gif'),
            'gu' => Yii::getAlias('@web/images/mana/GU.gif'),
            'gw' => Yii::getAlias('@web/images/mana/GW.gif'),
            'rg' => Yii::getAlias('@web/images/mana/RG.gif'),
            'rw' => Yii::getAlias('@web/images/mana/RW.gif'),
            'ub' => Yii::getAlias('@web/images/mana/UB.gif'),
            'ur' => Yii::getAlias('@web/images/mana/UR.gif'),
            'wb' => Yii::getAlias('@web/images/mana/WB.gif'),
            'wu' => Yii::getAlias('@web/images/mana/WU.gif'),
            'tap' => Yii::getAlias('@web/images/mana/tap.gif'),
        ];
    }

    /**
     * remove special chars and spaces
     *
     * @param $cardName
     *
     * @return clean cardName
     */
    public static function specialClean($name)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $str = str_replace($a, $b, $name);
        return preg_replace(['/(\s|-|_|\'|\/)+/'], ['-'], $str);
    }

    /**
     * @return array
     */
    public static function getColors()
    {
        return [
            'b' => 'black',
            'g' => 'green',
            'o' => 'multicolor',
            'r' => 'red',
            'u' => 'blue',
            'w' => 'white',
        ];
    }
}