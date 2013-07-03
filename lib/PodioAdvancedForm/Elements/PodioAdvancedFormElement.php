<?php


class PodioAdvancedFormElement {
	protected $field;
	protected $form;
	protected $value;
	
	public function __construct(PodioAppField $field, PodioAdvancedForm $form, $value = null) {
		$this->field = $field;
		$this->form = $form;
		$this->value = $value;;
	}
	
	
}

?>
