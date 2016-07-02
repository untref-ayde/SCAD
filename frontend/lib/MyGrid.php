<?php
class MyGrid extends Grid {
    function format_marcarCapacitaciones($field) {
		$model = $this->add('Model_Proyecto');
		$model->addCondition('nombre', $this->current_row['Proyecto']);
		$model->tryLoadAny();
        if($model['capacitacion']==1) {
			$this->setTDParam($field,'style/background','#D5EEF7');
        }else $this->setTDParam($field,'style/background','default');
    }
	function format_marcarInactivos($field) {
		$model = $this->add('Model_Empleado');
		$model->addCondition('email', $this->current_row['Email']);
		$model->tryLoadAny();
        if($model['activo']==false) {
			$this->setTDParam($field,'style/background','#D5EEF7');
        }else $this->setTDParam($field,'style/background','default');
    }
	function format_alinearDerecha($field){
		$this->setTDParam($field, 'align', 'right');
	}
}