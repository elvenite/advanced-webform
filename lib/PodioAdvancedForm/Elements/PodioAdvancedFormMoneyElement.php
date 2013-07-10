<?php

class PodioAdvancedFormMoneyElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'text');
		$this->set_attribute('currencies', $this->app_field->config['settings']['allowed_currencies']);
		
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


	public function render(){
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
		
		// change name attributes to include amount
		$attributes['name'] .= '[amount]';
		
		$elements = array();
		
		$attributes_string = '';
		foreach($attributes AS $key => $attribute){
			$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
		}
		
		$element = '<select name="' . $this->get_attribute('name') . '[currency]" class="span1">';
			foreach($this->get_attribute('currencies') AS $currency){
				$element .= '<option value="' . $currency . '">' . $currency . '</option>';
			}
		$element .= '</select>';
		
		$elements[] = $element;
		
		$element = '<input';
		
		if ($required){
			$element .= ' required';
		}
		
		$element .= $attributes_string;
		
		$element .= '>';
		
		$elements[] = $element;
		
		$description_decorator = '';
		if ($description){
			$description_decorator = sprintf('<span class="help-block">%1$s</span>',
												$description
											);
		}
		
		$decorator = sprintf('<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls">%3$s%4$s</div></div>', 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						implode(' ', $elements),
						$description_decorator
					);
		
		return $decorator;
	}
}

?>
