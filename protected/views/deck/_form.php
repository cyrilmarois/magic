<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 19:05
 */

use yii\helpers\Html;

?><div class="form-group">
    <?php echo Html::activeLabel($deck, 'deckName', ['class' => 'col-md-2']); ?>
    <div class="col-md-10">
        <?php echo Html::activeTextInput($deck, 'deckName', ['class' => 'form-control']); ?>
    </div>
</div>
<div class="form-group">
    <?php echo Html::activeLabel($deck, 'deckColor', ['class' => 'col-md-2']); ?>
    <div class="col-md-10">
        <?php echo Html::activeCheckboxList($deck, 'deckColor', $colors, ['encode' => false]); ?>
    </div>
</div>
<?php echo Html::submitButton('Créer', ['class' => 'btn btn-success col-md-2 pull-right']);