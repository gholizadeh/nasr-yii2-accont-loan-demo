<?php

namespace app\widgets;

use yii\helpers\Html;
use yii\jui\InputWidget;

class AutoCompleteGroup extends InputWidget
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $js = "$.widget(\"custom.autocompletegroup\", $.ui.autocomplete, {
                    _renderMenu: function( ul, items ) {
                        var currentCategory = '';
                        var that = this;
                        $.each( items, function( index, item ) {
                            if ( item.category != currentCategory ) {
                                ul.append( \"<li class='font-weight-bold pl-2 text-secondary' aria-label='\"+item.category+\"' class='ui-autocomplete-category \" + item.category + \"'>\" + item.category + \"</li>\" );
                            currentCategory = item.category;
                            }
                            li = that._renderItemData( ul, item );
                            li.addClass('pl-3');
                        });
                    }
                });";
        $this->getView()->registerJs($js);
        $this->registerWidget('autocompletegroup');
        return $this->renderWidget();
    }

    /**
     * Renders the AutoComplete widget.
     * @return string the rendering result.
     */
    public function renderWidget()
    {
        if ($this->hasModel()) {
            return Html::activeTextInput($this->model, $this->attribute, $this->options);
        }
        return Html::textInput($this->name, $this->value, $this->options);
    }
}
