<?php

class PodioAdvancedFormAppElement extends PodioAdvancedFormElement{
	protected $sub_form;
	
	public function __construct($app_field, $form, $item_field = null, $attributes = null) {
		parent::__construct($app_field, $form, $item_field, $attributes);
		
		// load a new PodioAdvancedForm in the sub_form attribute
		$this->set_attribute('reference_apps', $app_field->config['settings']['referenceable_types']);
		
		// for now, just get the first app
		$sub_app_id = $app_field->config['settings']['referenceable_types'][0];
		$this->set_attribute('app_id', $sub_app_id);
		
		$sub_item_id = null;
		if ($item_field){
			foreach($item_field->values AS $item){
				$item = $item['value'];
				if ($item['app']['app_id'] == $sub_app_id){
					$sub_item_id = $item['item_id'];
					break;
					
				}
			}
		}
		
		
		
		$view = $this->get_attribute('view');
		
		if ($view){
			$collection = PodioItem::filter_by_view($sub_app_id, $view);
			// TODO read total, filtered do decide if autocomplete should be used.
			if ($collection){
				$this->set_attribute('items', $collection['items']);
			} else {
				// if no items, then hide the field
				$this->set_attribute('hidden', true);
			}
		}
		
		$sub_form_attributes = array(
			'app_id' => $sub_app_id,
			'is_sub_form' => true,
			'item_id' => $sub_item_id,
			'parent' => $this,
		);

		$sub_form = new PodioAdvancedForm($sub_form_attributes);

		$this->set_sub_form($sub_form);
		
		/**
		 * TODO
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
		
	
	}
	
	public function get_sub_form(){
		return $this->sub_form;
	}
	
	public function set_sub_form($settings){
		if ($settings instanceof PodioAdvancedForm){
			$this->sub_form = $settings;
		} else {
			$this->sub_form = new PodioAdvancedForm($settings);
		}
		
		// just for extra safety
		$this->sub_form->set_is_sub_form(true);
		
		$this->sub_form->set_field_name_prefix($this->get_attribute('name'));
	}
	/**
	 * 
	 * @param array|int $values
	 * @return void
	 */
	public function set_value($values) {
		// if $values if an int, it means it's an item_id
		if (is_numeric($values)){
			$sub_form_item_id = (int) $values;
			$this->sub_form->get_item()->item_id = $sub_form_item_id;
		} elseif (is_array($values)) {
			$this->sub_form->set_values($values);
			$sub_form_item_id = $this->sub_form->save();
		} else {
			// no value, the select element is empty
			return;
		}
		
		$this->item_field->set_value($sub_form_item_id);
	}
	
	public function render_locked(){
		return $this->sub_form->render();
	}
	
	public function render_select(){
		$attributes = $this->get_attributes();
		$element = '<select';
		$element .= $this->attributes_concat($attributes);
		$element .= '>';
		
		if(!$this->get_attribute('required')){
			$app_field = $this->get_app_field();
			$label = $app_field->config['label'];
			$element .= '<option value="">' . $label . '</option>';
		}
		
		$items = $this->get_attribute('items');
		foreach($items AS $item){
			$element .= '<option value="' . $item->item_id . '">' . $item->title . '</option>';
		}
		
		$element .= '</select>';
		
		return $element;
	}
	
	public function render($element = null, $default_field_decorator = 'field'){
		if ($this->get_attribute('items')){
			return parent::render($this->render_select());
		}
		
		return parent::render($this->sub_form->render(), 'parent_field');
	}
}

?>
