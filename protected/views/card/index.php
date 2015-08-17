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
                    return Html::a(Html::img(Html::decode($data->setLogoUrl)), ['/card/list-cards', 'setId' => $data->setId]).Html::img(Html::decode($data->setIconUrl));
                }
            ],
            [
                'attribute' => 'setName',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->setName, ['/card/list-cards', 'setId' => $data->setId]);
                }
            ],
            [
                'label' => 'Nb de cartes',
                'value' => function($data) {
                    return count($data->cards);
                }
            ],
            'setYear',
        ]

    ]);