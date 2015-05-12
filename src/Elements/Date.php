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
 * Element for Date Field
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class Date extends Element{
    
    const END_TIME_BEFORE_START_TIME = 'endTimeBeforeStartTime';

    protected $messages = array(
        self::END_TIME_BEFORE_START_TIME => 'The end time must be later than the start time.',
    );

    protected $decorators = array(
        //'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls controls-row">%3$s%4$s</div></div>'
    );

    public function __construct($app_field, $form, $item_field = null) {
        parent::__construct($app_field, $form, $item_field);
        
        $this->set_attribute('settings', $app_field->config['settings']);
        // $settings['end']
        // $settings['time']
        // can be either enabled, disabled or required

        /**
         * TODO
         * check status is active
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         * support different date and time formats?
         */

        if ($item_field){
            $this->set_attribute('value', $item_field->values);
        }

    }
        
    /**
     * Validates the date interval by transforming date and time to seconds
     * since unix epoch
     * @param array $values
     */
    protected function validate($values){
        $start_string = $values['start_date'];
        $start_format = 'Y-m-d';
        if (isset($values['start_time']) && $values['start_time'] != ''){
            $start_string .= ' ' . $values['start_time'];
            $start_format .= ' H:i';
        }
        
        $start = \DateTime::createFromFormat($start_format, $start_string);
        $end = false;
        
        if (isset($values['end_date']) && $values['end_date'] != ''){
            $end_string = (isset($values['end_date']) && $values['end_date'] != '') ? $values['end_date'] : $values['start_date'];
            $end_format = 'Y-m-d';
            if (isset($values['end_time']) && $values['end_time'] != ''){
                $end_string .= ' ' . $values['end_time'];
                $end_format .= ' H:i';
            } elseif (isset($values['start_time'])){
                $end_string .= ' ' . $values['start_time'];
                $end_format .= ' H:i';
            }


            $end = \DateTime::createFromFormat($end_format, $end_string);
        }

        if ($start && $end && ($end >= $start)){
            return true;
        } elseif ($start) {
            return true;
        }

        // insert values into value attribute (but not the item's value property
        $this->set_attribute('value', $values);
        $this->throw_error($this->messages[self::END_TIME_BEFORE_START_TIME]);
    }
	
    public function set_value($values) {
        $value = array();
        if (empty($values['start_date']) || !$this->validate($values)){
            // does this really run?
            return false;
        }

        $value['start'] = $values['start_date'];

        if (!empty($values['start_date']) && !empty($values['start_time'])){
            $value['start'] .= ' ' . $values['start_time'] . ':00';
            //$values['start_time'] = $values['start_time'] . ':00';
        }

        if (!empty($values['start_date']) && !empty($values['end_date'])){
            $value['end'] = $values['end_date'];

            if (!empty($values['end_time'])){
                    $value['end'] .= ' ' . $values['end_time'] . ':00';
                    //$values['end_time'] = $values['end_time'] . ':00';
            }
        } else {
            $value['end'] = $value['start'];
        }

        parent::set_value($value);
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
        $settings = $attributes['settings'];
        unset($attributes['required']);
        unset($attributes['placeholder']);
        unset($attributes['name']);
        unset($attributes['settings']);

        $elements = array();

        $values = $this->get_attribute('value');
        unset($attributes['value']);

        // startdate
        $element = '<div class="col-sm-5 col-md-4 col-lg-3">';
        if ($settings['time'] == "enabled" || $settings['time'] == "required"){
            $element .= '<div class="input-group">';
        }
        $element .= '<input';
        if ($required){
            $element .= ' required';
        }

        $attributes['placeholder'] = 'YYYY-MM-DD';
        $attributes['name'] = $this->get_attribute('name') . '[start_date]';
        $attributes['required'] = $required;
        $attributes['value'] = (isset($values['start'])) ? $values['start']->format('Y-m-d') : null;

        $attributes_string = $this->attributes_concat($attributes);

        $element .= $attributes_string;
        
        $element .= '>';

        $elements[] = $element;

        if ($settings['time'] == "enabled" || $settings['time'] == "required"){
            // starttime
            $element = '<span class="input-group-input"><input';
            $attributes['placeholder'] = 'HH:MM';
            $attributes['name'] = $this->get_attribute('name') . '[start_time]';
            $attributes['type'] = 'text';
            $attributes['value'] = (isset($values['start'])) ? $values['start']->format('H:i') : null;
            // TODO how do we solved "required if the date is filled in"?
            $attributes['required'] = ($required && $settings['time'] == "required") ? true : null;
            $attributes['style'] = 'width:75px;';

            $attributes_string = $this->attributes_concat($attributes);

            $element .= $attributes_string;

            $element .= '></span></div> '; // end .input-group-input & .input-group
            $elements[] = $element;
            
        }
        
        $elements[] = '</div> ';
        
        
        if ($settings['end'] == "enabled" || $settings['end'] == "required"){
            $element = '<div class="col-sm-1">
                    <p class="form-control-static date-divider" style="text-align:center;padding-top:7px;">–</p>
                </div>';
            // enddate
            $element .= '<div class="col-sm-5 col-md-4 col-lg-3">';
            if ($settings['time'] == "enabled" || $settings['time'] == "required"){
                $element .= '<div class="input-group">';
            }
            $element .= '<input';
            $attributes['placeholder'] = 'YYYY-MM-DD';
            $attributes['name'] = $this->get_attribute('name') . '[end_date]';
            $attributes['type'] = $this->get_attribute('type');
            $attributes['value'] = (isset($values['end'])) ? $values['end']->format('Y-m-d') : null;
            $attributes['min'] = (isset($values['start'])) ? $values['start']->format('Y-m-d') : null;
            // TODO how do we solved "required if start date is filled in"?
            $attributes['required'] = ($required && $settings['end'] == "required") ? true : null;
            unset($attributes['style']);

            $attributes_string = $this->attributes_concat($attributes);

            $element .= $attributes_string;

            $element .= '>';
            $elements[] = $element;
            
            if ( 
            ($settings['time'] == "enabled" || 
             $settings['time'] == "required")){
                // endtime
                $element = '<span class="input-group-input"><input';
                $attributes['placeholder'] = 'HH:MM';
                $attributes['name'] = $this->get_attribute('name') . '[end_time]';
                $attributes['type'] = 'text';
                $attributes['value'] = (isset($values['end'])) ? $values['end']->format('H:i') : null;
                // TODO how do we solved "required if the date is filled in"?
                //$attributes['required'] = ($settings['time'] == "required") ? true : null;
                $attributes['style'] = 'width:75px;';

                $attributes_string = $this->attributes_concat($attributes);

                $element .= $attributes_string;

                $element .= '></span></div> '; // end .input-group-input & .input-group
                $elements[] = $element;
            }
            
            $elements[] = '</div>'; // end <div class="col-sm-5 col-md-4 col-lg-3">
        }

        $description_decorator = '';
        if ($description){
            $description_decorator = 
                sprintf($this->get_decorator('field_description'),
                    $description
                );
        }
        
        array_unshift($elements, '<div class="row">');
        array_push($elements, '</div>');

        $element = implode(' ', $elements);
        
        return parent::render($element);
    }
}
