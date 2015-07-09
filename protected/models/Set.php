<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 18/04/2015
 * Time: 20:24
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Set extends ActiveRecord
{
    /**
     * @inherit
     *
     * @return string
     */
    public static function tableName()
    {
        return 'sets';
    }

    public function scenarios()
    {
        return [
            'default' => ['setName', 'setLogoUrl', 'setIconUrl', 'setYear'],
            'create' => ['setName', 'setLogoUrl', 'setIconUrl', 'setYear'],
            'update' => ['setName', 'setLogoUrl','setIconUrl',  'setYear'],
            'selectSet' => ['setId'],
        ];
    }

    public function rules()
    {
        return [
            [['setName', 'setLogoUrl', 'setIconUrl', 'setYear'], 'required', 'on' => ['create', 'update']],
            [['setId'], 'required', 'on' => ['selectSet']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'setName' => 'Nom',
            'setYear' => 'Année',
        ];
    }

    /**
     * get $sets or selected one
     *
     * @param null|string $set
     *
     * @return array
     */
    public static function getSets($set= null)
    {
        $res = [
            'magic origins' => 'Magic Origins',
            'dragons of tarkir' => 'Dragons of Tarkir',
            'fate reforged' => 'Fate Reforged',
            'khans of tarkir' => 'Khans of Tarkir',
            'magic 2015 core set' => 'Magic 2015 Core Set',
            'journey into nyx' => 'Journey into Nyx',
            'born of the gods' => 'Born of the Gods',
            'theros' => 'Theros',
            'magic 2014 core set' => 'Magic 2014 Core Set',
            'dragon\'s maze' => 'Dragon\'s Maze',
            'gatecrash' => 'Gatecrash',
            'return to ravnica' => 'Return to Ravnica',
            'magic 2013 core set' => 'Magic 2013',
            'avacyn restored' => 'Avacyn Restored',
            'dark ascension' => 'Dark Ascension',
            'innistrad' => 'Innistrad',
            'magic 2012 core set' => 'Magic 2012',
            'new phyrexia' => 'New Phyrexia',
            'mirrodin besieged' => 'Mirrodin Besieged',
            'scars of mirrodin' => 'Scars of Mirrodin',
            'magic 2011 core set' => 'Magic 2011',
            'rise of the eldrazi' => 'Rise of the Eldrazi',
            'worldwake' => 'Worldwake',
            'zendikar' => 'Zendikar',
            'magic 2010 core set' => 'Magic 2010',
            'alara reborn' => 'Alara Reborn',
            'conflux' => 'Conflux',
            'shards of alara' => 'Shards of Alara',
            'eventide' => 'Eventide',
            'shadowmoor' => 'Shadowmoor',
            'morningtide' => 'Morningtide',
            'lorwyn' => 'Lorwyn',
            'core set tenth edition' => 'Tenth Edition',
            'future sight' => 'Future sight',
            'planar chaos' => 'Planar Chaos',
            'time spiral' => 'Time Spiral',
            'coldsnap' => 'Coldsnap',
            'dissension' => 'Dissension',
            'guildpact' => 'Guildpact',
            'ravnica: city of guilds' => 'Ravnica: City of Guilds',
            'core set ninth edition' => 'Ninth Edition',
            'saviors of kamigawa' => 'Saviors of Kamigawa',
            'betrayers of kamigawa' => 'Betrayers of Kamigawa',
            'champions of kamigawa' => 'Champions of Kamigawa',
            'fifth dawn' => 'Fifth Dawn',
            'darksteel' => 'Darksteel',
            'mirrodin' => 'Mirrodin',
            'core set eighth edition' => 'Eighth Edition',
            'scourge' => 'Scourge',
            'legions' => 'Legions',
            'onslaught' => 'Onslaught',
            'judgment' => 'Judgment',
            'torment' => 'Torment',
            'odyssey' => 'Odyssey',
            'apocalypse' => 'Apocalypse',
            'core set seventh edition' => 'Seventh Edition',
            'planeshift' => 'Planeshift',
            'invasion' => 'Invasion',
            'prophecy' => 'Prophecy',
            'nemesis' => 'Nemesis',
            'mercadian masques' => 'Mercadian Masques',
            'urza\'s destiny' => 'Urza\'s Destiny',
            'urza\'s destiny',
            'classic sixth edition' => 'Sixth Edition',
            'urza\'s legacy' => 'Urza\'s Legacy',
            'urza\'s saga' => 'Urza\'s Saga',
            'exodus' => 'Exodus',
            'stronghold' => 'Stronghold',
            'tempest' => 'Tempest',
            'weatherlight' => 'Weatherlight',
            'fifth edition' => 'Fifth Edition',
            'visions' => 'Visions',
            'mirage' => 'Mirage',
            'alliances' => 'Alliances',
            'homelands' => 'Homelands',
            'ice age' => 'Ice-Age',
            'fourth edition' => 'Fourth Edition',
            'fallen empires' => 'Fallen Empires',
            'the dark' => 'The-Dark',
            'legends' => 'Legends',
            'revised edition' => 'Revised Edition',
            'antiquities' => 'Antiquities',
            'arabian nights' => 'Arabian Nights',
            'alpha, beta and unlimited' => 'Unlimited Edition',
        ];
        if ($set !== null) {
            if ((is_array($set) === true) && (isset($set[0]) === true)) {
                $set = $set[0];
            }
            return $res[$set];
        }
        return $res;
    }

    /**
     * relation with cards
     *
     * @param int $limit
     *
     * @return static
     */
    public function getCards($limit = null)
    {
        return $this->hasMany(Card::className(), ['setId' => 'setId'])
            ->limit($limit)
            ->orderBy('cardNameVO');
    }
}