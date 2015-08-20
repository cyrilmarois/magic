<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\Card;

class m150820_112357_addColorCard extends Migration
{
    protected $color = [
        'b',
        'g',
        'o',
        'r',
        'u',
        'w',
    ];

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('cards', 'cardColor', Schema::TYPE_STRING);

        $color = null;
        $cards = Card::find()->all();
        foreach($cards as $card) {
            $cardCosts = str_split($card->cardCost);
            foreach($cardCosts as $cardCost) {
                if (in_array($cardCost, $this->color) === true) {
                    if (isset($color) === false) {
                        $color = $cardCost;
                    } elseif ($color !== $cardCost) {
                        $color = $this->color[2];
                    }
                }
            }

            var_dump($card->cardNameVO, $color);

        }
    }
    
    public function safeDown()
    {
    }
}
