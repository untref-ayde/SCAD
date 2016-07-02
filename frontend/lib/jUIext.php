<?php

class  jUIext extends jUI{

	function addStaticIncludeExt($file, $params, $ext = '.js'){
		if(@$this->included['js-'.$file.$ext]++)return;

        if(strpos($file,'http')!==0 && $file[0]!='/'){
            $url=$this->api->locateURL('js',$file.$ext);
        }else $url=$file;

        if(!empty($params)){
        	$parameters = '?' . $params;
        }
        else{
        	$parameters = '';
        }

        $this->api->template->appendHTML('js_include',
                '<script type="text/javascript" src="'.$url. $parameters . '"></script>'."\n");
        return $this;
	}
}