<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 26/04/2015
 * Time: 19:33
 */

use yii\helpers\Html;

$this->registerJsFile(Yii::getAlias('@web/js/deck.js'), ['position' => \yii\web\View::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);
?>
<h1><?php echo $deck->deckName /*.'('./$totalCards.')'. $deckManas*/; ?></h1>
<?php echo Html::activeHiddenInput($deck, 'deckId'); ?>
Filtrer par : <?php echo Html::dropDownList('filter', 'types', ['types' => 'types', 'cost' => 'cost', 'colors' => 'colors']); ?>

<div class="deck">
    <?php
        echo $content;
    ?>
</div>