<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 29/04/2015
 * Time: 00:19
 */

use app\models\Card;
use yii\helpers\Html;

?>
<?php foreach($cards as $i => $card): ?>
    <?php if ($i % 3 === 0): ?>
        <div class="row">
    <?php endif; ?>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-5 pull-left">
                <?php echo Html::img($card->getPicture(), ['alt' => $card->cardNameVO, 'class' => 'card']); ?>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-12 manas">
                        <?php echo $card->cardNameVO .' '.$card->getCostPictures(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            echo $card->cardType;
                            if (in_array($card->cardType, Card::getCardTypes()) === true) {
                                echo $card->cardMonsterEnergy;
                            }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 manas">
                        <?php echo Html::decode($card->replaceDesriptionPicto($card->cardDescriptionVO)); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            if (Yii::$app->user->identity === null) {
                                echo 'Connectez vous pour ajouter la carte à l\'un de vos decks';
                            } else {
                                //user is logged
                                echo Html::activeHiddenInput($card, 'cardId');
                                echo Html::dropDownList('deckName', null, $decksName,['prompt' => 'Sélectionner un deck']);
                                echo '<br>';
                                echo Html::textInput('cardNumber', null);
                                echo '<br>';
                                echo Html::button('Ajouter', ['class' => 'addCard']);
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($i % 3 === 2): ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
