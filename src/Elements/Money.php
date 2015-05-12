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
 * Money Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */

class Money extends Element{
	
    /**
    * (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
    * Element inline display
    * Element list display
    * Element dropdown display 
    * @var array 
    */
    protected $decorators = array(
        'wrapper' => '<div class="row">%1$s</div>',
        'wrapper-dropdown' => '<div class="col-lg-1 col-md-2 col-sm-2 col-xs-3"><select class="form-control" name="%3$s" %7$s>%1$s</select></div>',
        'element-dropdown' => '<option value="%1$s" %7$s>%2$s</option>',
        'element' => '<div class="col-lg-11 col-md-10 col-sm-10 col-xs-9"><input class="%5$s" type="%4$s" value="%1$d" name="%3$s" %7$s></div>',
    );

    public function __construct($app_field, $form, $item_field = null) {
            parent::__construct($app_field, $form, $item_field);

            $this->set_attribute('type', 'number');
            $this->set_attribute('currencies', $this->app_field->config['settings']['allowed_currencies']);

            if ($item_field){
                    $this->set_value(array(
                            'currency' => $item_field->currency,
                            'amount' => $item_field->amount,
                    ));
            };

            /**
             * TODO
             * check status is active
             * check visibility equals true (config['visible']
             * add delta field (delta is the sort order)
             */
    }

    public function save() {
        $values = $this->get_attribute('value');
        if (empty($values)){
            $values = null;
        } else {
            $this->set_attribute('value', $values);
            $this->item_field->amount = $values['amount'];
            $this->item_field->currency = $values['currency'];
        }
    }


    public function render($element = null, $default_field_decorator = 'field'){
        $attributes = $this->get_attributes();

        $options = array();
        $option = '';
        
        // currency
        foreach($attributes['currencies'] AS $currency){
            // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
            
            // 1. value
            $value = $currency;
            
            // 2. label
            $label = $currency;
            
            // 3. name
            $name = '';
            
            // 4. type
            $type = '';    
            
            // 5. class
            $class = array();
            
            // 6. style
            $style = array();

            // 7. other attributes
            $other = array();
            
            $selected = (isset($attributes['value']) && $attributes['value']['currency'] == $currency) ? 'selected' : '';
            
            $other[] = $selected;
        
            $option = sprintf(
                            $this->get_decorator('element-dropdown'), 
                            $value,
                            $label,
                            $name,
                            $type,
                            implode(' ', $class),
                            implode(' ', $style),
                            implode(' ', $other)
                          );

            $options[] = $option;
        }
        
        unset($attributes['currencies']);
        
        // wrap options in select tag
        $value = implode('', $options);
        $name = $attributes['name'] . '[currency]';
        $element = sprintf($this->get_decorator('wrapper-dropdown'), 
                $value,
                '',
                $name,
                '',
                '',
                '',
                '');
        
        $elements[] = $element;
        
        // amount
        
        // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
            
        // 1. value
        // make sure value is an integer
        if (isset($attributes['value'])){
            $value = number_format($attributes['value']['amount'], 2, '.', '');
        }

        // 2. label
        $label = $attributes['label'];
        unset($attributes['label']);

        // 3. name
       $name = $attributes['name'] . '[amount]';
       unset($attributes['name']);
            
        // 4. type
        $type = $attributes['type'];
        unset($attributes['type']);
        
        // 5. class
        $class = (array) $attributes['class'];
        unset($attributes['class']);

        // 7. other attributes
        $other = $this->attributes_concat($attributes);
        
        $element = sprintf(
                $this->get_decorator('element'), 
                $value,
                $label,
                $name,
                $type,
                implode(' ', $class),
                '',
                $other
              );
        
        $elements[] = $element;
        
        // 1. In the wrapper, value is the elements array
        $value = implode('', $elements);
        
        $row = sprintf(
                $this->get_decorator('wrapper'), 
                $value,
                '',
                '',
                '',
                '',
                '',
                ''
              );

        

        return parent::render($row);
    }
}
