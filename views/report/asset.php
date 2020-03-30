<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\widgets\PageSize;
use app\components\MaintnanceComp;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
/* @var $this yii\web\View */

$this->title = 'Asset Report'; 

$this->registerJsFile(
    '@web/js/report.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>
<div class="site-index">
    <div class="card light-shadow mb-2">
        <div class="card-header">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="asset-tab" data-toggle="tab" href="#asset" role="tab">Asset</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="work-order-tab" data-toggle="tab" href="#work-order" role="tab">Work Order</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="failure-tab" data-toggle="tab" href="#failure" role="tab">Failure</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="job-tab" data-toggle="tab" href="#job" role="tab">Job</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="movement-tab" data-toggle="tab" href="#movement" role="tab">Movement</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="upcomming-tab" data-toggle="tab" href="#upcomming" role="tab">Upcomming</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="mnt-board-tab" data-toggle="tab" href="#mnt-board" role="tab">Maintenance Board</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="history-card-tab" data-toggle="tab" href="#history-card" role="tab">History Card</a>
              </li>
            </ul>
            <div class="tab-content border-right border-bottom border-left bg-white" id="reportContent">
              <div class="tab-pane fade show active p-3" id="asset" role="tabpanel">
                    <?= $this->render('_asset', []) ?>
              </div>
              <div class="tab-pane fade p-3" id="work-order" role="tabpanel">
                    <?= $this->render('_workorder', [
                        'maintenances' => $maintenances,
                        'woStatuses' => $woStatuses,
                        'technicians' => $technicians,
                        'woTypes' => $woTypes
                    ]) ?>
              </div>
              <div class="tab-pane fade p-3" id="failure" role="tabpanel">
                    <?= $this->render('_failure', [
                        'failTypes' => $failTypes
                    ]) ?>
              </div>
              <div class="tab-pane fade p-3" id="job" role="tabpanel">
                    <?= $this->render('_job', [
                        'clients' => $clients,
                        'locations' => $locations,
                        'muds' => $muds
                    ]) ?>
              </div>
              <div class="tab-pane fade p-3" id="movement" role="tabpanel">
                    <?= $this->render('_movement', [
                        'mvTypes' => $mvTypes,
                        'locations' => $locations,
                        'mvStats' => $mvStats
                    ]) ?>
              </div>
              <div class="tab-pane fade p-3" id="upcomming" role="tabpanel">
                    <?= $this->render('_upcoming', [
                        'maintenances' => $maintenances,
                    ]) ?>
              </div>
              <div class="tab-pane fade p-3" id="mnt-board" role="tabpanel">
                    <?= $this->render('_mntboard') ?>
              </div>
              <div class="tab-pane fade p-3" id="history-card" role="tabpanel">
                    <?= $this->render('_historycard') ?>
              </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <div class="input-group">
                <?= AutoComplete::widget([
                    'name' => 'collection',
                    'clientOptions' => [
                        'source' => Url::to(['user-asset/search']),
                        'selectFirst' => true,
                        'change' => new JsExpression("function (event, ui) {
                            if (ui.item == null){
                                $(this).val('');
                                $('input[name=collection_id]').val('');
                            }
                        }"),
                        'select' => new JsExpression("function( event, ui ) {
                            $('input[name=collection_id]').val(ui.item.id);
                        }")
                    ],
                    'options' => [
                        'class' => 'form-control form-control-sm',
                        'placeholder' => 'Collection name...'
                    ]
                ]); ?>
                <?= Html::hiddenInput('collection_id', '') ?>
                <div class="input-group-append">
                    <span class="input-group-text p-0">
                        <?= Html::a(Html::tag('i', '', ['class' => "fa fa-history"]).' Load', '', [
                            'class' => 'text-dark px-2 btn-sm',
                            'onclick' => 'loadCollection(event)',
                            'id' => 'add-asset'
                        ]) ?>
                    </span>
                </div>
            </div>
            <table class="table table-striped table-bordered table-sm table-hover" id="selected-asset">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Serial No</th>
                        <th>Maintenance Types</th>
                        <th>Located</th>
                        <th>Cell</th>
                        <th>Set</th>
                        <th>Remarks</th>
                        <th width="40" class="text-center clear-all"><i class="fa fa-remove"></i></th>
                    </tr>
                </thead>
                <tbody><tr><td colspan="8">No selected asset</td></tr></tbody>
            </table>
        </div>
        <div id="asset-grid" class="card-body table-wrapper table-responsive">
            <span id="page-size" class="float-left mr-1">
                <?= PageSize::widget(['options'=>['class'=>'form-control form-control-sm']]); ?>
            </span>
            <button id="reset-table" class="mb-1 float-right btn btn-light btn-sm">Remove Filters</button>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel, 
                'filterSelector' => 'select[name="per-page"]',
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'contentOptions' =>['class' => 'checkbox-column'],
                        'headerOptions' => ['class' => 'checkbox-column head-check'],
                    ],
                    [
                        'attribute' => 'asset_code', 
                        'value' => 'assetCode.name', 
                        'label' => 'Asset Code'
                    ],
                    'serial_no',
                    [
                        'attribute' => 'maintenance_type', 
                        'label' => 'Maintenance Types',
                        'filter'=> $mntTypes,
                        'format' => 'raw',
                        'value' => function($data, $row){
                            $result = [];
                            foreach ($data->assetCode->assetCodeMaintenances as $acm) {
                                if(!in_array($acm->maintenance->maintenanceType['code'], $result))
                                $result[] = $acm->maintenance->maintenanceType['code'];
                            }

                            $str_result = '';
                            foreach ($result as $item) {
                                $str_result .= "<span class='btn btn-sm btn-secondary'>".$item."</span> ";
                            }
                            return $str_result;                            
                        }
                    ],
                    [
                        'attribute' => 'physical_location', 
                        'filter'=> $locations,
                        'value' => 'physicalLocation.location_name', 
                        'label' => 'Located'
                    ],
                    [
                        'attribute' => 'asset_cell', 
                        'value' => 'cell.name', 
                        'label' => 'Cell'
                    ],
                    [
                        'attribute' => 'asset_set', 
                        'value' => 'set.name', 
                        'label' => 'Set'
                    ],
                    'remarks',
                    [
                        'attribute' =>'movement_status',
                        'filter'=> $mvStatuses,
                        'label' => 'MvStat',
                        'value' => function($data, $row) use ($mvStatuses){
                            return $mvStatuses[$data->movement_status];
                        }
                    ],
                    [
                        'attribute' =>'status',
                        'filter'=> $statuses,
                        'format' => 'raw',
                        'value' => function($data, $row) use ($statuses){
                            $results = MaintnanceComp::find_status([
                                'asset_id' => $data->asset_id,
                                'status' => $data->status, 
                                'stat_str' => $statuses[$data->status], 
                                'maintenances' => $data->assetCode->assetCodeMaintenances
                            ]);
                            return implode(' ', $results);
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header'=>'View', 
                        'headerOptions' => ['width' => '40'],
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a(Html::tag('i', '', ['class' => "fa fa-eye"]), ['/asset/view', 'id' => $model->asset_id]);
                            }
                        ]
                    ],
                ],
                'tableOptions' =>['class' => 'table table-striped table-bordered table-sm table-hover'],
                'filterRowOptions' => ['class' => 'sm-row']
            ]); ?>

        </div>
    </div>
</div>