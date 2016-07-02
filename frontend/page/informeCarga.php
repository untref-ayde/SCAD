<?php

class Page_informeCarga extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar('Administrador')){
		
			$periodo = $modulos->mostrarPeriodo(0);
			
			$this->title = "Informe de carga (".$periodo.')';
			$this->add("View_Info")->set("Los empleados inactivos se muestran en sombreado celeste. Datos correspondientes al período ".$periodo.'.');
			
			$fechas = $modulos->obtenerLunes(0);
			
			// Obtener la carga de cada empleado a cada proyecto en cada semana.
			foreach($fechas as $dia){
				$this->add('H4')->set(date('j-n-Y', strtotime($dia)));
				$cargas = array();
				$grid = $this->add('MyGrid');
				$model_empleado = $this->add('Model_Empleado');
				$model_empleado->addCondition('cargo', 'Desarrollador');
				foreach ($model_empleado as $empleado) array_push($cargas, ['Empleado' => $empleado['nombre'], 'Email' => $empleado['email'], 'Dia' => $dia, 'Carga' => $this->obtenerPorcentaje($empleado['id'], $dia).'%']);
				$grid->setSource($cargas);
				$grid->addColumn('link','Empleado');
				$grid->addColumn('Carga');
				$grid->removeColumn('id');
				foreach($grid->columns as $field=>$junk) $grid->addFormatter($field,'marcarInactivos');
			}
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
	
	/*
	 * Obtiene el porcentaje de cargas de un empleado en una semana específica.
	 */
	function obtenerPorcentaje($empleado, $semana){
		
		$model = $this->add("Model_Dedicacion");
		$model->addCondition("empleado_id", $empleado);
		$model->addCondition("semana", $semana);
		
		$porcentaje = 0;

		foreach($model as $row) $porcentaje += $row["porcentaje"];

		return $porcentaje;
	}
	
}