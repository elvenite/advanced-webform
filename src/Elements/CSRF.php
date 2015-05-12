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
 * Number Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class CSRF extends Element{
	
    public function __construct($app_field, $form) {
        $this->set_app_field($app_field);
        $this->form = $form;

        $this->set_name($app_field->external_id);
        $this->set_attribute('type', 'hidden');

        /**
         * TODO
         * check status is active
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */
    }
    
    /**
     * Override Element save method
     * There is no item_field to insert the value into
     * @param type $value
     * @return type
     */
    public function save(){
        return;
    }
    
    public function validate($value){
        $app = $this->form->get_app();
        
        $csrf = new \AdvancedWebform\CSRF();
        $csrf->is_valid($value, $app->link);
    }
    
    protected function render_element(){
        $attributes = $this->get_attributes();

        $element = '<input';

        $element .= $this->attributes_concat($attributes);

        $element .= '>';

        return $element;
    }

    public function render($element = null, $default_field_decorator = 'field'){
        $csrf = new \AdvancedWebform\CSRF();
        $app = $this->form->get_app();
        
        $token = $csrf->generate($app->link);
        
        $this->set_attribute('value', $token);
        
        return $this->render_element();
    }
}
