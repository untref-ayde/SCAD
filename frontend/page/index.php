<?php

class page_index extends \Page {
    function init(){
        parent::init();
		
		$this->title = "SCAD";
		
		$modulos = $this->add('misc/Modulos');
		
		if($this->app->auth->isLoggedIn()){
			$this->add('View_Info')->set('Seleccione una opción en la barra superior.');
		}else{
			// Si no inició sesión, muestra los campos para registrarse.
			$this->add("View_Info")->set("Debe registrarse para poder utilizar el sistema. Si ya se registró ingrese completando los campos de arriba.");
		
			$form = $this->add('MVCForm', null, null, ['form/minimal']);
			$form->setModel('Usuario');
			
			$form->addField('password', 'doble')->setCaption('Repita la password');
							
			$form->addSubmit("Registrarse");
			
			if($form->isSubmitted()){
			
				$auth=$this->api->auth;
				$l=$form->get('email');
				$p=$form->get('password');
				
				// Si no existe empleado con el email ingresado, muestra error.
				$model = $this->add('Model_Empleado');
				$model->addCondition('email', $l);
				$model->tryLoadAny();
				if(!$model->loaded()) return $form->error('email','No hay ningún empleado registrado con ese email.');
				
				$model_usuario = $this->add('Model_Usuario');
				$model_usuario->addCondition('email', $l);
				$model_usuario->tryLoadAny();

				// Si ya existe una cuenta con el email ingresado, muestra error.
				if($model_usuario->loaded()) return $form->error('email','El email ingresado ya fue registrado.');
				
				// Si la password es muy corta o no coincide con la doble, muestra error.
				if(strlen($p)<8) return $form->error('password','La password debe poseer 8 caracteres como mínimo.');
				if($form->get('doble') != $p) return $form->error('doble','Las passwords no coinciden.');

				// Hashea la password, guarda el usuario e inicia sesión.
				$enc_p = $auth->encryptPassword($p,$l);
				$form->set('password', $enc_p);

				$form->update();
				$form->app->auth->login($l);
				$this->js()->univ()->redirect('index')->execute();
							
			}
		}
		$modulos->barraInferior($this);
    }
}
