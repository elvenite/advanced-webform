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
 * Location Field Element
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */

class Location extends Element{

  protected $structured_subfields = array(
    'street_address' => 'Address',
    'postal_code' => 'Postal code',
    'city' => 'City',
    'state' => 'State',
    'country' => 'Country',
  );

  /**
  * (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)
  * Element inline display
  * Element list display
  * Element dropdown display
  * 
  * @var array 
  */
  protected $decorators = array(
    'wrapper' => '<div class="form-horizontal">%1$s</div>',
    'element' => '<div class="form-group"><label class="control-label col-xs-2" for="%3$s">%2$s</label><div class="col-xs-10 field-multiple-location"><input class="%5$s" type="%4$s" value="%1$s" name="%3$s" %7$s></div></div>',
  );

  /**
   */
  public function __construct($app_field, $form, $item_field = null) {
    parent::__construct($app_field, $form, $item_field);

    $this->set_attribute('type', 'text');

    $this->set_attribute('structured', (bool) $this->app_field->config['settings']['structured']);

    // if not structured, the parent constructor loads the humanized value
    if ($item_field && $this->get_attribute('structured')){
      $this->set_value($item_field->values);
    };

    /**
     * TODO
     * check status is active
     * check visibility equals true (config['visible']
     * add delta field (delta is the sort order)
     */

  }

  protected function render_structured(){
    $elements = array();
    $output = '';
    // (1=value, 2=label, 3=name, 4=type, 5=class, 6=style, 7=other attributes)

    $attributes = $this->get_attributes();

    unset($attributes['structured']);
    unset($attributes['value']);
    unset($attributes['label']);
    unset($attributes['name']);
    unset($attributes['id']);

    // 1. Value
    $values = $this->get_value();

    // 4. type
    $type = $attributes['type'];
    unset($attributes['type']);
    
    // 5. class
    $class = (array) $attributes['class'];
    unset($attributes['class']);

    // 7. other attributes
    $other = $this->attributes_concat($attributes);

    foreach($this->structured_subfields AS $subfield_key => $subfield_label){

      $name = $this->get_attribute('name') . '[' . $subfield_key . ']';

      $value = (isset($values[$subfield_key])) ? $values[$subfield_key] : '';

      $element = sprintf(
        $this->get_decorator('element'), 
        $value,
        $subfield_label,
        $name,
        $type,
        implode(' ', $class),
        '',
        $other
      );

      $elements[] = $element;
    }

    $output = sprintf(
      $this->get_decorator('wrapper'), 
      implode('', $elements)
    );

    return $output;
  }

  /**
   * Renders the input field
   * This method HAS to return parent::render() with an optional element
   * string, this way parent::render() can decide whether to display the
   * element or not. If it happened to have hidden=true
   * @return string
   */
  public function render($element = null, $default_field_decorator = 'field'){
    // if only one field
    if (!$this->get_attribute('structured')){
        return parent::render();
    }

    // if location consists of several fields for street-adress, postal-code etc.
    $output = $this->render_structured();

    return parent::render($output);
  }
}
