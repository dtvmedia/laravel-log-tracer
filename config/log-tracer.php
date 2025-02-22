<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Format string for the log messages
    |--------------------------------------------------------------------------
    |
    | Available placeholder:
    | - {{class_basename}}
    | - {{method}}
    | - {{file}}
    | - {{line}}
    | - {{message}}
    |
    */

    'format' => '[{{class_basename}}::{{method}}] {{message}}',

    /*
    |--------------------------------------------------------------------------
    | Ignore Exceptions
    |--------------------------------------------------------------------------
    |
    | This option determines whether detected exceptions should be ignored
    | for further processing. When set to true, any log message identified as
    | an exception will not include the additional log trace.
    |
    */

    'ignore_exceptions' => true,

];
