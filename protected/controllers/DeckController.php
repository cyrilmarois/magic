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
            } elseif ($filter === 'colors') {
                $response = $this->filterByColors($deckId);
            } else {
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
     * display deck card by cost
     *
     * @param $deckId
     * @throws Exception
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

    /**
     * display deck card by color
     *
     * @param $deckId
     * @throws Exception
     *
     * @return string
     */
    public function filterByColors($deckId)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $deck = Deck::findOne($deckId);
            if ($deck === null) {
                throw new NotFoundHttpException;
            }
            $colors = Utilities::getColors();
            $cards = $deck->getCards()->all();
            $deckCards = [];

            foreach($cards as $card) {
                $cardColor = (isset($colors[$card->cardColor]) === true) ? $colors[$card->cardColor] : 'uncolor';
                $deckCards[$cardColor][$card->cardId] = $card;
            }
            //sort ASC
            arsort($deckCards);

            return $this->renderPartial('_color', [
                'deck' => $deck,
                'deckCards' => $deckCards,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }

    /**
     * add card to the deck selected
     *
     * @throws \yii\db\Exception
     */
    public function actionAddCard()
    {
        if ((isset($_POST['deckId']) === true) && (isset($_POST['cardId']) === true) && (isset($_POST['cardNumber']) === true)) {
            $sql = Yii::$app->db->createCommand('SELECT * FROM decksCards WHERE cardId = '.$_POST['cardId'].' AND deckId = '.$_POST['deckId'])->queryOne();
            if ($sql === false) {
                Yii::$app->db->createCommand()->insert('decksCards', [
                    'deckId' => $_POST['deckId'],
                    'cardId' => $_POST['cardId'],
                    'cardNumber' => $_POST['cardNumber'],
                ])->execute();
            } else {
                $newCardNumber = (int) $sql['cardNumber'] + (int)$_POST['cardNumber'];
                Yii::$app->db->createCommand()->update('decksCards', [
                    'deckId' => $_POST['deckId'],
                    'cardId' => $_POST['cardId'],
                    'cardNumber' => $newCardNumber,
                ], [
                    'deckId' => $_POST['deckId'],
                    'cardId' => $_POST['cardId'],
                ])->execute();
            }

            $response = Yii::$app->response;
            $response->statusCode = 200;
            $response->format = \yii\web\Response::FORMAT_HTML;
            $response->data = '<div class="col-md-12"><span class="success">La carte a bien été ajouté</span></div>';
        }
    }

    /**
     * delete the deckId
     *
     * @param $deckId the deckId
     *
     * @throws Exception
     * @throws \Exception
     *
     * @return void
     * @since  XXX
     */
    public function actionDelete($deckId)
    {
        try {
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $deck = Deck::findOne($deckId);
            if ($deck === null) {
                throw new NotFoundHttpException('Le deck n\'existe pas', 404);
            }

            $deck->delete();
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }


    /**
     * add card form
     */
    public function actionAddCardForm()
    {
        if (Yii::$app->user->identity !== null) {
            $decksName = [];
            $decks = Deck::find()->where(['userId' => Yii::$app->user->identity->userId])->orderBy('deckName')->all();
            foreach($decks as $deck) {
                $decksName[$deck->deckId] = $deck->deckName;
            }
            $str = '';
            $str .= '<div class="col-md-11 cardForm">';
            $str .=  Html::dropDownList('deckName', null, $decksName,['prompt' => 'Sélectionner un deck']);
            $str .= '<br>';
            $str .= Html::textInput('cardNumber', null);
            $str .= '<br>';
            $str .= Html::button('Ajouter', ['class' => 'addCard']);
            $str .= '</div>';
        } else {
            $str = '';
            $str .= '<div class="col-md-11 cardForm">';
            $str .= Html::tag('span', 'Vous devez être connecté si vous souhaitez pouvoir ajouter la carte à l\'un de vos decks');
            $str .= '</div>';
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_HTML;
        $response->content = $str;
    }

    /**
     * @inherit
     */
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