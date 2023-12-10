<?php

if (!function_exists('is_abeta_punchout_user')) {
    /**
     * Check if user is logged in via the Abeta PunchOut Middleware
     *
     * @return bool
     */
    function is_abeta_punchout_user()
    {
    	return \AbetaIO\Laravel\AbetaPunchOut::isPunchoutUser();
    }
}
