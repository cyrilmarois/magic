<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 29/04/2015
 * Time: 00:19
 */

use yii\helpers\Html;

$offset = count($cards);

?>
<div class="row">
    <div class="col-md-5">
        <h1><?php echo $setName; ?></h1>
    </div>
    <div class="pull-right">
        <?php
            echo Html::a(
                Html::tag('span', '', ['aria-hidden' => true, 'class' => 'glyphicon glyphicon-th']),
                ['/card/list-cards', 'setId' => $set->setId, 'displayMode' => 'list']
            );
        ?> |
        <?php
            echo Html::a(
                Html::tag('span', '', ['aria-hidden' => true, 'class' => 'glyphicon glyphicon-th-list']),
                ['/card/list-cards', 'setId' => $set->setId, 'displayMode' => 'details']
            );
        ?>
    </div>
</div>

<?php
   echo $this->render($viewMode, [
       'cards' => $cards,
       'decksName' => $decksName,
   ]);
?>