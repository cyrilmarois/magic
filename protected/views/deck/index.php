<?php

use yii\helpers\Html;
use yii\grid\GridView;

?>
<h1>List des decks</h1>
<div class="row">
    <div class="col-md-3">
        <?php echo Html::a('Créer', ['/deck/create'], ['class' => 'btn']); ?>
    </div>
</div>
<?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'deckName',
            'deckColor',
            [
                'label' => 'Nb de cartes',
                'value' => function($data) {
                    return count($data->getCards()->all());
                }
            ],
            'actions' => [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}'
            ]
        ]

    ]);