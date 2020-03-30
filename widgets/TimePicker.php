<?php

namespace app\widgets;

use Yii;
use yii\helpers\FormatConverter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\jui\DatePicker;
use app\assets\TimePickerAsset;

class TimePicker extends DatePicker
{
    /**
     * @var boolean If true, will omit the date portion of the widget.
     */
    public $timeOnly = false;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $picker = $this->timeOnly ? 'timepicker' : 'datetimepicker';

        echo $this->renderWidget() . "\n";

        $containerID = $this->inline ? $this->containerOptions['id'] : $this->options['id'];
        $language = $this->language ? $this->language : Yii::$app->language;

        if (strncmp($this->dateFormat, 'php:', 4) === 0) {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDatePhpToJui(substr($this->dateFormat, 4));
        } else {
            $this->clientOptions['dateFormat'] = FormatConverter::convertDateIcuToJui($this->dateFormat, 'date', $language);
        }

        if ($language !== 'en-US') {
            $view = $this->getView();
            $assetBundle = DatePickerLanguageAsset::register($view);
            $assetBundle->language = $language;
            $options = Json::htmlEncode($this->clientOptions);
            $language = Html::encode($language);
            $view->registerJs("jQuery('#{$containerID}').{$picker}($.extend({}, $.{$picker}.regional['{$language}'], $options));");
        } else {
            $this->registerClientOptions($picker, $containerID);
        }

        $this->registerClientEvents($picker, $containerID);
        TimePickerAsset::register($this->getView());
    }

    protected function renderWidget()
    {
        $contents = [];

        // get formatted date value
        if ($this->hasModel()) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        if ($value !== null && $value !== '') {
            // format value according to dateFormat
            try {
                $value = Yii::$app->formatter->asTime($value, $this->dateFormat);
            } catch(InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        $options = $this->options;
        $options['value'] = $value;

        if ($this->inline === false) {
            // render a text input
            if ($this->hasModel()) {
                $contents[] = Html::activeTextInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::textInput($this->name, $value, $options);
            }
        } else {
            // render an inline date picker with hidden input
            if ($this->hasModel()) {
                $contents[] = Html::activeHiddenInput($this->model, $this->attribute, $options);
            } else {
                $contents[] = Html::hiddenInput($this->name, $value, $options);
            }
            $this->clientOptions['defaultDate'] = $value;
            $this->clientOptions['altField'] = '#' . $this->options['id'];
            $contents[] = Html::tag('div', null, $this->containerOptions);
        }

        return implode("\n", $contents);
    }
}
