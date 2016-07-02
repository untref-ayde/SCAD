<?php
/**
 * The layout engine helping you to create a flexible and responsive layout
 * of your page. The best thing is - you don't need to CSS !
 *
 * Any panel you have added can have a number of classes applied. Of course
 * you are can use those classes in other circumstances too.
 *
 *
 */
class Layout_Fluid extends Layout_Basic {

    /**
     * Pointns to a user_menu object
     *
     * @var [type]
     */
    public $user_menu;

    /**
     * Points to a footer, if initialized
     *
     * @var [type]
     */
    public $footer;

    /**
     * Points to menu left-menu if initialized
     *
     * @var [type]
     */
    public $menu;

    /**
     * Points to top menu
     *
     * @var [type]
     */
    public $top_menu;

    function defaultTemplate() {
        return array('layout/fluid');
    }

    function init(){
        parent::init();
		if(!$this->app->auth->isLoggedIn()){
			$top_menu = $this->add('Menu_Horizontal',null,'Main_Menu');
			$form = $top_menu->add('Form',null,null,['form/minimal']);
			$c = $form->add('Columns');
			$c1 = $c->addColumn(4);
			$c1->addField('Line','email');
			$c2 = $c->addColumn(4);
			$c2->addField('Password','clave');
			$c3 = $c->addColumn(2);
			$c3->addSubmit('Ingresar');
			$form->onSubmit(function($form){

				if(!$form->app->auth->verifyCredentials(
					$form['email'],
					$form['clave']
				)) return $form->error('email','Usuario y/o contraseña incorrectos.');
				
				$form->app->auth->login($form['email']);
				return $this->js()->univ()->redirect('index')->execute();
			});
		}
        if ($this->template->hasTag('UserMenu')) {
			$u=$this->add('Menu_Horizontal',null,'UserMenu');
            if($this->app->auth->isLoggedIn()){
				$model = $this->add('Model_Empleado');
				$model->addCondition('email',$this->app->auth->model['email']);
				$model->tryLoadAny();
                $menu = $u->addMenu($model['nombre']);
				$menu->addItem(['Cambiar password', 'icon'=>'lock'],'cambiarPass');
				$menu->addItem(['Cerrar sesión', 'icon'=>'logout'],'logout');
            } else {
				$u->add('Text')->setHtml('<div align="right"><img src="scad.jpg" alt="SCAD"></div>');
            }
        }
    }

    function addHeader($class = 'Menu_Objective') {
        $this->header_wrap = $this->add('View',null,'Header',array('layout/fluid','Header'));

        $this->header=$this->header_wrap->add($class,null,'Header_Content');

        return $this->header;
    }

    function addMenu($class = 'Menu_Horizontal', $options=null) {
        return $this->menu = $this->add($class,$options,'Main_Menu');
    }

    function addFooter($class = 'View') {
        return $this->footer = $this->footer = $this->add($class,null,'Footer_Content');
    }
}
