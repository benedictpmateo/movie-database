<?php
namespace App\Helpers;

use App\Log;
use Auth;

class AppHelper
{
    public static function logActivity($message) {
        $id = Auth::id();

        $ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        
        $log = new Log();
        $log->user_id = $id;
        $log->message = $message;
        $log->ip_address = $ip;
        $log->user_agent = $user_agent;
        $log->city = !empty($details->city) ? $details->city : null;
        $log->region = !empty($details->region) ? $details->region : null;
        $log->country = !empty($details->country) ? $details->country : null;
        $log->loc = !empty($details->loc) ? $details->loc : null;
        $log->postal = !empty($details->postal) ? $details->postal : null;
        $log->org = !empty($details->org) ? $details->org : null;
        $log->save();
    }

    public static function instance() {
        return new AppHelper();
    }

}