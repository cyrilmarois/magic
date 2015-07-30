<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 25/04/2015
 * Time: 23:27
 */

use yii\helpers\Html;
?>
<h1>Me connecter</h1>
<?php echo Html::beginForm('', 'POST', ['class' => 'form-horizontal', 'id' => 'loginForm']); ?>
    <?php echo Html::activeHiddenInput($user, 'userFirstname', ['class' => 'form-control']); ?>
    <?php echo Html::activeHiddenInput($user, 'userLastname', ['class' => 'form-control']); ?>
    <?php echo Html::activeHiddenInput($user, 'userAuthKey', ['class' => 'form-control']); ?>
    <div class="form-group">
        <?php echo Html::activeLabel($user, 'userEmail', ['class' => 'col-md-2']); ?>
        <div class="col-md-10">
            <?php echo Html::activeTextInput($user, 'userEmail', ['class' => 'form-control']); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Html::activeLabel($user, 'userPassword', ['class' => 'col-md-2']); ?>
        <div class="col-md-10">
            <?php echo Html::activePasswordInput($user, 'userPassword', ['class' => 'form-control']); ?>
        </div>
    </div>
    <?php echo Html::submitButton('Se connecter', ['class' => 'btn btn-success col-md-2 pull-right']); ?>
<?php echo Html::endForm(); ?>