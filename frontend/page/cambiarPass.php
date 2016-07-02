<?php

class Page_cambiarPass extends Page{

	public function init(){
		parent::init();
		
		$modulos = $this->add('misc/Modulos');
		
		$this->title = "Cambiar password";
		
		$form = $this->add('Form', null, null, ['form/minimal']);
		
		$form->addField('password', 'actual')->setCaption('Password actual');
		$form->addField('password', 'nueva')->setCaption('Nueva password');
		$form->addField('password', 'doble')->setCaption('Repita la nueva password');
							
		$form->addSubmit("Cambiar");
					
		if($form->isSubmitted()){
			
			// Si la password actual es incorrecta o la nueva no cumple las revisiones, muestra error.
			if(!$form->app->auth->verifyCredentials($this->app->auth->model['email'],$form['actual'])) return $form->error('actual','La password actual es incorrecta.');
			if(strlen($form['nueva'])<8) return $form->error('nueva','La password debe poseer 8 caracteres como mínimo.');
			if($form['nueva'] != $form['doble']) return $form->error('doble','Las passwords no coinciden.');
			
			// Hashea la contraseña, la reemplaza y muestra un mensaje.
			$m = $this->app->auth->model;
			$this->app->auth->addEncryptionHook($m);
			
			$m['password'] = $form['nueva'];
			$m->save();
			return $this->js()->univ()->successMessage('Password cambiada con éxito')->execute();
		}
		$modulos->barraInferior($this);
	}
}
