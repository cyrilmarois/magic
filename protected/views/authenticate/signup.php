<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 25/04/2015
 * Time: 23:27
 */

use yii\helpers\Html;

?><h1>Créer votre compte</h1>
<?php echo Html::beginForm('', 'POST', ['class' => 'form-horizontal']); ?>
    <div class="form-group">
        <?php echo Html::activeLabel($user, 'userNickname', ['class' => 'col-md-2']); ?>
        <div class="col-md-10">
            <?php echo Html::activeTextInput($user, 'userNickname', ['class' => 'form-control']); ?>
        </div>
    </div>
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
    <div class="form-group">
        <?php echo Html::activeLabel($user, 'userPasswordCheck', ['class' => 'col-md-2']); ?>
        <div class="col-md-10">
            <?php echo Html::activePasswordInput($user, 'userPasswordCheck', ['class' => 'form-control']); ?>
        </div>
    </div>
    <?php echo Html::submitButton('Créer', ['class' => 'btn btn-success col-md-2 pull-right']); ?>
<?php echo Html::endForm(); ?>