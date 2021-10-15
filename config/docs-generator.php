<?php

return [
    'annotations_path' => app_path() . '/Swagger',
    'actions_directory' => 'Actions',
    'schemas_directory' => 'Schemas',
    'fortify_endpoints' => [
        'Authentication/CsrfCookie',
        'Authentication/Login',
        'Authentication/Logout',
        'Password/ForgetPassword',
        'Password/ResetPassword',
        'Register/Register',
        'Profile/Show',
        'Profile/Update',
        'Profile/ConfirmPassword',
        'Profile/PasswordConfirmationStatus',
        'Profile/UpdatePassword',
    ],
];
