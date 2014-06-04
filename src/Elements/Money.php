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
	
//	protected $decorators = array(
//		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls controls-row">%3$s%4$s</div></div>'
//	);
	
	protected $decorators = array(
		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls-row controls">%3$s%4$s</div>',
		'sub_field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls-row controls">%3$s%4$s</div>',
 	);

	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'text');
		$this->set_attribute('currencies', $this->app_field->config['settings']['allowed_currencies']);
		
		if ($item_field){
			$this->set_attribute('value', array(
				'currency' => $item_field->currency(),
				'amount' => $item_field->amount(),
			));
		};
			
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	}
	
	public function set_value($values) {
		$this->item_field->set_amount($values['amount']);
		$this->item_field->set_currency($values['currency']);
	}


	public function render($element = null, $default_field_decorator = 'field'){
		$attributes = $this->get_attributes();
		
		// change name attributes to include amount
		$attributes['name'] .= '[amount]';
		
		$elements = array();
		
		$element = '<select name="' . $this->get_attribute('name') . '[currency]" class="span1">';
			foreach($this->get_attribute('currencies') AS $currency){
				
				$selected = (isset($attributes['value']) && $attributes['value']['currency'] == $currency) ? 'selected' : '';
				$element .= '<option value="' . $currency . '" ' . $selected . '>' . $currency . '</option>';
			}
			
		$element .= '</select>';
		
		$elements[] = $element;
		
		$element = '<input';
		
		// make sure value is an integer
		if (isset($attributes['value'])){
			$attributes['value'] = number_format($attributes['value']['amount'], 2, '.', '');
		}
		
		$attributes['class'] = 'span7';
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		$elements[] = $element;
		
		return parent::render(implode('', $elements));
	}
}

?>
