<?php

class Page_cargaSimple extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		if($modulos->validar('Desarrollador')){
			
			$this->title = "Cargar datos";
			
			date_default_timezone_set('America/Argentina/Buenos_Aires');
					
			$form = $this->add('Form', null, null, ['form/minimal']);
			
			$c = $form->add('Columns');
			$c1 = $c->addColumn(6);
			
			$fechas = $modulos->obtenerLunes(0);
			
			$semana = $c1->addField('dropdown','semana');
			$semana->setValueList($fechas);
			
			$proyecto = $c1->addField("dropdown", "proyecto");
			$proyecto->setModel("Proyecto")->addCondition("activo", 1)->setOrder('computable',true);
		
			$porcentaje = $c1->addField('Number',"porcentaje")->setCaption('Porcentaje, sin el signo %.');
			$porcentaje->js(true)->univ()->numericField();
			
			$c1->addSubmit("Cargar");
					
			$c2 = $modulos->mostrarDedicaciones($c);
			
			if($form->isSubmitted()){
				
				// Carga la dedicación.
				$model = $this->add("Model_Dedicacion");
				$model['empleado_id'] = $modulos->cotejarEmpleado()['id'];
				$model['proyecto_id'] = $proyecto->get();
				$model['semana'] = date('Y-m-d', strtotime($fechas[$semana->get()]));
				$model['porcentaje'] = $form['porcentaje'];
				$model->save();
					
				return $c2->js()->univ()->reload()->successMessage("Dedicación cargada con éxito.")->execute();
			}
		}else $modulos->accesoDenegado($this);
		$modulos->barraInferior($this);
	}
}
