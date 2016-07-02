<?php

class Model_Usuario extends SQL_Model {

	public $table = "usuario";
	
	public function init(){
		parent::init();

		$this->addField('email');
        $this->addField('password')->type('password');
	}	
}