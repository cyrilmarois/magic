<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 29/04/2015
 * Time: 00:19
 */

use app\models\Set;
use yii\helpers\Html;

?>
<?php echo Html::hiddenInput('displayMode', '_list'); ?>
<?php foreach($cards as $i => $card): ?>
    <?php if ($i % 5 === 0): ?>
        <div class="row">
    <?php endif; ?>
        <div class="col-md-2">
            <?php echo Html::activeHiddenInput($card, 'cardId'); ?>
            <?php echo Html::img($card->getPicture(), ['alt' => $card->cardNameVO, 'class' => 'card']); ?>
        </div>
    <?php if ($i % 5 === 4): ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

