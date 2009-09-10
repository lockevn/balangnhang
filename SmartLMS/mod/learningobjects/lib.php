<?php


function learningobjects_get_types() {
    $types = array();

    $type = new object;
    $type->modclass = MOD_CLASS_RESOURCE;
    $type->type = 'learningobjects';
    $type->typestr = get_string('lo', 'smartcom');
    $types[] = $type;

    return $types;
}


?>
