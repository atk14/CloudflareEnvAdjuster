<?php
class TcCloudflareEnvAdjuster extends TcBase {

	function test(){
		global $HTTP_REQUEST;

		$_SERVER = &$GLOBALS["_SERVER"];
		$_SERVER_ORIG = $_SERVER;

		// volani CloudflareEnvAdjuster::AdjustEnv() musi mit vliv i na globalni $HTTP_REQUEST
		myAssert(!!$HTTP_REQUEST,'$HTTP_REQUEST must exist');

		// IPv4

		$_SERVER = [
			"HTTP_X_FORWARDED_FOR" => "217.28.84.78",
			"REMOTE_ADDR" => "172.68.213.32",
			"HTTP_X_FORWARDED_PROTO" => "https",
		];

		$this->assertEquals("172.68.213.32",$HTTP_REQUEST->getRemoteAddr());

		CloudflareEnvAdjuster::AdjustEnv();

		$this->assertEquals("217.28.84.78",$_SERVER["REMOTE_ADDR"]);
		$this->assertEquals("172.68.213.32",$_SERVER["X_CF_REMOTE_ADDR"]);
		$this->assertEquals("https",$_SERVER["REQUEST_SCHEME"]);
		$this->assertEquals("on",$_SERVER["HTTPS"]);
		$this->assertTrue(!isset($_SERVER["HTTP_X_FORWARDED_FOR"]));
		$this->assertTrue(!isset($_SERVER["HTTP_X_FORWARDED_PROTO"]));
		$this->assertEquals("true",$GLOBALS["_SERVER"]["_CLOUDFLARE_ENV_TUNER_PASSED"]);

		$request = new HTTPRequest();
		$this->assertEquals("217.28.84.78",$request->getRemoteAddr());
		$this->assertEquals(true,$request->ssl());

		$this->assertEquals("217.28.84.78",$HTTP_REQUEST->getRemoteAddr());
		$this->assertEquals(true,$HTTP_REQUEST->ssl());

		// IPV6

		$_SERVER = [
			"HTTP_X_FORWARDED_FOR" => "2a01:5f0:c001:119:216:3eff:fe01:36e",
			"REMOTE_ADDR" => "172.71.15.44",
			"HTTP_X_FORWARDED_PROTO" => "http",
		];

		CloudflareEnvAdjuster::AdjustEnv();

		$this->assertEquals("2a01:5f0:c001:119:216:3eff:fe01:36e",$_SERVER["REMOTE_ADDR"]);
		$this->assertEquals("172.71.15.44",$_SERVER["X_CF_REMOTE_ADDR"]);
		$this->assertEquals("http",$_SERVER["REQUEST_SCHEME"]);
		$this->assertTrue(!isset($_SERVER["HTTPS"]));
		$this->assertTrue(!isset($_SERVER["HTTP_X_FORWARDED_FOR"]));
		$this->assertTrue(!isset($_SERVER["HTTP_X_FORWARDED_PROTO"]));

		$request = new HTTPRequest();
		$this->assertEquals("2a01:5f0:c001:119:216:3eff:fe01:36e",$request->getRemoteAddr());
		$this->assertEquals(false,$request->ssl());

		$this->assertEquals("2a01:5f0:c001:119:216:3eff:fe01:36e",$HTTP_REQUEST->getRemoteAddr());
		$this->assertEquals(false,$HTTP_REQUEST->ssl());

		// non-cloudflare IP

		$_SERVER = [
			"HTTP_X_FORWARDED_FOR" => "217.28.84.78",
			"REMOTE_ADDR" => "127.0.0.1",
			"HTTP_X_FORWARDED_PROTO" => "https",
		];

		CloudflareEnvAdjuster::AdjustEnv();

		$this->assertEquals("127.0.0.1",$_SERVER["REMOTE_ADDR"]);
		$this->assertEquals("217.28.84.78",$_SERVER["HTTP_X_FORWARDED_FOR"]);
		$this->assertTrue(!isset($_SERVER["X_CF_REMOTE_ADDR"]));

		$request = new HTTPRequest();
		$this->assertEquals("127.0.0.1",$request->getRemoteAddr());
		$this->assertEquals(true,$request->ssl()); // ??

		$this->assertEquals("127.0.0.1",$HTTP_REQUEST->getRemoteAddr());
		$this->assertEquals(true,$HTTP_REQUEST->ssl());

		$_SERVER = $_SERVER_ORIG;
	}
}
