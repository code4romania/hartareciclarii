<?php

declare(strict_types=1);

return [

    // Number of registration attempts allowed per minute
    'register' => (int) env('THROTTLE_REGISTER_PER_MINUTE', 10),

    // Number of login attempts allowed per minute
    'login' => (int) env('THROTTLE_LOGIN_PER_MINUTE', 5),

    // Number of forgot-password attempts allowed per minute
    'forgot-password' => (int) env('THROTTLE_FORGOT_PASSWORD_PER_MINUTE', 5),

    // Number of reset-password attempts allowed per minute
    'reset-password' => (int) env('THROTTLE_RESET_PASSWORD_PER_MINUTE', 10),

    // Number of map interactions allowed per minute
    'map' => (int) env('THROTTLE_MAP_PER_MINUTE', 120),

    // Number of new point or report attempts allowed per minute
    'submit' => (int) env('THROTTLE_SUBMIT_PER_MINUTE', 30),

    // Number of media uploads or deletions allowed per minute
    'media' => (int) env('THROTTLE_MEDIA_PER_MINUTE', 30),

];
