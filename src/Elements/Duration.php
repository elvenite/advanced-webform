<?php

/**
 * Advanced Webform for Podio - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/advanced-webform
 * @license     https://github.com/elvenite/advanced-webform
 * @version     1.0.0
 * @package     AdvancedWebform
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace AdvancedWebform\Elements;

/**
 * Duration Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */

class Duration extends Element{
	
    public function __construct($app_field, $form, $item_field = null) {
        parent::__construct($app_field, $form, $item_field);
        
        $this->set_attribute('fields', $app_field->config['settings']['fields']);

        $this->set_attribute('type', 'number');

        $this->set_attribute('value_types', array(
            'days' => 'Days',
            'hours' => 'Hours',
            'minutes' => 'Minutes',
            'seconds' => 'Seconds',
        ));

        /**
         * TODO
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */
        
        if ($item_field){
            $value = (int) $item_field->values;
            $this->set_value($value);
        }
    }

    public function set_value($values){
        $value = 0;
        
        if (is_array($values)){
            // days
            if (isset($values['days'])){
                $value += ($values['days']*86400);
            }
            // hours
            if (isset($values['hours'])){
                $value += ($values['hours']*3600);
            }
            // minutes
            if (isset($values['minutes'])){
                $value += ($values['minutes']*60);
            }
            // seconds
            if (isset($values['seconds'])){
                $value += $values['seconds'];
            }
        } else {
            $value = $values;
        }

        parent::set_value($value);
    }
    
    /**
     * Returns the value, if split = true, split value into active fields
     * @param bool $split
     */
    public function get_value($split = false){
        if (isset($this->item_field->values[0])){
            $value = $this->item_field->values[0]['value'];
        } else {
            $value = $this->get_attribute('value');
        }
        
        if (!$split){
            return $value;
        }
        
        foreach($this->get_attribute('fields') AS $f){
            switch($f){
                case 'days':
                    $values['days'] = (int) floor($value/86400);
                    $value -= $values['days']*86400;
                    break;
                case 'hours':
                    $values['hours'] = (int) floor($value/3600);
                    $value -= $values['hours']*3600;
                    break;
                case 'minutes':
                    $values['minutes'] = (int) floor($value/60);
                    $value -= $values['minutes']*60;
                    break;
                case 'seconds':
                    $values['seconds'] = (int) floor($value);
                    break;
            }
        }
        
        return $values;
    }

    public function render($element = null, $default_field_decorator = 'field'){
        // output is:
        // decorator
        // element

        $attributes = $this->get_attributes();
        // some attributes should not go into the element, like type,
        // description
        // required is a special case as well
        // handle them first

        $description = $this->get_attribute('description');
        unset($attributes['description']);
        $required = $this->get_attribute('required');
        unset($attributes['required']);

        $values = $this->get_value('value', true);

        unset($attributes['label']);
        unset($attributes['name']);
        unset($attributes['value']);


        $attributes_string = '';

        $elements = array();
        
        $elements[] = '<div class="row">';


        foreach($attributes['fields'] AS $value_type){
            $value_types = $this->get_attribute('value_types');
            $help_text = $value_types[$value_type];
            
            foreach($attributes AS $key => $attribute){
                    if (is_array($attribute)){
                        $attribute = json_encode($attribute);
                    }
                    $attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
            }
            $element = '<div class="col-xs-3 col-md-2 col-lg-1"><input';

            // TODO how to solve required?
//			if ($required){
//				$element .= ' required';
//			}

            $element .= $attributes_string;

            $element .= ' name="' . $this->get_attribute('name') . '[' . $value_type . ']"';
            if ( $values ){
                    $element .= ' value="' . (string) $values[$value_type] . '"';
            }

            $element .= '>';

            $help_text_decorator = sprintf('<span class="help-inline">%1$s</span>&nbsp;&nbsp;&nbsp;&nbsp;',
                                                                                    $help_text
                                                                            );

            $element .= $help_text_decorator;
            
            $element .= '</div>';

            $elements[] = $element;
        }
        
        $elements[] = '</div>';

        $description_decorator = '';
        if ($description){
                $description_decorator = sprintf($this->get_decorator('field_description'),
                                                                                        $description
                                                                                );
        }

        $decorator = sprintf($this->get_decorator('field'), 
                                        $this->get_attribute('name'),
                                        $this->get_attribute('label'),
                                        implode('', $elements),
                                        $description_decorator,
                                        ($this->get_attribute('required')) ? $this->get_decorator('field_required') : '',
                                        '' // empty css class
                                );

        return parent::render($decorator);
    }
}
