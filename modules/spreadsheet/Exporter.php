<?php
namespace app\modules\spreadsheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\helpers\FileHelper;
use yii\i18n\Formatter;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\Response;

class Exporter extends Component
{
    /**
     * @var bool whether to show the header section of the sheet.
     */
    public $showHeader = true;
    /**
     * @var bool whether to show the footer section of the sheet.
     */
    public $showFooter = false;
    /**
     * @var string|null sheet title.
     */
    public $title;
    /**
     * @var string the HTML display when the content of a cell is empty.
     * This property is used to render cells that have no defined content,
     * e.g. empty footer or filter cells.
     *
     * Note that this is not used by the [[DataColumn]] if a data item is `null`. In that case
     * the [[nullDisplay]] property will be used to indicate an empty data value.
     */
    public $emptyCell = '';
    /**
     * @var string the text to be displayed when formatting a `null` data value.
     */
    public $nullDisplay = '';
    /**
     * @var string writer type (format type). If not set, it will be determined automatically.
     * Supported values:
     *
     * - 'Xls'
     * - 'Xlsx'
     * - 'Ods'
     * - 'Csv'
     * - 'Html'
     * - 'Tcpdf'
     * - 'Dompdf'
     * - 'Mpdf'
     *
     * @see IOFactory
     */
    public $writerType;
    /**
     * @var int|null current sheet row index.
     * Value of this field automatically changes during spreadsheet rendering. After rendering is complete,
     * it will contain the number of the row next to the latest fill-up one.
     * Note: be careful while manually manipulating value of this field as it may cause unexpected results.
     */
    public $rowIndex;

    public $rowOfset = 0;

    /**
     * @var bool whether spreadsheet has been already rendered or not.
     */
    protected $isRendered = false;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet|null spreadsheet document representation instance.
     */
    private $_document;
    /**
     * @var array|Formatter the formatter used to format model attribute values into displayable texts.
     * This can be either an instance of [[Formatter]] or an configuration array for creating the [[Formatter]]
     * instance. If this property is not set, the "formatter" application component will be used.
     */
    private $_formatter;

    public $pageSettings = [];

    public $sheetHead = [];

    public $start_col = 'A';

    public $last_col = 'Z';

    /**
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet spreadsheet document representation instance.
     */
    public function getDocument()
    {
        if (!is_object($this->_document)) {
            $this->_document = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        }
        return $this->_document;
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet|null $document spreadsheet document representation instance.
     */
    public function setDocument($document)
    {
        $this->_document = $document;
    }

    /**
     * @return Formatter formatter instance.
     */
    public function getFormatter()
    {
        if (!is_object($this->_formatter)) {
            if ($this->_formatter === null) {
                $this->_formatter = Yii::$app->getFormatter();
            } else {
                $this->_formatter = Instance::ensure($this->_formatter, Formatter::class);
            }
        }
        return $this->_formatter;
    }

    /**
     * @param array|Formatter $formatter formatter instance.
     */
    public function setFormatter($formatter)
    {
        $this->_formatter = $formatter;
    }

    /**
     * Sets spreadsheet document properties.
     * @param array $properties list of document properties in format: name => value
     * @return $this self reference.
     * @see \PhpOffice\PhpSpreadsheet\Document\Properties
     */
    public function properties($properties)
    {
        $documentProperties = $this->getDocument()->getProperties();
        foreach ($properties as $name => $value) {
            $method = 'set' . ucfirst($name);
            call_user_func([$documentProperties, $method], $value);
        }
        return $this;
    }

    /**
     * Configures (re-configures) this Exporter with the property values.
     * This method is useful for rendering multisheet documents. For example:
     *
     * ```php
     * (new Exporter([
     *     'title' => 'Monitors',
     * ]))
     * ->render()
     * ->configure([
     *     'title' => 'Mouses',
     * ])
     * ->save('/path/to/export/files/office-equipment.xls');
     * ```
     *
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return $this self reference.
     */
    public function configure($properties)
    {
        Yii::configure($this, $properties);
        return $this;
    }

    /**
     * Performs actual document composition.
     * @return $this self reference.
     */
    public function render()
    {
        $document = $this->getDocument();

        if ($this->isRendered) {
            // second run
            $document->createSheet();
            $document->setActiveSheetIndex($document->getActiveSheetIndex() + 1);
        }

        if ($this->title !== null) {
            $document->getActiveSheet()->setTitle($this->title);
        }

        $this->rowIndex = 1;
        $this->rowIndex += $this->rowOfset;

        if ($this->showHeader) {
            $this->renderHeader();
        }

        if ($this->showFooter) {
            $this->renderFooter();
        }

        $this->isRendered = true;

        $this->gc();

        return $this;
    }

    protected function renderLogo(){
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(Yii::getAlias('@webroot') .'/images/export-logo.png');
        $drawing->setCoordinates($this->start_col.$this->rowIndex);                      

        $drawing->setWorksheet($this->getDocument()->getActiveSheet());
    }

    protected function renderHeader(){
        if(!empty($this->sheetHead)){
            $this->renderLogo();
            $sheet = $this->getDocument()->getActiveSheet();

            $cols_name = range('A','Z');
            $first_index = array_search($this->start_col, $cols_name);
            $last_index = array_search($this->last_col, $cols_name); 

            //find middle
            $title_index = (int)(($last_index - $first_index) / 2);
            $height = isset($this->sheetHead['height']) ? $this->sheetHead['height'] : 1;
            $top = (int)($height / 2);
            //render title
            $sheet->setCellValue($cols_name[$title_index].($this->rowIndex+$top),$this->sheetHead['title']);
            $this->applyCellStyle($cols_name[$title_index].($this->rowIndex+$top), [
                'font' => ['bold' => true],
            ]);

            //render date and no
            if(isset($this->sheetHead['no'])){
                $no_col = $cols_name[$last_index - 1];
                $sheet->setCellValue($no_col.($this->rowIndex+$height-2),$this->sheetHead['no']);
            }
            
            //render date and no
            if(isset($this->sheetHead['date'])){
                $no_col = $cols_name[$last_index - 1];
                $sheet->setCellValue($no_col.($this->rowIndex+$height-1),$this->sheetHead['date']);
            }

            $this->rowIndex += $height;
            return;
        }
    }

    public function renderSeperator($title, $style){
        $sheet = $this->getDocument()->getActiveSheet();
        $this->renderCell($this->start_col.$this->rowIndex,$title);
        $sheet->mergeCells($this->start_col.$this->rowIndex .':'. $this->last_col.$this->rowIndex);
        $this->applyCellStyle($this->start_col.$this->rowIndex .':'. $this->last_col.$this->rowIndex,$style);
        $this->rowIndex++;
    }

    public function BorderedNexLine($style){
        $this->applyCellStyle($this->start_col.$this->rowIndex .':'. $this->last_col.$this->rowIndex,$style);
        $this->rowIndex++;
    }

    /**
     * Renders cell with given coordinates.
     * @param string $cell cell coordinates, e.g. 'A1', 'B4' etc.
     * @param string $content cell raw content.
     * @param array $style cell style options.
     * @return $this self reference.
     */
    public function renderCell($cell, $content, $style = [])
    {
        $sheet = $this->getDocument()->getActiveSheet();
        $vcell = (strpos($cell, ':') !== false) ? explode(":", $cell)[0] : $cell;
        $sheet->setCellValue($vcell, $content);
        if($vcell !== $cell)
            $this->mergeCells($cell);
        $this->applyCellStyle($cell, $style);
        return $this;
    }

    public function renderList($cell, $items, $allowBlank=true, $showInputMsg=true, $showErrorMsg=true){
        $validation = $this->getDocument()->getActiveSheet()->getCell($cell)->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank($allowBlank);
        $validation->setShowInputMessage($showInputMsg);
        $validation->setShowErrorMessage($showErrorMsg);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1('"'.implode(',', $items).'"');
    }

    public function renderListByRef($cell, $range_name, $allowBlank=true, $showInputMsg=true, $showErrorMsg=true){
        $validation = $this->getDocument()->getActiveSheet()->getCell($cell)->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank($allowBlank);
        $validation->setShowInputMessage($showInputMsg);
        $validation->setShowErrorMessage($showErrorMsg);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Value is not in list.');
        $validation->setPromptTitle('Pick from list');
        $validation->setPrompt('Please pick a value from the drop-down list.');
        $validation->setFormula1("=$range_name");
    }

    public function createList($name, $title, $title_style, $col, $items){
        $i = 1;
        $this->renderCell($col.$i, $title, $title_style);
        foreach ($items as $value) {
            $i++;
            $this->renderCell($col.$i, $value);
        }

        $this->getDocument()->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

        $this->getDocument()->addNamedRange( 
            new \PhpOffice\PhpSpreadsheet\NamedRange(
                $name, 
                $this->getDocument()->getActiveSheet(), 
                $col.'2:'.$col.($i)
            ) 
        ); 
    }

    /**
     * Applies cell style from configuration.
     * @param string $cell cell coordinates, e.g. 'A1', 'B4' etc.
     * @param array $style style configuration.
     * @return $this self reference.
     * @throws \PhpOffice\PhpSpreadsheet\Exception on failure.
     */
    public function applyCellStyle($cell, $style)
    {
        if (empty($style)) {
            return $this;
        }

        $cellStyle = $this->getDocument()->getActiveSheet()->getStyle($cell);
        if (isset($style['alignment'])) {
            $cellStyle->getAlignment()->applyFromArray($style['alignment']);
            unset($style['alignment']);
            if (empty($style)) {
                return $this;
            }
        }
        $cellStyle->applyFromArray($style);

        return $this;
    }

    /**
     * Merges sell range into single one.
     * @param string $cellRange cell range (e.g. 'A1:E1').
     * @return $this self reference.
     * @throws \PhpOffice\PhpSpreadsheet\Exception on failure.
     */
    public function mergeCells($cellRange)
    {
        $this->getDocument()->getActiveSheet()->mergeCells($cellRange);
        return $this;
    }

    public function sendOutput($attachmentName, $options = [])
    {
        if (!$this->isRendered) {
            $this->render();
        }

        $writerType = $this->writerType;
        if ($writerType === null) {
            $fileExtension = strtolower(pathinfo($attachmentName, PATHINFO_EXTENSION));
            $writerType = ucfirst($fileExtension);
        }

        $writer = IOFactory::createWriter($this->getDocument(), $writerType);

        $filename = Yii::getAlias('@webroot') .'/uploads/reportTmp/'.$attachmentName;
        $writer->save($filename);

        $response = Yii::$app->getResponse();
        $response->on(Response::EVENT_AFTER_SEND, function() use ($filename) {
            unlink($filename);
        });
        return $response->sendFile($filename, $attachmentName, $options);
    }

    /**
     * Performs PHP memory garbage collection.
     */
    protected function gc()
    {
        if (!gc_enabled()) {
            gc_enable();
        }
        gc_collect_cycles();
    }
}