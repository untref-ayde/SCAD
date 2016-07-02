<?php

class Page_copiarDatos extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		$model_auth = $modulos->cotejarEmpleado();
		
		if($modulos->validar('Desarrollador')){
			
			$this->title = "Copiar datos";
			
			date_default_timezone_set('America/Argentina/Buenos_Aires');
					
			$form = $this->add('Form', null, null, ['form/stacked']);
			
			$c = $form->add('Columns');
			$c1 = $c->addColumn(6);
			
			$fechas = $modulos->obtenerLunes(0);
		
			$origen = $c1->addField('dropdown','origen');
			$origen->setValueList($fechas);
			
			$destino = $c1->addField('dropdown','destino');
			$destino->setValueList($fechas);
			
			$c1->add('View_Warning')->set('La copia eliminará todos los datos cargados en la semana destino.');
			
			$c1->addSubmit("Copiar");
					
			$c2 = $modulos->mostrarDedicaciones($c);
			
			if($form->isSubmitted()){
				
				// Obtiene los días origen y destino.
				$reload[] = $c2->js()->reload();
				$diaOrigen = date('Y-m-d', strtotime($fechas[$origen->get()]));
				$diaDestino = date('Y-m-d', strtotime($fechas[$destino->get()]));
				
				// Elimina todas las dedicaciones de la semana destino.
				$model_dedicacion = $this->add('Model_Dedicacion');
				$cargado = false;
				do {
					$model_dedicacion->addCondition('empleado_id',$model_auth['id']);
					$model_dedicacion->addCondition('semana',$diaDestino);
					$model_dedicacion->tryLoadAny();
					$cargado = $model_dedicacion->loaded();
					if ($cargado) $model_dedicacion->delete();
				} while ($cargado);
				
				// Configura el modelo para agregar las dedicaciones.
				$model_dedicacion2 = $this->add('Model_Dedicacion');
				$model_dedicacion2->addCondition('empleado_id',$model_auth['id']);
				$model_dedicacion2->addCondition('semana',$diaOrigen);
				
				// Copia todas las del día origen.
				foreach($model_dedicacion2 as $dedicacion){
					$model_dedicacion = $this->add('Model_Dedicacion');
					$model_dedicacion['empleado_id'] = $model_auth['id'];
					$model_dedicacion['semana'] = $diaDestino;
					$model_dedicacion['proyecto_id'] = $dedicacion['proyecto_id'];				
					$model_dedicacion['porcentaje'] = $dedicacion['porcentaje'];
					$model_dedicacion->save();
				}
				$reload[] = $this->js()->univ()->successMessage('Copia realizada con éxito');
				return $this->js(null, $reload)->execute();
			}
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}
