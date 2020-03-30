<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <nav class="navbar navbar-dark navbar-ray navbar-expand-lg top-navbar">
        <div class="container justify-content-start">
            <?= Html::a(
                Html::img('@web/images/top-logo.png',['id' => 'logo']), 
                [Yii::$app->homeUrl], ['class' => 'navbar-brand d-none d-sm-flex']) 
            ?>
            <button type="button" class="navbar-toggler collapsed" data-toggle="collapse" data-target="#w1-collapse" aria-controls="w1-collapse" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="d-flex d-lg-none btn-group">
                <button type="button" class="btn navbar-toggler dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Html::tag('i','',['class' => 'fa fa-user m-1']) ?>
                </button>
                <div class="dropdown-menu dropdown-menu-left seg-part p-2">
                    <?= $this->render('_segment') ?>

                    <?=Html::beginForm(['site/logout'], 'post') ?>
                        <?=Html::submitButton(
                            Html::tag('i','',['class' => 'fa fa-sign-out m-1'])." Logout", ['class' => 'btn logout']
                        ) ?>
                    <?=Html::endForm() ?>
                </div>
            </div>
            <button class="btn navbar-toggler d-flex d-lg-none sidebarCollapse">
                <?= Html::tag('i','',['class' => 'fa fa-bell m-1']) ?>
            </button>
            <div id="w1-collapse" class="navbar-collapse collapse">
            <?php
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'خانه', 'url' => ['/']],//[Yii::$app->homeUrl]],
                    ['label' => 'صندوق', 
                        'items' => [
                            ['label' => 'مشتریان', 'url' => ['/client']],
                            ['label' => 'حساب ها', 'url' => ['/account']],
                            ['label' => 'تسهیلات', 'url' => ['/loan']],
                        ]
                    ],
                    ['label' => 'اطلاعات پایه', 
                        'items' => [
                            ['label' => 'صندوق ها', 'url' => ['/segment']],
                            '<div class="dropdown-divider"></div>',
                            '<div class="dropdown-header">مدیریت صندوق</div>',
                            ['label' => 'انواع حساب', 'url' => ['/account-type']],
                            ['label' => 'انواع تسهیلات', 'url' => ['/loan-type']],
                            ['label' => 'هزینه ها', 'url' => ['/costs']],
                            '<div class="dropdown-divider"></div>',
                            ['label' => 'راهنما', 'url' => ['/site/about']],
                        ]
                    ],
                    ['label' => 'گزارشات', 
                        'items' => [
                            ['label' => 'تکمیل نشده', 'url' => ['/report/test']],
                        ]
                    ],
                    ['label' => 'کاربران',
                       'items' => [
                            ['label' => 'کاربران', 'url' => ['/user']],
                            ['label' => 'گروه های کاربری', 'url' => ['/user-group']],
                       ]
                    ]
                ],
            ]);

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav mr-auto d-none d-lg-flex'],
                'encodeLabels' => false,
                'items' => [
                    '<div class="dropdown btn navbar-link">'.
                        Html::a(Html::tag('i','',['class' => 'fa fa-user m-1']), 
                            [''], ['class' => 'nav-link dropdown-toggle p-0 pt-1', 'data-toggle'=>'dropdown']
                        ).
                        '<a class="dropdown-menu dropdown-menu-left seg-part p-2">'. $this->render('_segment') .
                            Html::beginForm(['site/logout'], 'post').Html::submitButton(
                                "خروج", ['class' => 'btn logout']
                            ).Html::endForm().
                        '</a>
                    </div>',
                    ['label' => Html::tag('i','',['class' => 'fa fa-bell']),
                       'options' => ['class' => 'sidebarCollapse btn navbar-link '],
                    ]
                ],
            ]);
            ?>
            </div>
        </div>
    </nav>

    <nav id="sidebar">
        <div class="sidebar-header">
            <h4>اعلان ها</h4>
        </div>
    </nav>

    <div id="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer px-2">
    <p class="pull-left">طراحی و ساخت: قلی زاده</p>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
