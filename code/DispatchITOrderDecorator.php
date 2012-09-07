<?php

class DispatchITOrderDecorator extends DataObjectDecorator{
	
	function extraStatics(){
		return array(
			'db' => array(
				'SentToDispatchIT' => 'Boolean' 
			)
		);
	}
	
}