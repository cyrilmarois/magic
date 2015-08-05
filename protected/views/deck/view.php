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
<h1><?php echo $deck->deckName /*.'('./$totalCards.')'. $deckManas*/; ?></h1>
Filtrer par : <?php echo Html::dropDownList('filter', 'types', ['types' => 'types', 'cost' => 'cost']); ?>

<div class="deck">
    <?php
        echo $content;
    ?>
</div>