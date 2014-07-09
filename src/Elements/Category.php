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
 * Element for Category Field
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class Category extends Element{
    
    /**
     * (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
     * Element inline display
     * Element list display
     * Element dropdown display 
     * @var array 
     */
    protected $decorators = array(
        'element-inline' => '<label class="label-control %5$s" style="%6$s">
                                 <input type="%4$s" value="%1$d" name="%3$s" %7$s> %2$s
                            </label>',
        'element-list' => '<label class="label-control %5$s" style="%6$s">
                                 <input type="%4$s" value="%1$d" name="%3$s" %7$s> %2$s
                            </label><br>',
        'element-dropdown' => '<option value="%1$d" %7$s>%2$s</option>',
        'wrapper-inline' => '<br>%1$s',
        'wrapper-list' => '<br>%1$s',
        'wrapper-dropdown' => '<br><select class="form-control" name="%3$s" %7$s>%1$s</select>',
    );
	
    /**
     * Constructor
     * @param PodioAppField $app_field
     * @param \AdvancedWebform $form
     * @param PodioItemField $item_field
     */
    public function __construct($app_field, $form, $item_field = null) {
        parent::__construct($app_field, $form, $item_field);

        // set multiple
        $this->set_attribute('multiple', $app_field->config['settings']['multiple']);

        $this->set_attribute('options', $app_field->config['settings']['options']);
        
        $this->set_attribute('display', $app_field->config['settings']['display']);

        // set type to checkbox or radio depending on if category allows multiple
        // values, also change the name if multiple = true, add [] to indicate
        // array values

        $name = $this->get_attribute('name');

        if ($this->get_attribute('multiple')){
            $type = 'checkbox';
            $name .= '[]';
        } else {
            $type = 'radio';
        }

        $this->set_attribute('name', $name);
        $this->set_attribute('type', $type);
        /**
         * TODO
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */

        if ($item_field){
            $this->set_value($item_field->api_friendly_values());
        }
    }

    public function render($element = null, $default_field_decorator = 'field'){
        // TODO remove question field specific code, it doesn't exist anymore
        // if the method is invoked from QuestionElement
        // just pass it on to the parent.
        if ($element){
                return parent::render($element);
        }

        $elements = array();
        $element = '';
        
        

        $required = $this->get_attribute('required');

        foreach($this->get_attribute('options') AS $key => $option){
        // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
            
            // 1. value
            $value = $option['id'];
            
            // 2. label
            $label = $option['text'];
            
            // 3. name
            $name = $this->get_attribute('name');
            
            // 4. type
            $type = $this->get_attribute('type');
            
            // 5. class
            $class = array();
            switch($this->get_attribute('display')){
                case 'inline':
                case 'list':
                    $class[] = $this->get_attribute('type') . '-inline';
                    break;
            }
            
            // 6. style
            $style = array();
            $color = $option['color'] ? $option['color'] : 'DCEBD8';
            $style[] = 'background: #' . $color . ';';

            // 7. other attributes
            $other = array();
            $other[] = ($required) ? 'required' : '';
            
            // check the first option ($key === 0) if field is required and radio
            $checked = (($required && 
            !$this->get_attribute('multiple') &&
            $key === 0 &&
            (!$this->get_value())) ||
            in_array($option['id'], (array) $this->get_value())) ? 'checked' : '';
            
            $other[] = $checked;
            
            $selected = ($this->get_attribute('display') == "dropdown" && ($required && 
                        $key === 0 &&
                        (!$this->get_value())) ||
                        in_array($option['id'], (array) $this->get_value())) ? 'selected' : '';
            
            $other[] = $selected;

            $element = sprintf(
                        $this->get_decorator('element-' . $this->get_attribute('display')), 
                        $value,
                        $label,
                        $name,
                        $type,
                        implode(' ', $class),
                        implode(' ', $style),
                        implode(' ', $other)
                      );
    
            $elements[] = $element;
        }
        
        // if display = dropdown and not required, ad an empty option
        if ($this->get_attribute('display') == "dropdown" && !$required){
            $element = sprintf(
                        $this->get_decorator('element-' . $this->get_attribute('display')), 
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                      );
            
            array_unshift($elements, $element);
        }
        
        // 1. In the wrapper, value is the elements array
        $value = implode('', $elements);
        
        $row = sprintf(
                        $this->get_decorator('wrapper-' . $this->get_attribute('display')), 
                        $value,
                        $label,
                        $name,
                        $type,
                        implode(' ', $class),
                        implode(' ', $style),
                        implode(' ', $other)
                      );

        return parent::render($row);
    }

    /**
     * Get value
     * @return mixed
     */
    public function get_value(){
        $value = parent::get_attribute('value');
        if (!$value){
                return array();
        }

        return $value;
    }
}
