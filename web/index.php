<?php
require_once "../vendor/autoload.php";


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Lib\Sys;

$valid_passwords = array ("root" => "monitor$1");
$valid_users = array_keys($valid_passwords);

$user = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
$pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="Authorization Needed"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Not authorized");
}

$request    = Request::createFromGlobals();
Twig_Autoloader::register();
!is_dir('files') ? mkdir('files') : null;

if($request->getMethod() == 'GET'){
    $files          = scandir('files');
    $fileCount      = count($files) - 2;
    $fileSize       = 0;

    foreach($files as $k=>$v){
        if($v !== '.' && $v !== '..'){
            $fileSize += filesize('files/'.$v);
        }
    }

    echo Sys::getTwig()->render('index.html.twig',[
        'count' => $fileCount,
        'size' => Sys::formatSize($fileSize)
    ]);
}


if($request->getMethod() == 'POST'){
    $images     = $request->files->get('images');

    if(count($images) > 0){
        $links = [];
        foreach($images as $k=>$v){
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $v */
            $originalName   = $v->getClientOriginalName();
            $size           = $v->getSize();
            $ext            = $v->guessExtension();
            $name           = $size.'_'.time().'_'.md5(mt_rand(0,10000)).".".$ext;
            $mime           = $v->getClientMimeType();

            if(in_array($mime,['image/png','image/jpeg',]) && $size < 1024 * 1024){
                $v->move('files',$name);
                $links[] = $request->getHost().'/files/'.$name;
            }else{
                $links[] = "Файл $originalName не соответствует требованиям";
            }
        }

        echo Sys::getTwig()->render('uploaded.html.twig',[
            'links' => $links
        ]);
    }
}















