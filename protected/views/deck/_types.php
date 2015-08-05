<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 19:33
 */

use app\models\Card;
use yii\helpers\Html;

?>
<div class="row">
    <div class="col-md-3">
        <table class="table">
            <?php foreach($deckCards as $type => $deckCard): ?>
                <thead>
                    <th><h2><?php echo $type; ?> (<?php echo $deckCard['totalCards']; ?>)</h2></th>
                </thead>
                <?php foreach($deckCard['cards'] as $card): ?>
                    <tr>
                        <td>
                            <?php echo $card->cardNumber; ?>
                            <?php echo $card->cardNameVO; ?>
                            <?php echo Html::hiddenInput('cardPictureVO', $card->getPicture()); ?>
                        </td>
                    </tr><?php endforeach; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="col-md-4">
        <?php echo Html::img('@web/images/'.Card::DEFAULT_PICTURE_CARD); ?>
    </div>
</div>