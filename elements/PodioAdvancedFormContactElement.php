<?php

require_once 'PodioAdvancedFormContactsSubElements.php';

class PodioAdvancedFormContactElement extends PodioAdvancedFormElement{
	
	protected $sub_fields;
	
//	protected $decorators = array(
//		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls"><fieldset>%3$s%4$s</fieldsset></div></div>'
//	);
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		
		if ($this->app_field->config['settings']['type'] != 'space_contacts'){
			// workspace members should not be exposed in the outside world
			//return false;
			throw new ErrorException('Workspace members is not supported.');
		}
		
		// Contact Name
		$this->add_sub_field('name', new PodioAdvancedFormContactsSubElementText(array(
			'name' => 'name',
			'required' => true, // name is required by Podio
			'placeholder' => 'Name',
			'parent' => $this,
		)));
		
		// Contact Title
		$this->add_sub_field('title', new PodioAdvancedFormContactsSubElementText(array(
			'name' => 'title',
			'required' => false,
			'placeholder' => 'Title',
			'multi' => true,
			'parent' => $this,
		)));
		
		// Contact Organization
		$this->add_sub_field('organization', new PodioAdvancedFormContactsSubElementText(array(
			'name' => 'organization',
			'required' => false, 
			'placeholder' => 'Organisation',
			'parent' => $this,
		)));
		
		// Contact Email
		$this->add_sub_field('mail', new PodioAdvancedFormContactsSubElementEmail(array(
			'name' => 'mail',
			'required' => false, 
			'placeholder' => 'Email',
			'parent' => $this,
		)));
		
		// Contact Phone
		$this->add_sub_field('phone', new PodioAdvancedFormContactsSubElementText(array(
			'name' => 'phone',
			'required' => false,
			'placeholder' => 'Phone',
			'multi' => true,
			'parent' => $this,
		)));
		
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
	
	public function add_sub_field($name, PodioAdvancedFormContactsSubElement $element){
		$this->sub_fields[$name] = $element;
	}
	
	public function set_value($values){
		if (is_numeric($values)){
			$profile_id = (int) $values;
			
			parent::set_value(array(
				'profile_id' => $profile_id,
			));
		}
		
		if (isset($values['name']) && !empty($values['name'])){
			$space_id = $this->form->get_app()->space_id;
			if ($profile_values = $this->get_value()){
				$profile_id = $profile_values[0]['value']['profile_id'];
				PodioContact::update($profile_id, $values);
			} else {
				$profile_id = PodioContact::create($space_id, $values);
			}
                        
                        $values['profile_id'] = $profile_id;

			parent::set_value($values);
		}
	}
        
        public function render_locked(){
            $elements = array();
            foreach($this->sub_fields AS $sub_field){
                $elements[] = $sub_field->render_locked();
            }
            
            return implode('', $elements);
        }
	
	public function render($element = null, $default_field_decorator = 'field'){
		$elements = array();
		
		foreach($this->sub_fields AS $sub_field){
			$elements[] = $sub_field->render();
		}
		
		if ($this->get_form()->is_sub_form()){
			$decorator_string = 'sub_parent_field';
		} else {
			$decorator_string = 'parent_field';
		}
		
		return parent::render(
			implode('', $elements),
			$decorator_string
		);
	}
}