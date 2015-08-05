<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 03/05/2015
 * Time: 11:50
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="row">
    <div class="col-md-3 col-md-offset-1">
        <?php echo Html::img($card->getPicture(), ['alt' => $card->cardNameVO]); ?>
    </div>
    <div class="col-md-6">
        <?php
            echo DetailView::widget([
                'model' => $card,
                'attributes' => [
                    'cardNameVO',
                    [
                        'label' => 'cardCost',
                        'format' => 'html',
                        'value' => $card->getCostPictures(),
                    ],
                    'cardType',
                    'cardMonsterEnergy',
                    [
                        'label' => 'cardDescriptionVO',
                        'format' => 'html',
                        'value' => $card->cardDescriptionVO,
                    ],
                ]
            ]);
        ?>
    </div>
</div>
