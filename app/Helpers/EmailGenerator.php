<?php
namespace App\Helpers;
class EmailGenerator{
    public static function generateEmailDomain(string $nis) {
        $domain = env("EMAIL_DOMAIN");
        if(!$domain) {
            $domain = "example.com";
        }
        return sprintf("%s@%s",$nis,$domain);
    }
}