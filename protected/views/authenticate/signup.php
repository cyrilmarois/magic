<?php
/**
 * Created by PhpStorm.
 * User: cyril
 * Date: 25/04/2015
 * Time: 23:27
 */

use yii\helpers\Html;
?>
<div class="col-md-3 col-md-offset-4">
    <div class="row">
        <h1>Créer votre compte</h1>
        <fb:login-button scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button>
        <?php echo Html::beginForm('', 'POST', ['id' => 'registerForm', 'class' => 'form-horizontal']); ?>
            <?php echo Html::activeHiddenInput($user, 'userFirstname', ['class' => 'form-control']); ?>
            <?php echo Html::activeHiddenInput($user, 'userLastname', ['class' => 'form-control']); ?>
            <?php echo Html::activeHiddenInput($user, 'userAuthKey', ['class' => 'form-control']); ?>

            <div class="form-group">
                <?php echo Html::activeLabel($user, 'userNickname', ['class' => 'col-md-4']); ?>
                <div class="col-md-8">
                    <?php echo Html::activeTextInput($user, 'userNickname', ['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo Html::activeLabel($user, 'userEmail', ['class' => 'col-md-4']); ?>
                <div class="col-md-8">
                    <?php echo Html::activeTextInput($user, 'userEmail', ['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo Html::activeLabel($user, 'userPassword', ['class' => 'col-md-4']); ?>
                <div class="col-md-8">
                    <?php echo Html::activePasswordInput($user, 'userPassword', ['class' => 'form-control']); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo Html::activeLabel($user, 'userPasswordCheck', ['class' => 'col-md-4']); ?>
                <div class="col-md-8">
                    <?php echo Html::activePasswordInput($user, 'userPasswordCheck', ['class' => 'form-control']); ?>
                </div>
            </div>
            <?php echo Html::a('Se connecter', ['/authenticate/signin'], ['class' => 'btn btn-info col-md-4']); ?>
            <?php echo Html::submitButton('Créer', ['class' => 'btn btn-success col-md-4 pull-right']); ?>
        <?php echo Html::endForm(); ?>
    </div>
</div>