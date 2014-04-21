<?php

/**
 * Podio Advanced Form - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/podio-advanced-form
 * @license     https://github.com/elvenite/podio-advanced-form
 * @version     1.0.0
 * @package     PodioAdvancedForm
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

/**
 * Element for Category Field
 * @package PodioAdvancedForm
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class PodioAdvancedFormCategoryElement extends PodioAdvancedFormElement{
	
    /**
     * Constructor
     * @param PodioAppField $app_field
     * @param PodioAdvancedForm $form
     * @param PodioItemField $item_field
     */
    public function __construct($app_field, $form, $item_field = null) {
        parent::__construct($app_field, $form, $item_field);

        // set multiple
        $this->set_attribute('multiple', $app_field->config['settings']['multiple']);

        $this->set_attribute('options', $app_field->config['settings']['options']);

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
            $this->set_attribute('value', $item_field->api_friendly_values());
        }
    }

    public function render($element = null, $default_field_decorator = 'field'){
        // if the method is invoked from PodioAdvancedFormQuestionElement
        // just pass it on to the parent.
        if ($element){
                return parent::render($element);
        }

        $elements = array();
        $element = '';

        $required = $this->get_attribute('required');

        foreach($this->get_attribute('options') AS $key => $option){
        $class = array();
        $class[] = $this->get_attribute('type');
        $class[] = $option['color'] ? 'color-' . $option['color'] : 'color-DCEBD8';
        // check the first option ($key === 0) if field is required and radio
        $checked = (($required && 
            !$this->get_attribute('multiple') &&
            $key === 0 &&
            (!$this->get_value())) ||
            in_array($option['id'], $this->get_value())) ? 'checked' : '';

            $element = sprintf(
                        '<label class="%7$s inline">
                            <input type="%1$s" value="%2$d" name="%3$s" %4$s %5$s> %6$s
                        </label>', 
                        $this->get_attribute('type'),
                        $option['id'],
                        $this->get_attribute('name'),
                        ($required && !$this->get_attribute('multiple'))
                            ? 'required' : '',
                        $checked,
                        $option['text'],
                        implode(' ',$class)
                      );

            $elements[] = $element;
        }

        return parent::render(implode('', $elements));
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
