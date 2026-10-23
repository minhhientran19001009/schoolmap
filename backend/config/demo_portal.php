<?php

return [
    'enabled' => (bool) env(
        'DEMO_PORTAL_ENABLED',
        env('APP_ENV', 'production') === 'local',
    ),
];
