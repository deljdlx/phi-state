<?php



spl_autoload_register(function ($calledClassName) {

    $normalizedClassName = preg_replace('`^\\\\`', '', $calledClassName);


    if(strpos($normalizedClassName, 'Phi\State') === 0) {

        $relativeClassName = str_replace('Phi\State', '', $normalizedClassName);
        $relativePath = str_replace('\\', '/', $relativeClassName);


        $definitionClass = __DIR__.'/class'.$relativePath.'.php';
        if(is_file($definitionClass)) {
            include($definitionClass);
        }
    }



});


