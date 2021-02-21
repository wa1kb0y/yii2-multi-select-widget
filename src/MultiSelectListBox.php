<?php

/*
 * This file is part of the 2amigos/yii2-multiselect-widget project.
 * (c) 2amigOS! <http://2amigos.us/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace dosamigos\multiselect;

use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * MultiSelectListBox renders a [Louis Cuny Multiselect listbox widget](http://loudev.com/)
 *
 * @see http://loudev.com/
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\multiselect
 */
class MultiSelectListBox extends MultiSelect
{
    public $ajax;

    public $seleactAllEnabled = true;
    public $seleactAllText = 'Select All';
    public $deseleactAllText = 'Deselect All';

    public $searchEnabled = true;


    public function run()
    {
        $id = $this->options['id'];

        parent::run();

        if ($this->seleactAllEnabled) {
            echo '<a href="#" id="'.$id.'-select-all" class="ms-select-all">'.$this->seleactAllText.'</a>';
            echo '<a href="#" id="'.$id.'-deselect-all" class="ms-deselect-all">Deselect All</a>';
        }
    }

    public function searchOptions()
    {
        return [
            'selectableHeader' => "<input type='text' class='search-input form-control mb-1' autocomplete='off' placeholder='Filter...'>",
            'selectionHeader' => "<input type='text' class='search-input form-control mb-1' autocomplete='off' placeholder='Filter...'>",
            'afterInit' => new JsExpression('function(ms) {
                var that = this,
                    selectableSearch = that.$selectableUl.prev(),
                    selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = \'#\'+that.$container.attr(\'id\')+\' .ms-elem-selectable:not(.ms-selected)\',
                    selectionSearchString = \'#\'+that.$container.attr(\'id\')+\' .ms-elem-selection.ms-selected\';
                console.log(this);
                that.qs1 = selectableSearch.quicksearch(selectableSearchString)
                .on(\'keydown\', function(e){
                  if (e.which === 40){
                    that.$selectableUl.focus();
                    return false;
                  }
                });

                that.qs2 = selectionSearch.quicksearch(selectionSearchString)
                .on(\'keydown\', function(e){
                  if (e.which == 40){
                    that.$selectionUl.focus();
                    return false;
                  }
                });
            }'),
            'afterSelect' => new JsExpression("function() {
                this.qs1.cache();
                this.qs2.cache();
            }"),
            'afterDeselect' => new JsExpression("function() {
                this.qs1.cache();
                this.qs2.cache();
            }"),
        ];
    }

    protected function registerPlugin()
    {
        $view = $this->getView();

        MultiSelectListBoxAsset::register($view);

        $id = $this->options['id'];

        if ($this->searchEnabled) {
            $this->clientOptions = array_merge($this->searchOptions(), $this->clientOptions);
        }

        $options = $this->clientOptions !== false && !empty($this->clientOptions)
            ? Json::encode($this->clientOptions)
            : '';

        $js = "jQuery('#$id').multiSelect($options);";
        $view->registerJs($js);

        if ($this->ajax) {
            $view->registerJs('
                $.ajax({
                    url: "'.$this->ajax['url'].'",
                    type: "GET",
                    success: function (qx) {
                        $.each(qx, function (i, item) {
                            if (item.id && item.text) {
                                $("#'.$id.'").multiSelect("addOption", { value: item.id, text: item.text });
                            }
                        });
                        $("#'.$id.'").multiSelect("refresh");
                    },
                    error: function () {
                    }
                });
            ');
        }

        if ($this->seleactAllEnabled) {
            $view->registerJs('
                $("#'.$id.'-select-all").click(function(){
                  $("#'.$id.'").multiSelect("select_all");
                  return false;
                });
                $("#'.$id.'-deselect-all").click(function(){
                  $("#'.$id.'").multiSelect("deselect_all");
                  return false;
                });
            ');
        }
    }
}
