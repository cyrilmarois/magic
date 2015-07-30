<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 14:35
 */

namespace app\widgets;

use app\models\Deck;
use yii\base\Widget;
use Yii;

class Sidebar extends Widget
{
    private $decks = [];

    public function init()
    {
        parent::init();
        $this->decks = Deck::find()->where(['userId' => Yii::$app->user->identity->userId])->all();
    }

    public function run()
    {
        return $this->render('sidebar', [
            'decks' => $this->decks,
        ]);
    }
}