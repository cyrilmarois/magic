<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 14:35
 */

use yii\helpers\Url;
use yii\helpers\Html;

$js = <<<EOF
    $('.glyphicon-remove').on('click', function(evt) {
        evt.preventDefault();
        self = $(this);
        $.ajax({
            type: 'GET',
            url: $(this).parent('a').data('url'),
            data: { deckId: $(this).parent('a').data('deckid') }
        }).done(function(data) {
            self.parents('.row').remove();
        });
    });

EOF;

$this->registerJs($js, \yii\web\View::POS_READY);
?>
<div class="sidebar">
    <div class="row">
        <div class="col-md-12">
            <h2>Mes decks <?php echo Html::a(Html::tag('span', '', ['class' => 'glyphicon-plus']), ['/deck/create']); ?></h2>
        </div>
    </div>

    <?php foreach ($decks as $deck): ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo Html::a($deck->deckName, ['/deck/view', 'deckId' => $deck->deckId]); ?>
                <?php echo Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']), '#', ['data-url' => Url::to(['/deck/delete'], true), 'data-deckid' => $deck->deckId ]); ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>