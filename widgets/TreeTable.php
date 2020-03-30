<?php
namespace app\widgets;

use yii\helpers\Html;
use yii\grid\GridView;
use app\assets\TreeTableAsset;

class TreeTable extends GridView
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        TreeTableAsset::register($this->getView());
        Html::addCssClass($this->tableOptions, 'treetable');
    }

    /**
     * Renders a table body row with id and parentId, needed for ActsAsTreeTable
     * jQuery extension.
     * @param integer $row the row number (zero-based).
     */
    public function renderTableRow($model, $key, $index)
    {
        $option = $model->parent_id
                ? ['data-tt-parent-id' => $model->parent_id, 'data-tt-id' => $key]
                : ['data-tt-id' => $key];

        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

        return Html::tag('tr', implode('', $cells), array_merge($options, $option));
    }
}