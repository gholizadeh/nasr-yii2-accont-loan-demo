<?php

namespace app\controllers;

use Yii;
use app\models\Asset;
use app\models\Location;
use app\models\MaintenanceType;
use app\models\Maintenance;
use app\models\User;
use app\models\WorkOrder;
use app\models\Failure;
use app\models\Client;
use app\models\Job;
use app\models\Movement;
use app\modules\spreadsheet\Spreadsheet;
use app\components\MaintnanceComp;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use yii\web\NotAcceptableHttpException;

class ReportController extends BasicController
{
    /**
     * {@inheritdoc}
     */
    public function Verbs()
    {
        return [
            'asset-report' => ['POST'],
            'work-order' => ['POST'],
            'job' => ['POST'],
            'failure' => ['POST'],
            'movement' => ['POST'],
            'maintenance-board' => ['POST'],
            'upcoming-activity' => ['POST'],
            'history-card' => ['POST'],
            'mnt-efficiency' => ['POST'],
            'total-efficiency' => ['POST']
        ];
    }

    public static function breakContent($content, $width = 35){
        $html_helper = new Html();
        $pattern = '/(?=\s)(.{1,'.$width.'})(?:\s|$)/uS';
        $replace = '$1<br>';
        $return = preg_replace($pattern, $replace, $content);
        return $html_helper->toRichTextObject($return);
    }

    public function GenerateHeader($title, $filters = []){
        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

        $payable=$richText->createTextRun("RAY OMAN - Asset Management");
        $payable->getFont()->setBold(true);
        $payable->getFont()->setItalic(true);
        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKYELLOW ) );

        $filter_str = '';
        foreach($filters as $key=>$item) {
            if(!empty($item))
                $filter_str .= $key.':'.$item.', ';
        }
        rtrim($filter_str, ',');

        $richText->createText ( 
            "\n" . $title . "\n" .
            "On: ".date('Y-m-d')." | By User: ".Yii::$app->user->full_name . "\n" . 
            "filters: " .(!empty($filter_str) ? $filter_str : "(not set)" )
        );

        return $richText;
    }

    /**
     * Displays All Assets to filter.
     *
     * @return string
     */
    public function actionAsset(){
        $searchModel = new Asset();
        $searchModel->scenario = 'search';
        $dataProvider = $searchModel->indexSearch(Yii::$app->request->queryParams);
        return $this->render('asset', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'mvStatuses' => $searchModel->getMvStatuses(),
           'statuses' => $searchModel->getStatuses(),
           'locations' => Location::getLocations(),
           'mntTypes' => MaintenanceType::getTypes(),
           // workorder
           'maintenances' => Maintenance::listMaintenances(),
           'woStatuses' => WorkOrder::getAllStatuses(),
           'woTypes' => WorkOrder::getTypes(),
           'technicians' => User::getUsers(),
           //fialure
           'failTypes' => Failure::getTypes(),
           //job
           'clients' => Client::getClients(),
           'muds' => Job::getMuds(),
           //movement
           'mvTypes' => Movement::getTypes(),
           'mvStats' => Movement::getStatuses(),
        ]);
    }

    public function actionAssetReport()
    {
        $searchModel = new Asset();
        $searchModel->scenario = 'search';
        $mvStatuses = $searchModel->getMvStatuses();
        $statuses = $searchModel->getStatuses();

        $dataProvider = $searchModel->indexSearch(Yii::$app->request->queryParams, Yii::$app->request->post());

        $filters = [];
        if(isset(Yii::$app->request->queryParams['Asset']))
            $filters = Yii::$app->request->queryParams['Asset'];

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'landscape',
                'fitToPage' => true,
                //'fitToWidth' => true,
                //'fitToHeight' => false,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Asset Report",$filters),
            'columns' => [
                'finance_no',
                [
                    'attribute' => 'asset_code', 
                    'value' => 'assetCode.name', 
                    'label' => 'Asset Code',
                ],
                'serial_no',
                'part_no',
                [
                    'attribute' => 'maintenance_type', 
                    'label' => 'Mnt Types',
                    'format' => 'raw',
                    'value' => function($data, $row){
                        $result = [];
                        foreach ($data->assetCode->assetCodeMaintenances as $acm) {
                            if(!in_array($acm->maintenance->maintenanceType['code'], $result))
                            $result[] = $acm->maintenance->maintenanceType['code'];
                        }
                        return implode("\n", $result);          
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'location', 
                    'value' => 'location.location_name', 
                    'label' => 'Owner',
                ],
                [
                    'attribute' => 'physical_location', 
                    'value' => 'physicalLocation.location_name', 
                    'label' => 'Located',
                ],
                [
                    'attribute' => 'asset_cell', 
                    'value' => 'cell.name', 
                    'label' => 'Cell',
                ],
                [
                    'attribute' => 'asset_set', 
                    'value' => 'set.name', 
                    'label' => 'Set',
                ],
                [
                    'attribute' =>'movement_status',
                    'label' => 'MvStat',
                    'value' => function($data, $row) use ($mvStatuses){
                        return $mvStatuses[$data->movement_status];
                    },
                ],
                [
                    'attribute' =>'status',
                    'format' => 'raw',
                    'value' => function($data, $row) use ($statuses){
                        $results = MaintnanceComp::find_status([
                            'asset_id' => $data->asset_id,
                            'status' => $data->status, 
                            'stat_str' => $statuses[$data->status], 
                            'maintenances' => $data->assetCode->assetCodeMaintenances
                        ], 'report');

                        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                        foreach ($results as $i => $result) {
                            $payable = $richText->createTextRun(($i > 0 ? "\n": "").$result['stat']);
                            $payable->getFont()->setBold(true);
                            $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $result['color'] ) );
                        }
                        return $richText;
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'remarks',
                    'value' => function($data){
                        return self::breakContent($data->remarks);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'description',
                    'value' => function($data){
                        return self::breakContent($data->description);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ]
            ],
        ]);

        $file_name = 'asset_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }


    public function actionWorkOrder()
    {
        $asset_query = isset(Yii::$app->request->queryParams['Asset']) ? Yii::$app->request->queryParams['Asset'] : []; 
        $post = Yii::$app->request->post();

        $searchModel = new WorkOrder();
        $searchModel->scenario = 'search';
        $types = $searchModel->getTypes();
        $statuses = $searchModel->getAllStatuses();
        $maintenances = Maintenance::listMaintenances();
        $dataProvider = $searchModel->reportSearch($post, $asset_query);

        $filters = ($post['limited'] == 'filtered') ? $asset_query : [];
        if(isset($post['WorkOrder'])){
            
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("WorkOrder Report",$filters),
            'columns' => [
                'wo_code',
                [
                    'attribute' => 'date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'done_date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'wo_maintenance', 
                    'label' => 'Maintenance',
                    'value' => 'mnt.name'
                ],
                [
                    'attribute' =>'status',
                    'value' => function($data, $row) use ($statuses){
                        return $statuses[$data->status];
                    }
                ],
                [
                    'attribute' => 'wo_user', 
                    'label' => 'User',
                    'value' => 'user.full_name'
                ],
                [
                    'attribute' => 'wo_assigned', 
                    'label' => 'Assigned to',
                    'value' => 'assignedTo.full_name'
                ],
                [
                    'attribute' =>'type',
                    'filter'=> $types,
                    'value' => function($data, $row) use ($types){
                        return $types[$data->type];
                    }
                ],
                [
                    'attribute' => 'action',
                    'value' => function($data){
                        return self::breakContent($data->action);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'work_order('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionFailure()
    {
        $asset_query = isset(Yii::$app->request->queryParams['Asset']) ? Yii::$app->request->queryParams['Asset'] : []; 
        $post = Yii::$app->request->post();

        $searchModel = new Failure();
        $searchModel->scenario = 'search';
        $types = $searchModel->getTypes();
        $mntTypes = MaintenanceType::getTypes();
        $dataProvider = $searchModel->reportSearch($post, $asset_query);

        $filters = $post['limited'] == 'filtered' ? $asset_query : [];
        if(isset($post['Failure'])){
            
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'landscape',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Failure Report",$filters),
            'columns' => [
                'failure_code',
                [
                    'attribute' => 'date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'failure_reason', 
                    'label' => 'Reference of cause',
                ],
                [
                    'attribute' => 'type', 
                    'value' => function($data, $row) use ($types){
                        return $types[$data->type];
                    }
                ],
                [
                    'attribute' => 'failure_mnt', 
                    'label' => 'Maintenance Type',
                    'value' => function($data, $row) use ($mntTypes){
                        if($data->type == Failure::TYPE_FISH)
                            return "- All -";
                        return $mntTypes[$data->mnt_type_id];
                    }
                ],
                [
                    'attribute' => 'failure_user', 
                    'label' => 'User',
                    'value' => 'user.full_name'
                ],
                [
                    'attribute' => 'brief_report',
                    'value' => function($data){
                        return self::breakContent($data->brief_report);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'immediate_action',
                    'value' => function($data){
                        return self::breakContent($data->immediate_action);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'failure('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionJob()
    {
        $asset_query = isset(Yii::$app->request->queryParams['Asset']) ? Yii::$app->request->queryParams['Asset'] : []; 
        $post = Yii::$app->request->post();

        $searchModel = new Job();
        $searchModel->scenario = 'search';
        $muds = Job::getMuds();
        $dataProvider = $searchModel->reportSearch($post, $asset_query);

        $filters = $post['limited'] == 'filtered' ? $asset_query : [];
        if(isset($post['Job'])){
            
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'landscape',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Job Report",$filters),
            'columns' => [
                'job_code',
                [
                    'attribute' => 'date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'job_client', 
                    'label' => 'Client',
                    'value' => 'client.name'
                ],
                [
                    'attribute' => 'job_location', 
                    'label' => 'Location',
                    'value' => 'location.location_name'
                ],
                [
                    'attribute' => 'well', 
                    'label' => 'Well/Rig'
                ],
                [
                    'attribute' => 'job_user', 
                    'label' => 'User',
                    'value' => 'user.full_name'
                ],
                [
                    'attribute' => 'Mud type', 
                    'value' => function($data, $row) use ($muds){
                        if(!empty($data->mud_type))
                            return $muds[$data->mud_type];
                        return '';
                    }
                ],
                [
                    'attribute' => 'mud_wt', 
                    'label' => 'Mud Wt',
                ],
                [
                    'attribute' => 'remarks',
                    'value' => function($data){
                        return self::breakContent($data->remarks);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'job('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionMovement()
    {
        $asset_query = isset(Yii::$app->request->queryParams['Asset']) ? Yii::$app->request->queryParams['Asset'] : []; 
        $post = Yii::$app->request->post();

        $searchModel = new Movement();
        $searchModel->scenario = 'search';
        $mvTypes = Movement::getTypes();
        $mvStats = Movement::getStatuses();
        $dataProvider = $searchModel->reportSearch($post, $asset_query);

        $filters = $post['limited'] == 'filtered' ? $asset_query : [];
        if(isset($post['Job'])){
            
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'landscape',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Movement Report",$filters),
            'columns' => [
                'mv_code',
                [
                    'attribute' => 'date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'Type', 
                    'value' => function($data, $row) use ($mvTypes){
                        return $mvTypes[$data->type];
                    }
                ],
                [
                    'attribute' => 'movement_user', 
                    'label' => 'User',
                    'value' => 'user.full_name'
                ],
                [
                    'attribute' => 'movement_origin', 
                    'label' => 'Origin',
                    'value' => 'originLocation.location_name'
                ],
                [
                    'attribute' => 'movement_destination', 
                    'label' => 'Destination',
                    'value' => 'destinationLocation.location_name'
                ],
                [
                    'attribute' => 'deliver_date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                [
                    'attribute' => 'Status', 
                    'value' => function($data, $row) use ($mvStats){
                        return $mvStats[$data->status];
                    }
                ],
                [
                    'attribute' => 'remarks',
                    'value' => function($data){
                        return self::breakContent($data->remarks);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'movement('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionUpcomingActivity(){
        $post = Yii::$app->request->post();
        if(empty($post['end_date']))
            $post['end_date'] = date('Y-m', strtotime('+3 month', time()));

        $searchModel = new Asset();
        $searchModel->scenario = 'search';
        $mvStatuses = $searchModel->getMvStatuses();
        $statuses = $searchModel->getStatuses();

        $dataProvider = $searchModel->indexSearch(Yii::$app->request->queryParams, $post);

        $filters = [];
        if(isset(Yii::$app->request->queryParams['Asset']))
            $filters = Yii::$app->request->queryParams['Asset'];

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                //'fitToWidth' => true,
                //'fitToHeight' => false,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Upcoming Report",$filters),
            'columns' => [
                [
                    'attribute' => 'asset_det', 
                    'value' => function ($data, $row){
                        return $data->assetCode->name."\t\n".
                               $data->serial_no."\t\n".
                               $data->part_no;
                    },
                    'label' => "Asset Code\t\nSerial No.\t\nPart No.",
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'maintenance_type', 
                    'label' => 'Mnt Types',
                    'format' => 'raw',
                    'value' => function($data, $row){
                        $result = [];
                        foreach ($data->assetCode->assetCodeMaintenances as $acm) {
                            if(!in_array($acm->maintenance->maintenanceType['code'], $result))
                            $result[] = $acm->maintenance->maintenanceType['code'];
                        }
                        return implode("\n", $result);          
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'physical_location', 
                    'value' => function($data, $row){
                        return ($data->physicalLocation ? $data->physicalLocation->location_name."\t\n" : "").
                               ($data->cell ? $data->cell->name."\t\n" : "").
                               ($data->set ? $data->set->name : "");
                    },
                    'label' => "Located\t\nCell.\t\nSet.",
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' =>'movement_status',
                    'label' => 'MvStat',
                    'value' => function($data, $row) use ($mvStatuses){
                        return $mvStatuses[$data->movement_status];
                    },
                ],
                [
                    'attribute' =>'status',
                    'format' => 'raw',
                    'value' => function($data, $row) use ($statuses,$post){
                        $results = MaintnanceComp::upcomingReport([
                            'asset_id' => $data->asset_id,
                            'status' => $data->status, 
                            'stat_str' => $statuses[$data->status], 
                            'maintenances' => $data->assetCode->assetCodeMaintenances,
                            'end_date' => $post['end_date']
                        ], 'report');

                        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                        foreach ($results as $i => $result) {
                            $payable = $richText->createTextRun(($i > 0 ? "\n": "").$result['stat']);
                            $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $result['color'] ) );
                        }
                        return $richText;
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'upcoming_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionMaintenanceBoard(){
        $post = Yii::$app->request->post();
        if(empty($post['start_date']))
            $post['start_date'] = date('Y-m');
        if(empty($post['end_date']))
            $post['end_date'] = date('Y-m', strtotime('+11 month', time()));

        $searchModel = new Asset();
        $searchModel->scenario = 'search';
        $mvStatuses = $searchModel->getMvStatuses();
        $statuses = $searchModel->getStatuses();

        $dataProvider = $searchModel->indexSearch(Yii::$app->request->queryParams, $post);

        $filters = [];
        if(isset(Yii::$app->request->queryParams['Asset']))
            $filters = Yii::$app->request->queryParams['Asset'];

        $head_array [] = "Year\nMonth";
        $month = strtotime ( $post['start_date'] );
        while ( $month <= strtotime ( $post['end_date'] ) ) {
            $head_array [] = date ( 'y', $month ) . "\n" . date ( 'M', $month );
            $month = strtotime ( "+1 month", $month );
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                //'fitToWidth' => true,
                //'fitToHeight' => false,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("MaintenanceBoard Report",$filters),
            'has_extended' => [
                '5' => $head_array  // column => headers array,
            ],
            // 'headerColumnUnions' => [
            //     [
            //         'header' => 'Technical Status',
            //         'offset' => 4,
            //     ],
            // ],
            'columns' => [
                [
                    'attribute' => 'asset_det', 
                    'value' => function ($data, $row){
                        return $data->assetCode->name."\t\n".
                               $data->serial_no."\t\n".
                               $data->part_no;
                    },
                    'label' => "Asset Code\t\nSerial No.\t\nPart No.",
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'maintenance_type', 
                    'label' => 'Mnt Types',
                    'format' => 'raw',
                    'value' => function($data, $row){
                        $result = [];
                        foreach ($data->assetCode->assetCodeMaintenances as $acm) {
                            if(!in_array($acm->maintenance->maintenanceType['code'], $result))
                            $result[] = $acm->maintenance->maintenanceType['code'];
                        }
                        return implode("\n", $result);          
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'physical_location', 
                    'value' => function($data, $row){
                        return ($data->physicalLocation ? $data->physicalLocation->location_name."\t\n" : "").
                               ($data->cell ? $data->cell->name."\t\n" : "").
                               ($data->set ? $data->set->name : "");
                    },
                    'label' => "Located\t\nCell.\t\nSet.",
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' =>'movement_status',
                    'label' => 'MvStat',
                    'value' => function($data, $row) use ($mvStatuses){
                        return $mvStatuses[$data->movement_status];
                    },
                ],
                [
                    //'attribute' =>'status',
                    'format' => 'raw',
                    'value' => function($data, $row) use ($statuses,$post){
                        $results = MaintnanceComp::mntboardReport([
                            'asset_id' => $data->asset_id,
                            'status' => $data->status, 
                            'stat_str' => $statuses[$data->status], 
                            'maintenances' => $data->assetCode->assetCodeMaintenances,
                            'start_date' => $post['start_date'],
                            'end_date' => $post['end_date']
                        ], 'report');

                        $return = [];
                        foreach ($results as $index => $result) {
                            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                            foreach ($result as $i => $stat) {
                                $payable = $richText->createTextRun(($i > 0 ? "\n": "").$stat['stat']);
                                $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $stat['color'] ) );
                            }
                            $return[$index] = $richText;
                        }

                        return serialize($return);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
            ],
        ]);

        $file_name = 'mntboard_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }


    public function actionHistoryCard(){
        $post = Yii::$app->request->post();

        if(empty($post['selected_asset']))
            throw new NotAcceptableHttpException('Please choose an asset before...');
        if(empty($post['items']))
            throw new NotAcceptableHttpException('Please choose one or more items to report (WorkOrder, Movement, ...)');

        $filters = $post['items'];

        $asset_id = explode(',', $post['selected_asset'])[0];
        $from_month = isset($post['month']) ? date("Y-m-d",strtotime("-".$post['month']." Months")) : null;

        $searchModel = new Asset();
        $asset = $searchModel->findAsset($asset_id);
        $header = "HistoryCard Report \n".
                  "Choosen Asset <<AssetCode: ". $asset->assetCode->name." | ".
                  "Serial No: ". $asset->serial_no." | ".
                  "Part No: ". $asset->part_no . ">>";

        $results = $searchModel->historySearch($asset_id,$post['items'],$from_month);
        $dataProvider = MaintnanceComp::get_history($post['items'], $results);

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                //'fitToWidth' => true,
                //'fitToHeight' => false,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader($header,$filters),
            'columns' => [
                [
                    'attribute' => 'done_date',
                    'format'=>['DateTime','php:Y-m-d']
                ],
                'event_type',
                'event_code',
                [
                    'attribute' => 'remarks',
                    'value' => function($data){
                        return self::breakContent($data['remarks']);
                    },
                    'contentOptions' => [
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => 'center'
                        ]
                    ]
                ],
                [
                    'attribute' => 'done_by', 
                    'label' => 'Assigned/Done by',
                ],
            ],
        ]);

        $file_name = 'historycard_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionEfficiency(){
        return $this->render('efficiency', [
           'locations' => Location::getLocations(),
           'mnt_types' => MaintenanceType::getTypes(),
           'maintenances' => Maintenance::listMaintenances(),
        ]);
    }

    public function actionMntEfficiency(){
        $locations = Location::getLocations();
        $Maintenances = Maintenance::listMaintenances();

        $post = Yii::$app->request->post();
        if(empty($post['compare_date']))
            throw new NotAcceptableHttpException('Please choose a date to compare...');

        //mnt_ids
        $mnt_ids = [];
        if(isset($post['mnt_id'])){
            foreach ($post['mnt_id'] as $mnt_id)
                $mnt_ids[$mnt_id] = $Maintenances[$mnt_id];

            $selected_mnts = $mnt_ids;
        }else{
            $selected_mnts = $Maintenances;
        }

        $filters = [
            "maintenance types" => implode(',', $mnt_ids),
            "compare date" => $post['compare_date'],
        ];
       
        $searchModel = new Asset();
        $results = $searchModel->efficiencySearch($post);
        $dataProvider = MaintnanceComp::get_efficiency($results, $post);

        $columns = [
            [
                'attribute' => 'loc', 
                'label' => 'Location',
            ],
            [
                'attribute' => 'cell', 
                'label' => 'Cell',
            ],
            [
                'attribute' => 'date', 
                'label' => date("Y M d", strtotime($post['compare_date']."-".date("d"))),
            ],
            [
                'attribute' => 'today', 
                'label' => date("Y M d"),
            ],
        ];

        foreach ($selected_mnts as $key => $value) {
            $columns[] = [
                'attribute' => $key,
                'label' => $value,
                'value' => function($data) use ($key){
                    return isset($data[$key]) ? $data[$key] : "NA";
                }
            ];
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Maintenance Efficiency Report",$filters),
            'colorize_if' => ['value' => 'Total', 'color' => '66FF99'],
            'columns' => $columns,
        ]);

        $file_name = 'mnt_efficiency_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionTotalEfficiency(){
        $locations = Location::getLocations();
        $mnt_types = MaintenanceType::getTypes();
        
        $post = Yii::$app->request->post();
        if(empty($post['compare_date']))
            throw new NotAcceptableHttpException('Please choose a date to compare...');

        //mnt_types
        $choosen_mnt = [];
        if(isset($post['mnt_type_id'])){
            foreach ($post['mnt_type_id'] as $mnt_type)
                $choosen_mnt[$mnt_type] = $mnt_types[$mnt_type];

            $selected_mnts = $choosen_mnt;
        }else{
            $selected_mnts = $mnt_types;
        }

        $filters = [
            "maintenance types" => implode(',', $choosen_mnt),
            "compare date" => $post['compare_date'],
        ];

        $searchModel = new Asset();
        $results = $searchModel->efficiencySearch($post);
        $dataProvider = MaintnanceComp::get_total_Efficiency($results, $post);

        $columns = [
            [
                'attribute' => 'loc', 
                'label' => 'Location',
            ],
            [
                'attribute' => 'cell', 
                'label' => 'Cell',
            ],
            [
                'attribute' => 'date', 
                'label' => date("Y M d", strtotime($post['compare_date']."-".date("d"))),
            ],
            [
                'attribute' => 'today', 
                'label' => date("Y M d"),
            ],
        ];

        foreach ($selected_mnts as $key => $value) {
            $columns[] = [
                'attribute' => $key,
                'label' => $value,
                'value' => function($data) use ($key){
                    return isset($data[$key]) ? $data[$key] : "NA";
                }
            ];
        }

        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Maintenance Efficiency Report",$filters),
            'colorize_if' => ['value' => 'Total', 'color' => '66FF99'],
            'columns' => $columns,
        ]);

        $file_name = 'total_efficiency_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }

    public function actionKpi(){
        if (!Yii::$app->request->isPost)
            return $this->render('kpi', [
               'locations' => Location::getLocations()
            ]);

        $post = Yii::$app->request->post();
        $filters = [];

        $searchModel = new Asset();
        $results = $searchModel->efficiencySearch($post);
        $dataProvider = MaintnanceComp::get_kpi($results, $post['location']);

        //-------------------------------------------------------------------
        $Maintenances = Maintenance::listMaintenances();
        $columns = [
            [
                'attribute' => 'date', 
                'label' => 'Date',
            ],
            [
                'attribute' => 'cell', 
                'label' => 'Cell',
            ],
        ];

        foreach ($Maintenances as $key => $value) {
            $columns[] = [
                'attribute' => $key,
                'label' => $value,
                'value' => function($data) use ($key){
                    if(isset($data[$key])){
                        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                        $payable = $richText->createTextRun($data[$key]['value']);
                        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $data[$key]['color'] ) );
                        return $richText;
                    }
                    return "NA";
                }
            ];
        }
        $columns[] = [
            'attribute' => 'total', 
            'label' => 'Total',
            'value' => function($data){
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($data['total']['value']);
                $payable->getFont()->setBold(true);
                $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $data['total']['color'] ) );
                return $richText;
            }
        ];

        $exporter = (new Spreadsheet([
            'title' => 'KPI',
            'dataProvider' => $dataProvider['kpi'],
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("KPI Report",$filters),
            'colorize_if' => ['value' => 'Total', 'color' => 'b6e5ef'],
            'columns' => $columns,
        ]))->render();

        //---------------------------------------------------------------------------------
        $mnt_types = MaintenanceType::getTypes();
        $columns = [
            [
                'attribute' => 'date', 
                'label' => 'Date',
            ],
            [
                'attribute' => 'cell', 
                'label' => 'Cell',
            ],
        ];

        foreach ($mnt_types as $key => $value) {
            $columns[] = [
                'attribute' => $key,
                'label' => $value,
                'value' => function($data) use ($key){
                    if(isset($data[$key])){
                        $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                        $payable = $richText->createTextRun($data[$key]['value']);
                        $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $data[$key]['color'] ) );
                        return $richText;
                    }
                    return "NA";
                }
            ];
        }
        $columns[] = [
            'attribute' => 'total', 
            'label' => 'Total',
            'value' => function($data){
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($data['total']['value']);
                $payable->getFont()->setBold(true);
                $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( $data['total']['color'] ) );
                return $richText;
            }
        ];

        $exporter->configure([ // update spreadsheet configuration
            'title' => 'Total KPI',
            'dataProvider' => $dataProvider['total'],
            'dimensionOptions' => [
                'autoSize' => true
            ],
            'contentOptions' => [
                'alignment' => [
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ],
            'striped' => true,
            'pageSettings' => [
                'orientation' => 'portrait',
                'fitToPage' => true,
                'paperSize' => \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
            ],
            'sheetHead'=>$this->GenerateHeader("Total KPI Report",$filters),
            'colorize_if' => ['value' => 'Total', 'color' => 'b6e5ef'],
            'columns' => $columns,
        ])->render(); // call `render()` to create a single worksheet


        $file_name = 'kpi_report('.Yii::$app->user->id.'-'.time().')';
        $exporter->sendOutput($file_name.'.xlsx');
    }
}
