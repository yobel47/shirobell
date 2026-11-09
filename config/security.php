<?php

return [

    /*
    | Content-Security-Policy header toggle. Keep enabled in every environment;
    | disable only temporarily to debug a broken third-party embed.
    */
    'csp_enabled' => (bool) env('SECURITY_CSP_ENABLED', true),

];
