<?php

class PodioAdvancedFormDurationElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'text');
		$this->set_attribute('class', 'span1');
		
		$this->set_attribute('value_types', array(
			'hours' => 'Hours',
			'minutes' => 'Minutes',
			'seconds' => 'Seconds',
		));
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	}
	
	public function set_value($values){
		$value = 0;
		
		// hours
		$value += ($values['hours']*3600);
		// minutes
		$value += ($values['minutes']*60);
		// seconds
		$value += $values['seconds'];
		
		parent::set_value($value);
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
		
		unset($attributes['placeholder']);
		unset($attributes['name']);
		
		$attributes_string = '';
		
		$elements = array();
		
		
		foreach($attributes['value_types'] AS $value_type => $help_text){
			foreach($attributes AS $key => $attribute){
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}
			$element = '<input';
		
			// TODO how to solve required?
//			if ($required){
//				$element .= ' required';
//			}

			$element .= $attributes_string;
			
			$element .= ' name="' . $this->get_attribute('name') . '[' . $value_type . ']"';

			$element .= '>';
			
			$help_text_decorator = '';
			$help_text_decorator = sprintf('<span class="help-inline">%1$s</span>&nbsp;&nbsp;&nbsp;&nbsp;',
												$help_text
											);
			
			$element .= $help_text_decorator;
			
			$elements[] = $element;
		}
		
		$description_decorator = '';
		if ($description){
			$description_decorator = sprintf('<span class="help-block">%1$s</span>',
												$description
											);
		}
		
		$decorator = sprintf('<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls">%3$s%4$s</div></div>', 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						implode('', $elements),
						$description_decorator
					);
		
		return $decorator;
	}
}

?>
