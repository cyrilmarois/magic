<?php

use app\models\Card;
use yii\db\Schema;
use yii\db\Migration;
use yii\db\Exception;

class m150820_112357_addColorCard extends Migration
{
    protected $color = [
        'b',
        'g',
        'r',
        'u',
        'w',
    ];

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        //$this->addColumn('cards', 'cardColor', Schema::TYPE_STRING);

        $cards = Card::find()->all();
        foreach($cards as $card) {
            //$card->scenario = 'update';
            $color = null;
            $cardCosts = str_split($card->cardCost);
            foreach($cardCosts as $cardCost) {
                if (in_array($cardCost, $this->color) === true) {
                    if (isset($color) === false) {
                        $color = $cardCost;
                    } elseif ($color !== $cardCost) {
                        $color = 'o';
                    }
                }
            }
            $this->update('cards', ['cardColor' => $color], ['cardColor' => null, 'cardId' => $card->cardId]);
        }
    }
    
    public function safeDown()
    {
    }
}
