<?php
foreach ($allApps as $iKey=>$app){
    if ($app->id == '__module_webinar'){
        unset($allApps[$iKey]);
    }
}