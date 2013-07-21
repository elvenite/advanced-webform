<?php

class PodioAdvancedFormMoneyElement extends PodioAdvancedFormElement{
	
	protected $decorators = array(
		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls controls-row">%3$s%4$s</div></div>'
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


	public function render(){
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
