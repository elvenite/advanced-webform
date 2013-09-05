<?php

class PodioAdvancedFormDateElement extends PodioAdvancedFormElement{
	
	protected $decorators = array(
		//'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls controls-row">%3$s%4$s</div></div>'
	);
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 * support different date and time formats?
		 */
	
	}
	
	public function set_value($values) {
		$value = array();
		if (empty($values['start_date'])){
			return false;
		}
		$value['start'] = $values['start_date'];
		
		if (!empty($values['start_date']) && !empty($values['start_time'])){
			$value['start'] .= ' ' . $values['start_time'] . ':00';
		} else {
			$value['start'] .= ' 00:00:00';
		}
		
		if (!empty($values['start_date']) && !empty($values['end_date'])){
			$value['end'] = $values['end_date'];
			
			if (!empty($values['end_time'])){
				$value['end'] .= ' ' . $values['end_time'] . ':00';
			} else {
				$value['end'] .= ' 00:00:00';
			}
		} else {
			$value['end'] = $value['start'];
		}
		
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
		
		$elements = array();
		
		
		
		// startdate
			$element = '<input';
			if ($required){
				$element .= ' required';
			}
			$attributes['placeholder'] = 'YYYY-MM-DD';
			$attributes['name'] = $this->get_attribute('name') . '[start_date]';
			$attributes['class'] = 'span3';

			$attributes_string = '';
			foreach($attributes AS $key => $attribute){
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}

			$element .= $attributes_string;

			$element .= '> ';
			$elements[] = $element;
			
		// starttime
			$element = '<input';
			$attributes['placeholder'] = 'HH:MM';
			$attributes['name'] = $this->get_attribute('name') . '[start_time]';
			$attributes['type'] = 'text';
			$attributes['class'] = 'span1';

			$attributes_string = '';
			foreach($attributes AS $key => $attribute){
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}

			$element .= $attributes_string;

			$element .= '> ';
			$elements[] = $element;
		// enddate
			$element = '<input';
			$attributes['placeholder'] = 'YYYY-MM-DD';
			$attributes['name'] = $this->get_attribute('name') . '[end_date]';
			$attributes['type'] = $this->get_attribute('type');
			$attributes['class'] = 'span3';

			$attributes_string = '';
			foreach($attributes AS $key => $attribute){
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}

			$element .= $attributes_string;

			$element .= '> ';
			$elements[] = $element;
		// endtime
			$element = '<input';
			$attributes['placeholder'] = 'HH:MM';
			$attributes['name'] = $this->get_attribute('name') . '[end_time]';
			$attributes['type'] = 'text';
			$attributes['class'] = 'span1';

			$attributes_string = '';
			foreach($attributes AS $key => $attribute){
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}

			$element .= $attributes_string;

			$element .= '> ';
			$elements[] = $element;
		
		$description_decorator = '';
		if ($description){
			$description_decorator = sprintf($this->get_decorator('field_description'),
												$description
											);
		}
		
		$decorator = sprintf($this->get_decorator('field'), 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						implode(' ', $elements),
						$description_decorator,
						($this->get_attribute('required')) ? $this->get_decorator('field_required') : ''
					);
		
		return $decorator;
	}
}

?>
