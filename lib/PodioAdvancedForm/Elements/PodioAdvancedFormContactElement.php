<?php

class PodioAdvancedFormContactElement extends PodioAdvancedFormElement{
	
	protected $sub_field_attributes = array();
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		
		if ($this->app_field->config['settings']['type'] != 'space_contacts'){
			// workspace members should not be exposed in the outside world
			return false;
		}
		
		$this->set_sub_attributes(array(
			'name' => array(
				'placebolder' => 'Name',
				'required' => true,
				'type' => 'text',
				'multiple' => false,
				
			)
		))
				
		/*
{
  "mail": [
    "info@acme.com"
  ],
  "image": null,
  "profile_id": 74838393,
  "connection_id": null,
  "phone": [
    "08 - 555 123 12"
  ],
  "link": "https://elvenite.podio.com/dev-podio-event-map/contacts/74838393",
  "skype": "anderssonz",
  "city": "Karlstad",
  "about": "sldsbgflsfb lwfb wlefhbewef lwrjghb qweflj hwbf",
  "user_id": null,
  "name": "Anders Andersson",
  "zip": "652 24",
  "rights": [
    "delete",
    "view",
    "update"
  ],
  "url": [
    "http://acme.com"
  ],
  "external_id": null,
  "space_id": 1264291,
  "title": [
    "Manager"
  ],
  "org_id": null,
  "state": "Värmland",
  "country": "Sweden",
  "organization": "Acme Inc.",
  "type": "space",
  "last_seen_on": null,
  "address": [
    "Herrgårdsgatan 6A"
  ]
}
		 */
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
		
		/**
		 * fields inside a contact field
		 * name: The full name [String, single]
		 * organization: The organization or company the person is associated with [String, single]
		 * title: The persons title, usually the work title [String, multiple]
		 * phone: The phone number [String, multiple]	
		 * mail: Email address [String, multiple]
		 * skype: The username for Skype [String, single]
		 * address: The address where the person lives or work [String, multiple]
		 * zip: The zip code of the address [String, single]
		 * city: The name of the city [String, single]
		 * country: The name of the country [String, single]
		 * url: An URL to the persons homepage or the homepage of the company [String, multiple]
		 */
	
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
		
		$type = $this->get_attribute('type');
		unset($attributes['type']);
		$description = $this->get_attribute('description');
		unset($attributes['description']);
		$required = $this->get_attribute('required');
		unset($attributes['required']);
		
		$attributes_string = '';
		foreach($attributes AS $key => $attribute){
			$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
		}
		
		if ($type == 'text'){
			$element = '<input type="text"';
		} else {
			$element = '<textarea';
		}
		
		if ($required){
			$element .= ' required';
		}
		
		$element .= $attributes_string;
		
		$element .= '>';
		
		if ($type == 'textarea'){
			$element .= '</textarea>';
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
						$element,
						$description_decorator
					);
		
		return $decorator;
	}
	
	public function get_sub_attribute($key){
		if (!array_key_exists($key, $this->sub_field_attributes)){
			return null;
		}
		
		return $this->attributes[$key];
	}
	
	public function set_sub_attribute($key, $value){
		$key = (string) $key;
		$this->sub_field_attributes[$key] = $value;
	}
	
	public function set_sub_attributes($sub_attributes){
		$this->sub_field_attributes = $sub_attributes;
	}
}

?>
