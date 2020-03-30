<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        خطای فوق هنگام پردازش درخواست شما رخ داده است.
    </p>
    <p>
        در صورتی که علت آن مشخص نیست آن را به واحد فنی گزارش دهید. و آدرس و شرایط به وجود آمدن خطا را شرح دهید. با تشکر.
    </p>

</div>
