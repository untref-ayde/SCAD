<?php

class Model_Dedicacion extends Model_Table {

	public $table = "dedicacion";

	public function init(){
		parent::init();

		$empleado = $this->hasOne("Empleado")->sortable(true);
		$empleado->getModel()->addCondition('cargo','Desarrollador');
		$this->hasOne("Proyecto")->sortable(true);
		$this->addField("semana")->type("date")->sortable(true);
		$this->addField("porcentaje")->type("int")->sortable(true);

		// Revisiones antes de guardar la dedicación.
		$this->addHook('beforeSave',$this);
	}
	
	public function beforeSave(){
		
		// Revisa que el empleado exista y sea un desarrollador.
		$model = $this->add('Model_Empleado');
		$model->addCondition("id", $this['empleado_id']);
		$model->tryLoadAny();
		
		if(!$model->loaded()) $this->app->js()->univ()->alert('El empleado elegido no existe.')->execute();
		if($model['cargo'] != 'Desarrollador') $this->app->js()->univ()->alert('El empleado elegido no es un desarrollador.')->execute();
		
		// Revisa que el proyecto exista.
		$model = $this->add('Model_Proyecto');
		$model->addCondition("id", $this['proyecto_id']);
		$model->tryLoadAny();
		
		if(!$model->loaded()) $this->app->js()->univ()->alert('El proyecto elegido no existe.')->execute();
		
		// Revisa que se inserte un porcentaje válido.
		if(!$this["porcentaje"]) $this->app->js()->univ()->alert('Debe ingresar un porcentaje.')->execute();
		if($this["porcentaje"] > 100 || $this["porcentaje"] < 1) $this->app->js()->univ()->alert('El porcentaje ingresado debe estar entre 1 y 100.')->execute();
		
		// Revisa que el día elegido sea un lunes.
		if(date("w", strtotime($this['semana'])) != 1) $this->app->js()->univ()->alert('El día a cargar debe ser un lunes.')->execute();
		
		// Revisa que el total semanal no supere el máximo.
		$model = $this->add("Model_Dedicacion");
		$model->addCondition("empleado_id", $this['empleado_id']);
		$model->addCondition("semana", $this['semana']);
		
		$total = 0;
		foreach($model as $row){
			if($model['proyecto_id'] != $this['proyecto_id']) $total += $row["porcentaje"];
		}
		if($total + $this["porcentaje"] > 100) $this->app->js()->univ()->alert('El total semanal no debe superar 100%.')->execute();
		
		// Revisa que no existan horas cargadas por ese empleado a ese proyecto esa semana.
		$model = $this->add("Model_Dedicacion");
		$model->addCondition('empleado_id', $this['empleado_id']);
		$model->addCondition('proyecto_id', $this['proyecto_id']);
		$model->addCondition("semana", $this['semana']);
		$model->tryLoadAny();
		if ($model->loaded() && $model['id'] != $this['id']) $this->app->js()->univ()->alert('Ya se cargaron horas a '.$model['proyecto'].' esta semana.')->execute();
	}
}