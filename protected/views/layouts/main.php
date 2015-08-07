<?php
use app\assets\AppAsset;
use app\widgets\Sidebar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo Html::csrfMetaTags() ?>
        <title><?php echo Html::encode(Yii::$app->name) ?></title>
        <?php $this->head() ?>
        <script>
            var fetchCardUrl = "<?php echo Url::to(['/card/fetch']); ?>";
            var fetchCardsUrl = "<?php echo Url::to(['/card/fetch-cards']); ?>";
            var cardUrl = "<?php echo Url::to(['/card/view']); ?>";
        </script>
    </head>
    <body>

    <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
                NavBar::begin([
                    'brandLabel' => Yii::$app->name,
                    'brandUrl' => Yii::$app->homeUrl,
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                ]);
            ?>
            <ul class="navbar-form navbar-left">
                <li class="dropdown">
                    <input type="text" class="form-control" placeholder="Chercher une carte" id="search">
                    <ul class="dropdown-menu" role="menu">
                    </ul>
                </li>
            </ul>
            <?php
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav navbar-right'],
                    'items' => [
                        ['label' => 'Cartes', 'url' => ['/card/index']],
                        Yii::$app->user->isGuest ?
                            ['label' => 'Mon espace', 'url' => [Yii::$app->user->loginUrl]] :
                            ['label' => 'Me déconnecter',
                                'url' => ['/authenticate/logout'],
                                'linkOptions' => ['data-method' => 'post']],
                    ],
                ]);
                NavBar::end();
            ?>

            <div class="container">
                <?php
                    echo Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);

                    if (Yii::$app->user->identity !== null) {
                        echo Sidebar::widget();
                        echo Html::beginTag('div', ['class' => 'main']);
                    }
                        echo $content;

                    if (Yii::$app->user->identity !== null) {
                        echo Html::endTag('div');
                    }
                ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; <?php echo Yii::$app->name; ?> <?php echo date('Y') ?></p>
                <p class="pull-right"><?php echo Yii::powered() ?></p>
            </div>
        </footer>

    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
