<?php

class Page_administrar extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar('Administrador')){
		
			$this->title = "Administrar";
		
			$tabs = $this->add('Tabs');
			
			// Agrega una pestaña para administrar empleados.
			$tab = $tabs->addTab('Empleados');
			$crud = $tab->add("CRUD", array('allow_del'=>false));
			$crud->setModel('Model_Empleado');
			$crud->grid->addFormatter('sueldo_bruto','money');
			
			// Agrega una pestaña para administrar proyectos.
			$tab = $tabs->addTab('Proyectos');
			$crud = $tab->add("CRUD", array('allow_del'=>false));
			$crud->setModel('Model_Proyecto');
			
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}
