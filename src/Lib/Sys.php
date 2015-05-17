<?php
namespace Lib;

use Twig_Environment;
use Twig_Loader_Filesystem;

abstract class Sys
{

    static function getTwig()
    {
        $loader = new Twig_Loader_Filesystem('../src/View');
        return new Twig_Environment($loader);
    }

    static function formatSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}