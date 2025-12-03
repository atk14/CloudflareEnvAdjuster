<?php
class CloudflareEnvAdjuster {

	static function AdjustEnv(){
		if(isset($GLOBALS["_SERVER"]["_CLOUDFLARE_ENV_TUNER_PASSED"])){
			return;
		}

		$GLOBALS["_SERVER"]["_CLOUDFLARE_ENV_TUNER_PASSED"] = "true";

		if(!isset($GLOBALS["_SERVER"]["HTTP_X_FORWARDED_FOR"])){
			return;
		}

		if(!isset($GLOBALS["_SERVER"]["REMOTE_ADDR"])){
			return;
		}
		$remote_addr = $GLOBALS["_SERVER"]["REMOTE_ADDR"];

		$cloudflare_ips = [
			// https://www.cloudflare.com/ips-v4/
			"173.245.48.0/20",
			"103.21.244.0/22",
			"103.22.200.0/22",
			"103.31.4.0/22",
			"141.101.64.0/18",
			"108.162.192.0/18",
			"190.93.240.0/20",
			"188.114.96.0/20",
			"197.234.240.0/22",
			"198.41.128.0/17",
			"162.158.0.0/15",
			"104.16.0.0/13",
			"104.24.0.0/14",
			"172.64.0.0/13",
			"131.0.72.0/22",

			// https://www.cloudflare.com/ips-v6/
			"2400:cb00::/32",
			"2606:4700::/32",
			"2803:f800::/32",
			"2405:b500::/32",
			"2405:8100::/32",
			"2a06:98c0::/29",
			"2c0f:f248::/32",
		];

		if(!IP::Match($remote_addr,$cloudflare_ips)){
			return;
		}

		$GLOBALS["_SERVER"]["X_CF_REMOTE_ADDR"] = $remote_addr;
		$GLOBALS["_SERVER"]["REMOTE_ADDR"] = $GLOBALS["_SERVER"]["HTTP_X_FORWARDED_FOR"];
		$GLOBALS["_SERVER"]["REQUEST_SCHEME"] = $GLOBALS["_SERVER"]["HTTP_X_FORWARDED_PROTO"]; // "http", "https"
		if($GLOBALS["_SERVER"]["REQUEST_SCHEME"] === "https"){
			$GLOBALS["_SERVER"]["HTTPS"] = "on";
		}else{
			unset($GLOBALS["_SERVER"]["HTTPS"]);
		}
		unset($GLOBALS["_SERVER"]["HTTP_X_FORWARDED_FOR"]);
		unset($GLOBALS["_SERVER"]["HTTP_X_FORWARDED_PROTO"]);
	}
}
