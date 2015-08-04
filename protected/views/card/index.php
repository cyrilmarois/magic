<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 27/04/2015
 * Time: 20:51
 */

use yii\grid\GridView;
use yii\helpers\Html;

?><h1>List des collections</h1>
<?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'format' => 'html',
                'value' => function($data) {
                    return Html::a(Html::img(Html::decode($data->setLogoUrl)), ['/set/view', 'id' => $data->setId]).Html::img(Html::decode($data->setIconUrl));
                }
            ],
            [
                'attribute' => 'setName',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->setName, ['/set/view', 'id' => $data->setId]);
                }
            ],
            [
                'label' => 'Nb de cartes',
                'value' => function($data) {
                    return count($data->cards);
                }
            ],
            'setYear',
            [
                'format' => 'html',
                'value' => function($data) {
                    return Html::a('Voir les cartes', ['/card/list-cards', 'setId' => $data->setId]);
                }
            ]
        ]

    ]);