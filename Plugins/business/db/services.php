<?php
$functions = [

  
        // The name of your web service function, as discussed above.
    'local_business_get_all_courses' => [
        // The name of the namespaced class that the function is located in.
        'classname' => 'local_business_courses',
        'methodname' => 'get_all_courses',
        'classpath' => 'local/business/locallib.php',
        // A brief, human-readable, description of the web service function.
        'description' => 'get all courses',

        // Options include read, and write.
        'type'        => 'read',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,

        // An optional list of services where the function will be included.
        'services' => [
            // A standard Moodle install includes one default service:
            // - MOODLE_OFFICIAL_MOBILE_SERVICE.
            // Specifying this service means that your function will be available for
            // use in the Moodle Mobile App.
            MOODLE_OFFICIAL_MOBILE_SERVICE,
        ]
    ],

   
];