<?php

include 'C:\xampp\htdocs\vendor\autoload.php';
include 'C:\xampp\htdocs\frontend\lib\Model\Usuario.php';
include 'C:\xampp\htdocs\frontend\lib\Model\Empleado.php';
include 'C:\xampp\htdocs\frontend\lib\Model\Proyecto.php';
include 'C:\xampp\htdocs\frontend\lib\Model\Dedicacion.php';

class Tests extends PHPUnit_Framework_TestCase{
	
	public function testInicializar()
    {
        $app = new App_CLI();
        $app->pathfinder->addLocation(array(
            'addons'=>array('atk4-addons','addons','vendor'),
            'php'=>array('shared','shared/lib','../lib','..'),
            'mail'=>array('templates/mail'),
        ))->setBasePath('C:\xampp\htdocs\frontend');
        $app->dbConnect('mysql://root:@localhost/tests');
        $app->page = '';
        $app->add('Auth')
            ->usePasswordEncryption()
            ->setModel('Model_Usuario', 'email', 'password')
        ;

        return $app;
    }
	
	/**
     * @depends testInicializar
     */
    public function testCuentaInvalida(App_CLI $app)
    {
        $this->app = $app;

        $validez = $this->app->auth->verifyCredentials(
					'a@b.com',
					'12345678'
				);
		
		$this->assertFalse($validez);
    }
	
	/**
     * @depends testInicializar
     */
    public function testAgregarEmpleado(App_CLI $app)
    {
        $this->app = $app;

        $m = $app->add('Model_Empleado');
        $m
            ->set('nombre','Juan Pérez')
            ->set('email','a@b.com')
			->set('sueldo_bruto',10000)
            ->save();
			
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('email', 'a@b.com');
		$this->assertTrue($m->loaded());
		
		$this->assertEquals('Juan Pérez',$m['nombre']);
		$this->assertEquals('Desarrollador',$m['cargo']);
		$this->assertEquals('a@b.com',$m['email']);
		$this->assertEquals(1, $m['activo']);
		$this->assertEquals(10000,$m['sueldo_bruto']);
    }
	
	/**
     * @depends testInicializar
     */
    public function testEliminarEmpleado(App_CLI $app)
    {
        $this->app = $app;

        $m = $app->add('Model_Empleado');
        $m->loadBy('email', 'a@b.com');
		$this->assertTrue($m->loaded());
		$m->delete();
		
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('email', 'a@b.com');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testAgregarConCargoErroneo(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Empleado');
			$m
				->set('nombre','Juan Pérez')
				->set('cargo','Erróneo')
				->set('email','a@c.com')
				->set('sueldo_bruto',10000)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('email', 'a@c.com');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
    public function testEmpleadoSinSueldo(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Empleado');
			$m
				->set('nombre','Juan Pérez')
				->set('email','a@b.com')
				->set('activo',true)
				->set('sueldo_bruto',0)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('email', 'a@b.com');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
    public function testEmpleadoEmailInvalido(App_CLI $app)
    {
        $this->app = $app;
		
        try{
			$m = $app->add('Model_Empleado');
			$m
				->set('nombre','Juan Pérez')
				->set('email','a')
				->set('activo',true)
				->set('sueldo_bruto',10000)
				->save();
		}catch(BaseException $e){}
		
		$m->tryLoadBy('email', 'a');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testAgregarProyecto(App_CLI $app)
    {
        $this->app = $app;

        $m = $app->add('Model_Proyecto');
        $m
            ->set('nombre','Ejemplo')
            ->save();
			
		$m = $app->add('Model_Proyecto');
		$m->tryLoadBy('nombre', 'Ejemplo');
		$this->assertTrue($m->loaded());
		
		$this->assertEquals('Ejemplo',$m['nombre']);
		$this->assertEquals(1, $m['activo']);
		$this->assertEquals(0, $m['capacitacion']);
		$this->assertEquals(1, $m['computable']);
    }
	
	/**
     * @depends testInicializar
     */
    public function testEliminarProyecto(App_CLI $app)
    {
        $this->app = $app;

        $m = $app->add('Model_Proyecto');
        $m->loadBy('nombre', 'Ejemplo');
		$this->assertTrue($m->loaded());
		$m->delete();
		
		$m = $app->add('Model_Proyecto');
		$m->tryLoadBy('nombre', 'Ejemplo');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testAgregarDedicacion(App_CLI $app)
    {
        $this->app = $app;
		
		$m = $app->add('Model_Empleado');
        $m
			->set('id', 1)
            ->set('nombre','Juan Pérez')
            ->set('email','a@b.com')
			->set('sueldo_bruto',10000)
            ->save();
			
		$m = $app->add('Model_Proyecto');
        $m
			->set('id', 1)
            ->set('nombre','Ejemplo')
            ->save();

		$m = $app->add('Model_Dedicacion');
		$m
			->set('empleado_id','1')
			->set('proyecto_id','1')
			->set('semana','2016-01-04')
			->set('porcentaje',100)
			->save();
			
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('semana', '2016-01-04');
		$this->assertTrue($m->loaded());
		
		$this->assertEquals(1, $m['empleado_id']);
		$this->assertEquals(1, $m['proyecto_id']);
		$this->assertEquals('2016-01-04', $m['semana']);
		$this->assertEquals(100, $m['porcentaje']);
    }
	
	/**
     * @depends testInicializar
     */
    public function testEliminarDedicacion(App_CLI $app)
    {
        $this->app = $app;

        $m = $app->add('Model_Dedicacion');
        $m->loadBy('semana', '2016-01-04');
		$this->assertTrue($m->loaded());
		$m->delete();
		
		$m = $app->add('Model_Dedicacion');
        $m->tryLoadBy('semana', '2016-01-04');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testDedicacionEmpleadoInexistente(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Dedicacion');
			$m
				->set('empleado_id','99')
				->set('proyecto_id','1')
				->set('semana','2016-01-04')
				->set('porcentaje',100)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('empleado_id', '99');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testDedicacionProyectoInexistente(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Dedicacion');
			$m
				->set('empleado_id','1')
				->set('proyecto_id','99')
				->set('semana','2016-01-04')
				->set('porcentaje',100)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('proyecto_id', '99');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testDedicacionMayorA100(App_CLI $app)
    {
        $this->app = $app;
		
		$m = $app->add('Model_Proyecto');
        $m
			->set('id', 2)
            ->set('nombre','Otro ejemplo')
            ->save();
		
		try{
			$m = $app->add('Model_Dedicacion');
			$m
				->set('empleado_id','1')
				->set('proyecto_id','2')
				->set('semana','2016-01-04')
				->set('porcentaje',101)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('porcentaje', '101');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testDedicacionMartes(App_CLI $app)
    {
        $this->app = $app;

		try{
			$m = $app->add('Model_Dedicacion');
			$m
				->set('empleado_id','1')
				->set('proyecto_id','2')
				->set('semana','2016-01-05')
				->set('porcentaje',100)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('semana', '2016-01-05');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
	public function testDedicacionSemanalMayor(App_CLI $app)
    {
        $this->app = $app;
		
		$m = $app->add('Model_Dedicacion');
		$m
			->set('empleado_id','1')
			->set('proyecto_id','1')
			->set('semana','2016-01-04')
			->set('porcentaje',100)
			->save();

		try{
			$m = $app->add('Model_Dedicacion');
			$m
				->set('empleado_id','1')
				->set('proyecto_id','2')
				->set('semana','2016-01-04')
				->set('porcentaje',1)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Dedicacion');
		$m->tryLoadBy('proyecto_id', '2');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
    public function testEmpleadoEmailDuplicado(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Empleado');
			$m
				->set('nombre','Alguien')
				->set('email','a@b.com')
				->set('activo',true)
				->set('sueldo_bruto',10000)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('nombre', 'Alguien');
		$this->assertFalse($m->loaded());
		
		$m = $app->add('Model_Empleado');
		$m->loadBy('nombre', 'Juan Pérez');
		$m->delete();
		
		$m = $app->add('Model_Empleado');
		$m->tryLoadBy('nombre', 'Juan Pérez');
		$this->assertFalse($m->loaded());
    }
	
	/**
     * @depends testInicializar
     */
    public function testProyectoNombreDuplicado(App_CLI $app)
    {
        $this->app = $app;
		
		try{
			$m = $app->add('Model_Proyecto');
			$m
				->set('nombre','Ejemplo')
				->set('activo', false)
				->save();
		}catch(BaseException $e){}
		
		$m = $app->add('Model_Proyecto');
		$m->tryLoadBy('activo', false);
		$this->assertFalse($m->loaded());
		
		$m = $app->add('Model_Proyecto');
		$m->loadBy('nombre', 'Ejemplo');
		$m->delete();
		
		$m = $app->add('Model_Proyecto');
		$m->tryLoadBy('nombre', 'Ejemplo');
		$this->assertFalse($m->loaded());
    }
}