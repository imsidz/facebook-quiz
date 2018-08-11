<?php

return array(

    'dir' => 'media',

    'url' => null,

    'connectorAction' => null,

    'resizable' => false,

    'debug' => false,

    'requestType' => 'post',

    'accessControl' => 'W3G\MediaManager\MediaManager::accessControl',

    'roots' => [[
        'driver' => 'LocalFileSystem',
        'path' => content_path('media'),
        'URL' => ''
    ]]
);
