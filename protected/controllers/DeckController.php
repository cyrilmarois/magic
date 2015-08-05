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