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
 * Phone Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.1.0
 */
class Phone extends Element{

  /**
  * (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
  * Element inline display
  * Element list display
  * Element dropdown display
  * 
  * @var array 
  */
  protected $decorators = array(
      'wrapper' => '<div class="form-group"><div class="input-group">%1$s</div></div>',
      'wrapper-dropdown' => '<span class="input-group-addon"><select class="" name="%3$s" %7$s>%1$s</select></span>',
      'element-dropdown' => '<option value="%1$s" %7$s>%2$s</option>',
      'element' => '<input class="%5$s" type="%4$s" value="%1$s" name="%3$s" %7$s><span class="input-group-btn  field-remove hidden"><button type="button" class="btn btn-link">✕</button></span>',
  );

  public function __construct($app_field, $form, $item_field = null) {
      parent::__construct($app_field, $form, $item_field);

      $this->set_attribute('type', 'tel');
      $this->set_attribute('possible_types', $this->app_field->config['settings']['possible_types']);

      if ($item_field){
        $this->set_value($item_field->values);
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
      

      $values = array_filter($values, function($v){
        if (empty($v['value'])){
          return false;
        } else {
          return $v;
        }
      });

      $values = array_values($values);
      $this->set_attribute('value', $values);
      $this->item_field->values = $values;
    }
  }

    /**
     * Renders select box with possible types
     * Makes 
     * @param array $value
     * @return string
     */
  protected function render_dropdown( array $current_value = array(), $index){
    $options = array();
    $option = '';
    $attributes = $this->get_attributes();
    $possible_types = $this->get_attribute('possible_types');
      
    // type
    foreach($possible_types AS $possible_type){
      // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
      
      // 1. value
      $value = $possible_type;
      
      // 2. label
      $label = $possible_type;
      
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
      
      $selected = (isset($current_value['type']) && $current_value['type'] == $possible_type) ? 'selected' : '';
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
      
      // wrap options in select tag
      $value = implode('', $options);
      $name = $attributes['name'] . '[' . $index . '][type]';
      $element = sprintf($this->get_decorator('wrapper-dropdown'), 
        $value,
        '',
        $name,
        '',
        '',
        '',
        '');

      return $element;
  }


  public function render($element = null, $default_field_decorator = 'field'){
    $attributes = $this->get_attributes();
    unset($attributes['possible_types']);

    // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)

    unset($attributes['value']);
    unset($attributes['label']);
    unset($attributes['name']);
    unset($attributes['id']);

    // 4. type
    $type = $attributes['type'];
    unset($attributes['type']);
    
    // 5. class
    $class = (array) $attributes['class'];
    unset($attributes['class']);

    // 7. other attributes
    $other = $this->attributes_concat($attributes);

    // iterate through all existing values or once
    $values = (null !== $this->get_attribute('value')) ? $this->get_attribute('value') : array();

    $length = (count($values)) ? count($values) : 1;

    for($i = 0;$i<$length;$i++){
      $elements = array();
      $element = '';

      // 1. value
      if ($values && isset($values[$i])){
        $value = $values[$i];
      } else {
        $value = array();
      }

      $elements[] = $this->render_dropdown($value, $i);

      // 3. name
      $name = $this->get_attribute('name') . '['. $i .'][value]';

      $element = sprintf(
        $this->get_decorator('element'), 
        isset($value['value']) ? $value['value'] : '',
        '', // label (used in parent::render, not here)
        $name,
        $type,
        implode(' ', $class),
        '',
        $other
      );
    
      $elements[] = $element;

      $rows[] = sprintf(
        $this->get_decorator('wrapper'), 
        implode('', $elements)
      );
    }

    // add "add more" button

    $rows[] = '<div class="form-group"><button type="button" class="btn btn-link field-add-another"><span class="glyphicon glyphicon-plus-sign"></span> Add one more</button></div>';

    return parent::render(implode('', $rows));
  }
}
