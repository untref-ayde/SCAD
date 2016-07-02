<?php

class Model_Proyecto extends Model_Table {

	public $table = "proyecto";
	public $title_field = "nombre";

	public function init(){
		parent::init();

		$this->addField("nombre")->sortable(true);
		$this->addField("activo")->type("boolean")->defaultValue(true)->sortable(true);
		$this->addField("capacitacion")->type("boolean")->defaultValue(false)->sortable(true);
		$this->addField("computable")->type("boolean")->defaultValue(true)->sortable(true);
		
		// Revisiones antes de guardar el proyecto.
		$this->addHook('beforeSave',$this);
	}
	
	public function beforeSave(){
		
		// Revisa que se haya ingresado un nombre.
		if($this["nombre"] == '') $this->app->js()->univ()->alert('Ingrese un nombre para el proyecto.')->execute();
		
		// Revisa que el nombre ingresado no exista.
		$model = $this->add('Model_Proyecto');
		$model->addCondition("nombre", $this['nombre']);
		$model->tryLoadAny();
		
		if($model->loaded() && $model['id'] != $this['id']) $this->app->js()->univ()->alert('Ya existe un proyecto con ese nombre.')->execute();
	}
}