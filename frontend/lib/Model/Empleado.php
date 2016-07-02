<?php

class Model_Empleado extends Model_Table {

	public $table = "empleado";
	public $title_field = "nombre";

	public function init(){
		parent::init();

		$this->addField("nombre")->sortable(true);
		$this->addField("email")->sortable(true);
		$this->addField("activo")->type('boolean')->defaultValue(true)->sortable(true);
		$this->addField('cargo')->enum(array('Desarrollador','Administrador','Gerente'))->defaultValue('Desarrollador')->sortable(true);
		$this->addField("sueldo_bruto")->type("int")->defaultValue(0)->sortable(true);
		
		// Revisiones antes de guardar el empleado.
		$this->addHook('beforeSave',$this);
	}
	
	public function beforeSave(){
		
		// Revisa que se haya ingresado un nombre.
		if($this["nombre"] == '') $this->app->js()->univ()->alert('Ingrese un nombre para el empleado.')->execute();
		
		// Revisa que el email ingresado no exista.
		$model = $this->add('Model_Empleado');
		$model->addCondition("email", $this['email']);
		$model->tryLoadAny();
		
		if($model->loaded() && $model['id'] != $this['id']) $this->app->js()->univ()->alert('El email ingresado ya existe.')->execute();
		
		// Revisa que el email tenga un @.
		if(!strpos($this['email'], '@')) $this->app->js()->univ()->alert('El email ingresado debe contener un signo "@".')->execute();
		
		// Revisa que el sueldo bruto sea > 0
		if($this["sueldo_bruto"] <= 0) $this->app->js()->univ()->alert('El sueldo bruto debe ser mayor a 0.')->execute();
		
		// Revisa que se haya ingresado un cargo válido.
		if($this['cargo'] != 'Desarrollador' && $this['cargo'] != 'Administrador' && $this['cargo'] != 'Gerente')
			$this->app->js()->univ()->alert('El cargo ingresado no es correcto.')->execute();
	}
}