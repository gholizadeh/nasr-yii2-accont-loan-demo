<?php

use app\models\Account;
use app\models\Client;
use app\models\Loan;
use yii\helpers\Html;

$this->title = 'وضعیت صندوق'; 
?>
<div class="site-index">

    <div class="jumbotron light-shadow p-1 mb-2">
        <?= Html::img('@web/images/logo.png') ?>
    </div>
    
    <div class="card light-shadow mb-2">
        <div class="card-header py-1"> 
            داشبورد
        </div>
        <div class="card-body table-responsive table-wrapper pt-4">
            <div class="row">
                <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
                    <div class="d-item-container blue">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>تعداد مشتریان</h4>
                            <p class="w-75"><?= number_format(Client::segmentClientsCount()) ?></p>
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
                    <div class="d-item-container yellow">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>تعداد حسابها</h4>
                            <p class="w-75"><?= number_format(Account::segmentAccountsCount()) ?></p>
                            <i class="fa fa-credit-card"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
                    <div class="d-item-container orange">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>موجودی کل</h4>
                            <p class="w-75"><?= number_format(Account::segmentAccountsSum()) ?> تومان</p>
                            <i class="fa fa-university"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 mx-auto">
                    <div class="d-item-container green">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>تسهیلات اعطایی</h4>
                            <p class="w-75"><?= number_format(Loan::segmentLoanSum()) ?> تومان</p>
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 mb-5 mb-lg-0 mx-auto">
                    <div class="d-item-container sayan">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>تسهیلات جاری</h4>
                            <p class="w-75"><?= number_format(Loan::segmentCurrentLoanSum()) ?> تومان</p>
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 mx-auto">
                    <div class="d-item-container pink">
                        <div class="card-body d-flex align-items-right flex-column d-item">
                            <h4>تعداد تسهیلات جاری</h4>
                            <p class="w-75"><?= number_format(Loan::segmentLoanCount()) ?></p>
                            <i class="fas fa-funnel-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>