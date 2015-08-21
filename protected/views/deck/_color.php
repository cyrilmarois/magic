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
            <?php foreach($deckCards as $color => $deckCard): ?>
                <thead>
                    <tr>
                        <th><h2><?php echo ucfirst($color); ?></h2></th>
                    </tr>
                </thead>
                <?php foreach($deckCard as $card): ?>
                    <tr>
                        <td>
                            <?php echo $card->cardNameVO; ?>
                            <?php echo Html::hiddenInput('cardPictureVO', $card->getPicture()); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="col-md-4">
        <?php echo Html::img('@web/images/'.Card::DEFAULT_PICTURE_CARD); ?>
    </div>
</div>