<?php
function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}
if (Phpfox::getService('admincp.product.process')->delete('Webinar')){
    if (file_exists(PHPFOX_DIR_XML.'Webinar.xml')){
        unlink(PHPFOX_DIR_XML.'Webinar.xml');
        if (is_dir(PHPFOX_DIR_MODULE.'webinar')){
            delTree(PHPFOX_DIR_MODULE.'webinar');
        }
    }
}