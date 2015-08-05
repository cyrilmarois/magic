<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 25/04/2015
 * Time: 23:27
 */

use yii\helpers\Html;
?>
<h1>Créer votre Deck</h1>
<?php
    echo Html::beginForm('', 'POST', ['class' => 'form-horizontal']);
        echo $this->render('_form', [
            'deck' => $deck,
            'colors' => $colors,
        ]);
    echo Html::endForm();
