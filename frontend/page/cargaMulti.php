<?php

class Page_cargaMulti extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar('Desarrollador')){
			
			$this->title = "Cargar datos";
		
			date_default_timezone_set('America/Argentina/Buenos_Aires');
					
			$form = $this->add('Form');
			
			$c = $form->add('Columns');
			$c1 = $c->addColumn(6);
			
			$fechas = $modulos->obtenerLunes(0);
						
			$semana = $c1->addField('dropdown','semana');
			$semana->setValueList($fechas);
			
			// Agrega un campo para cada proyecto existente.
			$proyectos = array();
			$model_proyecto = $this->add('Model_Proyecto');
			$model_proyecto->addCondition("activo", 1);
			foreach($model_proyecto as $proy){
				$proyecto = $c1->addField('Number',$proy['nombre'])->setRange(0,100)->setCaption($proy['nombre']);
				$proyecto->js(true)->univ()->numericField();
				$proyectos[] = $proyecto;
			}
			
			$c1->addSubmit("Cargar");
					
			$c2 = $modulos->mostrarDedicaciones($c);
			
			if($form->isSubmitted()){

				$reload[] = $c2->js()->reload();
				
				// Calcula el porcentaje total.
				$porcentaje = 0;
				foreach($model_proyecto as $proy) $porcentaje += $form[$proy['nombre']];
				
				// Va cargando la dedicación a cada proyecto.
				foreach($model_proyecto as $proy){
					if($form[$proy['nombre']]>0){
						$model = $this->add("Model_Dedicacion");
						$model['empleado_id'] = $modulos->cotejarEmpleado()['id'];
						$model['proyecto_id'] = $proy['id'];
						$model['semana'] = date('Y-m-d', strtotime($fechas[$semana->get()]));
						$model['porcentaje'] = $form[$proy['nombre']];
						$model->save();
					}
				}
				
				// Si el porcentaje total es 0, muestra un error.
				if($porcentaje > 0) $reload[] = $form->js()->univ()->successMessage("Dedicaciones cargadas con éxito.");
				else $reload[] = $this->js()->univ()->errorMessage("No se ha introducido ningún dato.");

				return $this->js(null, $reload)->execute();
			}
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}
