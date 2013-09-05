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
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
		
		if ($item_field){
			$this->set_attribute('value', array(
				'hours' => $item_field->hours(),
				'minutes' => $item_field->minutes(),
				'seconds' => $item_field->seconds(),
			));
		}
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
		
		$values = $this->get_attribute('value');
		
		unset($attributes['placeholder']);
		unset($attributes['name']);
		unset($attributes['value']);
		
		
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
			if ( $values ){
				$element .= ' value="' . (string) $values[$value_type] . '"';
			}

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
			$description_decorator = sprintf($this->get_decorator('field_description'),
												$description
											);
		}
		
		$decorator = sprintf($this->get_decorator('field'), 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						implode('', $elements),
						$description_decorator,
						($this->get_attribute('required')) ? $this->get_decorator('field_required') : ''
					);
		
		return $decorator;
	}
}

?>
