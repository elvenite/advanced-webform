<?php

class PodioAdvancedFormImageElement extends PodioAdvancedFormFileElement{
	
	public function set_value($values){
		parent::set_value($values);

		$this->item_field->set_value($this->get_files());
	}
}

?>
