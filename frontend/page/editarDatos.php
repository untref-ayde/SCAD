<?php

class Page_editarDatos extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar('Administrador')){
		
			$this->title = "Editar datos";
			
			$crud = $this->add('CRUD');
			$crud->setModel("Dedicacion",["empleado_id","proyecto_id","semana","porcentaje"],["empleado","proyecto","semana","porcentaje"]);
			
			// Agrega un botón para exportar los datos de la grilla a un archivo xls.
			$export = $crud->add("misc/Export");
			$export->setActualFields(['empleado','proyecto','semana','porcentaje']);
			
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}