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
            Yii::trace('Trace :'.__METHOD__, __METHOD__);

            $query = Set::find();
            $dataProvider = new ActiveDataProvider([
               'query' => $query,
            ]);

            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        } catch(Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw $e;
        }
    }
}