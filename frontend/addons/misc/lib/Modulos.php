<?php

namespace misc;
class Modulos extends \AbstractObject{
	
	/*
	 * Busca a qué empleado corresponden los datos del usuario que ingresó al sistema.
	 */
	function cotejarEmpleado(){
		$model_auth = $this->add('Model_Empleado');
		$model_auth->addCondition('email',$this->app->auth->model['email']);
		$model_auth->tryLoadAny();
		return $model_auth;
	}
	
	/*
	 * Si el día del mes es 5 o más, devuelve todos los lunes de este mes.
	 * De lo contrario, devuelve todos los del mes anterior.
	 * $i: compensación en meses (ej. si i=2 y el día actual es >= 5,
	 * se devolverán todos los lunes de hace 2 meses.)
	 */
	function obtenerLunes($i){
		$fechas = array();
		$maximo = time();
		for($j=0; $j<$i; $j++){
			$maximo = strtotime('-1 month', $maximo);
		}
		$fecha = $maximo;
		if (date("d", $maximo) >= 5){
			while(date("n", $maximo) - date("n", $fecha) == 0){
				if(date("w", $fecha) == 1) array_push($fechas, date('j-n-Y',$fecha));
				$fecha = strtotime('-1 day', $fecha);
			}
		}else{
			while(date("n", $maximo) - date("n", $fecha) == 0) $fecha = strtotime('-1 day', $fecha);
			while(date("n", $maximo) - date("n", $fecha) == 1){
				if(date("w", $fecha) == 1) array_push($fechas, date('j-n-Y',$fecha));
				$fecha = strtotime('-1 day', $fecha);
			}
		}
		return $fechas;
	}
	
	/*
	 * Devuelve un conjunto de grillas mostrando todas las dedicaciones que el empleado actual
	 * cargó en el mes presente.
	 * $c: la vista donde se agregarán las grillas.
	 */
	function mostrarDedicaciones($c){
		$c2 = $c->addColumn(6);
		$fechas = $this->obtenerLunes(0);
		$id = $this->cotejarEmpleado()['id'];
		foreach($fechas as $dia){
			$c2->add('H4')->set($dia);
			$crud=$c2->add('CRUD', array('allow_add'=>false,));
			$crud->setModel("Dedicacion",["proyecto","porcentaje"]);
			$crud->model->addCondition('empleado_id',$id)->addCondition('semana',date('Y-m-d', strtotime($dia)));
			$total = 0;
			foreach($crud->model as $dedicacion) $total += $dedicacion['porcentaje'];
			$c2->add('P')->set('Total semanal: '.$total.'%');
		}
		return $c2;
	}
	
	/*
	 * Si el día del mes es 5 o más, devuelve el período actual (ej. 1/2016).
	 * De lo contrario, devuelve el período anterior.
	 * $i: compensación en meses (ej. si i=2 y el día actual es >= 5,
	 * se devolverá el período de hace 2 meses.)
	 */
	function mostrarPeriodo($i){
		$fecha = time();
		for($j=0; $j<$i; $j++) $fecha = strtotime('-1 month', $fecha);
		if (date("d", $fecha) < 5) $fecha = strtotime('-1 month', $fecha);
		return date('n/Y',$fecha);
	}
	
	/*
	 * Valida si el empleado actual posee el cargo necesario para ingresar a una página.
	 * $cargos: los cargos autorizados a ver la página.
	 */
	function validar($cargos){
		$model_auth = $this->cotejarEmpleado();
		if(!is_array($cargos)) return $model_auth['cargo'] == $cargos && $model_auth['activo'];
		else{
			foreach($cargos as $cargo){
				if ($model_auth['cargo'] == $cargo && $model_auth['activo']) return true;
			}
		}
		return false;
	}
	
	/*
	 * Crea una barra inferior con el logo de MVC.
	 * $page: la página a la cual se agregará la barra.
	 */
	function barraInferior($page){
		$page->add('HR');
		$page->add('Text')->setHtml('<div align="right"><img src="logo.jpg" alt="MVC" title="© 2016 MVC. Desarrollado con Agile Toolkit."></div>');
	}
	
	/*
	 * Si el empleado no puede ver la pantalla debido a su cargo, se le informará de ello.
	 * Si el empleado no se encuentra activo, se cerrará sesión
	 * $page: la página a la cual se agregará el aviso.
	 */
	function accesoDenegado($page){
		$page->title = "Acceso denegado";
		$model_auth = $this->cotejarEmpleado();
		if(!$model_auth['activo']) $this->app->auth->logout();
		else $page->add('View_Error')->set('Esta página no puede ser vista por un '.strtolower($model_auth['cargo']).'.');
	}
}
