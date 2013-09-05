<?php

class PodioAdvancedFormAppElement extends PodioAdvancedFormElement{
	protected $sub_form;
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		// load a new PodioAdvancedForm in the sub_form attribute
		$this->set_attribute('reference_apps', $app_field->config['settings']['referenceable_types']);
		
		// for now, just get the first app
		$sub_app_id = $app_field->config['settings']['referenceable_types'][0];
		
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
	
	public function set_value($values) {
		$this->sub_form->set_values($values);
		//var_dump($this->sub_form->get_item());

		$sub_form_item_id = $this->sub_form->save();
		$this->item_field->set_value($sub_form_item_id);
	}
	
	public function render_locked(){
		return $this->sub_form->render();
	}
	
	public function render(){
		return parent::render($this->sub_form->render(), 'parent_field');
	}
}

?>
