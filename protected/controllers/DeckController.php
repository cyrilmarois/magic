<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 12:34
 */

namespace app\controllers;

use app\components\Utilities;
use app\models\Card;
use app\models\Deck;
use app\models\Set;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

class DeckController extends Controller
{
    /**
     * display all deck of the current user
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Deck::find()->where(['userId' => Yii::$app->user->identity->userId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * deck creation
     *
     * @return null|string|\yii\web\Response
     */
    public function actionCreate()
    {
        $response = null;
        $colors = Deck::getColors();
        $formDeck = new Deck(['scenario' => 'create']);
        $sets = Set::find()->all();
        $cards = [];
        foreach($sets as $set){
            $cards[] = Card::find()->where(['setId' => $set->setId])->all();
        }

        if (($formDeck->load($_POST) === true) && ($formDeck->validate() === true)) {
            $formDeck->userId = Yii::$app->user->identity->userId;
            $formDeck->deckColor = implode('-', $formDeck->deckColor);
            $status = $formDeck->save();
            if ($status === true) {
                $response = $this->redirect(['/card/index']);
            }
        }

        if ($response === null) {
            $response = $this->render('create', [
                'deck' => $formDeck,
                'sets' => $sets,
                'cards' => $cards,
                'colors' => $colors
            ]);
        }

        return $response;
    }

    /**
     * display deck informations
     *
     * @param $deckId
     * @return string
     * @throws Exception
     * @throws \Exception
     *
     * @return void
     */
    public function actionView($deckId, $filter = null)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            //ob_start();
            $response = null;
            $deck = Deck::findOne($deckId);
            if ($deck === null) {
                throw new NotFoundHttpException;
            }

            $manas = Utilities::getMana();
            $colors = explode('-', $deck->deckColor);
            $deckManas = '';
            foreach($colors as $color) {
                $deckManas .= $manas[$color];
            }
            if ($filter === 'types') {
                $response = $this->filterByTypes($deckId);
            } elseif ($filter === 'cost') {
                $response = $this->filterByCost($deckId);
            }
            /* $content = ob_get_contents();
             ob_end_clean();*/

            if ($response === null) {
                $response = $this->render('view', [
                    'deck' => $deck,
                    'content' => $this->filterByTypes($deckId),
                ]);
            }
            return $response;
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * @param $deckId
     * @return string
     * @throws Exception
     * @throws \Exception
     */
    public function filterByTypes($deckId)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $deck = Deck::findOne($deckId);
            if ($deck === null) {
                throw new NotFoundHttpException;
            }
            $deckCards = [];
            $totalCards = 0;
            $totalCreaturesCards = 0;
            $totalLandsCards = 0;
            $totalSpellsCards = 0;
            $cardTypes = Card::getCardTypes();
            $cards = $deck->getCards()->all();

            foreach($cards as $card) {
                $type = 'spells';

                $sql = Yii::$app->db->createCommand('SELECT * FROM decksCards WHERE cardId = '.$card->cardId.' AND deckId = '.$deck->deckId)->queryOne();
                if ($sql === false) {
                    throw new NotFoundHttpException();
                }
                $totalCards += $sql['cardNumber'];
                foreach($cardTypes as $index => $cardType) {
                    if (in_array($card->cardType, $cardType) === true) {
                        $type = $index;
                        if ($type === 'creatures') {
                            $totalCreaturesCards += $sql['cardNumber'];
                        } elseif ($type === 'lands') {
                            $totalLandsCards += $sql['cardNumber'];
                        } else {
                            $totalSpellsCards += $sql['cardNumber'];
                        }
                        break;
                    }
                }
                if ($type === 'creatures') {
                    $deckCards[$type]['totalCards'] = $totalCreaturesCards;
                } elseif ($type === 'lands') {
                    $deckCards[$type]['totalCards'] = $totalLandsCards;
                } else {
                    $deckCards[$type]['totalCards'] = $totalSpellsCards;
                }

                $card->cardNumber =(int) $sql['cardNumber'];
                $deckCards[$type]['cards'][$card->cardId]  = $card;
            }

            return $this->renderPartial('_types', [
                'deck' => $deck,
                'deckCards' => $deckCards,
                'totalCards' => $totalCards,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
    /**
     *
     * display deck card by cost
     *
     * @param $deckId
     * @throws Exception
     * @throws \Exception
     *
     * @return string
     */
    public function filterByCost($deckId)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $deck = Deck::findOne($deckId);
            if ($deck === null) {
                throw new NotFoundHttpException;
            }
            $manas = Utilities::getMana();
            $colors = explode('-', $deck->deckColor);
            $deckManas = '';
            foreach($colors as $color) {
                $deckManas .= $manas[$color];
            }

            $cards = $deck->getCards()->all();
            $deckCards = [];

            foreach($cards as $card) {
                $deckCards[$card->getTotalCost()][$card->cardId] = $card;
            }
            //sort ASC
            arsort($deckCards);

            return $this->renderPartial('_mana', [
                'deck' => $deck,
                'deckCards' => $deckCards,
                'deckManas' => $deckManas,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'view'],
                        'roles' => ['@'],
                    ]
                ]
            ],
        ];
    }
}