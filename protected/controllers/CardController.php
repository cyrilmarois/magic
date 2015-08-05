<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 27/04/2015
 * Time: 20:43
 */

namespace app\controllers;

use app\models\Card;
use app\models\Deck;
use app\models\Set;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class CardController extends Controller
{
    /**
     * display all sets
     *
     * @return string
     */
    public function actionIndex()
    {
        try {
            Yii::trace('Trace :' . __METHOD__, __METHOD__);

            $query = Set::find();
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } catch (Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * display cards from set
     *
     * @param string $setId the set id of the cards
     * @param string $displayMode the displayMode
     *
     * @return string
     */
    public function actionListCards($setId, $displayMode = 'list')
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $decksName = [];
            if (Yii::$app->user->identity !== null) {
                $decks = Deck::find()->where(['userId' => Yii::$app->user->identity->userId])->orderBy('deckName')->all();
                foreach($decks as $deck) {
                    $decksName[$deck->deckId] = $deck->deckName;
                }
            }

            $setName = null;
            $set = Set::findOne($setId);
            if ($set !== null) {
                $htmlSets = Set::getSets();
                $setName = $htmlSets[$set->setName];
                $cards = $set->getCards(50)->all();
            }

            $viewMode = '_list';
            if ($displayMode === 'details') {
                $viewMode = '_details';
            }

            return $this->render('list', [
                'set' => $set,
                'setName' => $setName,
                'cards' => $cards,
                'viewMode' => $viewMode,
                'decksName' => $decksName,
                'offset' => 50,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * @param $cardId
     * @return string
     * @throws Exception
     */
    public function actionView($cardId)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $card = Card::findOne($cardId);
            if ($card === null) {
                throw new NotFoundHttpException();
            }

            return $this->render('view', [
                'card' => $card,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
}