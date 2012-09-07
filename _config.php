<?php

Director::addRules(50, array(
	DispatchITExporter::$url_segment.'//$Action/$ID' => 'DispatchITExporter',
));

Object::add_extension("Order", "DispatchITOrderDecorator");