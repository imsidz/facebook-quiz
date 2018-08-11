<?php

return [
    'APP_ENV'       =>  'production',
    'APP_DEBUG'     =>  empty($_COOKIE['devtest']) ? false : true,
    'APP_KEY'       =>  'I3FVW5iwavcEm4QftQjYke4Sgq9TAdOO',
    'CACHE_DRIVER'  =>  'file',
    'SESSION_DRIVER'=>  'file'
];