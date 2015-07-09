<?php

use yii\db\Schema;
use yii\db\Migration;

class m150424_174008_init extends Migration
{
    public function safeUp()
    {
        $this->createTable('cards', [
            'cardId' => Schema::TYPE_PK,
            'cardNameVO' => Schema::TYPE_STRING . ' NOT NULL',
            'cardNameVF' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'cardType' => Schema::TYPE_STRING . ' NOT NULL',
            'cardMonsterEnergy' => Schema::TYPE_STRING . ' NOT NULL',
            'cardDescriptionVO' => Schema::TYPE_TEXT . ' NOT NULL',
            'cardDescriptionVF' => Schema::TYPE_TEXT . ' NULL DEFAULT NULL',
            'cardCost' => Schema::TYPE_STRING . ' NOT NULL',
            'cardPictureVO' => Schema::TYPE_STRING . ' NOT NULL',
            'cardPictureVF' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'cardRarity' => Schema::TYPE_STRING . ' NOT NULL',
            'setId' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->createTable('sets', [
            'setId' => Schema::TYPE_PK,
            'setName' => Schema::TYPE_STRING . ' NOT NULL',
            'setLogoUrl' => Schema::TYPE_STRING . ' NOT NULL',
            'setIconUrl' => Schema::TYPE_STRING . ' NOT NULL',
            'setYear' => 'YEAR NOT NULL',
        ]);

        $this->createTable('deck', [
            'deckId' => Schema::TYPE_PK,
            'deckName' => Schema::TYPE_STRING . ' NOT NULL',
            'deckColor' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'deckDateCreate' => Schema::TYPE_DATETIME . ' NOT NULL',
            'deckDateUpdate' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'userId' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->createTable('decksCards', [
            'deckId' => Schema::TYPE_INTEGER . ' NOT NULL',
            'cardId' => Schema::TYPE_INTEGER . ' NOT NULL',
            'cardNumber' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->createTable('users', [
            'userId' => Schema::TYPE_PK,
            'userFirstname' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'userLastname' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'userNickame' => Schema::TYPE_STRING .  ' UNIQUE NOT NULL',
            'userEmail' => Schema::TYPE_STRING . ' UNIQUE NOT NULL',
            'userPassword' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'userStatus' => Schema::TYPE_BOOLEAN . ' DEFAULT 0',
            'userDateCreate' => Schema::TYPE_DATETIME . ' NOT NULL',
            'userDateUpdate' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'userDateLogin' => Schema::TYPE_DATETIME . ' NULL DEFAULT NULL',
            'userAuthKey' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'userToken' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ]);

        $this->addForeignKey('cardSetId', 'cards', 'setId', 'sets', 'setId', 'CASCADE', 'CASCADE');
        $this->addForeignKey('deckUserId', 'deck', 'userId', 'users', 'userId', 'CASCADE', 'CASCADE');
        $this->addForeignKey('cardDeckId', 'decksCards', 'deckId', 'deck', 'deckId', 'CASCADE', 'CASCADE');
        $this->addForeignKey('decksCardId', 'decksCards', 'cardId', 'cards', 'cardId', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m150424_174008_init cannot be reverted.\n";

        return false;
    }
}
