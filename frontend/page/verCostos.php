<?php

class Page_verCostos extends Page{

	public function init(){
		parent::init();
			
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar(['Administrador','Gerente'])){
		
			$periodo = $modulos->mostrarPeriodo(1);
			$this->title = 'Ver costos ('.$periodo.')';
			$this->add("View_Info")->set("Las capacitaciones se muestran en sombreado celeste. Datos correspondientes al período ".$periodo.'.');
		
			$model_recurso = $this->add('Model_Empleado');
			$model_proyecto = $this->add('Model_Proyecto');
			$costos_proyecto = array();
			$costos_recurso = array();
			
			// Calcula los costos de cada empleado.
			foreach($model_recurso as $recurso){
				$costo = round($recurso["sueldo_bruto"]*13/12*1.3);
				$costos_recurso[$recurso["email"]] = $costo;
			}
			
			$fechas = $modulos->obtenerLunes(1);
			
			// En base a los anteriores, calcula los costos de cada proyecto en el último mes.
			foreach($model_proyecto as $proyecto){
				if ($proyecto['computable']){
					$costoActual = 0;
					foreach ($model_recurso as $recurso){
						$model_dedicacion = $this->add('Model_Dedicacion');
						$model_dedicacion->addCondition('empleado_id',$recurso['id']);
						$model_dedicacion->addCondition('proyecto_id',$proyecto['id']);
						$semanasProyecto = 0.0;
						foreach($model_dedicacion as $dedicacion){
							if (in_array(date('j-n-Y', strtotime($dedicacion['semana'])), $fechas)) $semanasProyecto += $dedicacion['porcentaje'];
						}
						$costoActual += round($semanasProyecto/100 * $costos_recurso[$recurso['email']] / 4);
					}
					array_push($costos_proyecto, ['Proyecto' => $proyecto['nombre'], 'Costo' => $costoActual]);
				}
			}
			
			// Muestra los costos en una grilla.
			$grid = $this->add('MyGrid');
			$grid->setSource($costos_proyecto);
			$grid->addColumn('Proyecto');
			$grid->addColumn('money','Costo');
			$grid->removeColumn('id');
			foreach($grid->columns as $field=>$junk) $grid->addFormatter($field,'marcarCapacitaciones');
			
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}