CloudflareEnvAdjuster
=====================

[![Build Status](https://app.travis-ci.com/atk14/CloudflareEnvAdjuster.svg?token=Kc7UxgK5oqFG8sZAhCzg&branch=master)](https://app.travis-ci.com/atk14/CloudflareEnvAdjuster)

Adjust the environment of a web app behind Cloudflare protection.

Basically if CloudflareEnvAdjuster detects that the remote address in the current request is from one of Cloudflare's ranges, it transparently replaces `$_SERVER["REMOTE_ADDR"]` with `$_SERVER["HTTP_X_FORWARDED_FOR"]` and stores the original REMOTE_ADDR into `$_SERVER["X_CF_REMOTE_ADDR"]`.

Usage in an ATK14 application
-----------------------------

    <?php
    // file: config/after_initialize.php
    CloudflareEnvAdjuster::AdjustEnv();

Usage in a non-ATK14 application
--------------------------------

Place the call to `CloudflareEnvAdjuster::AdjustEnv()` as close to the beginning of your PHP script as you can.

Installation
------------

Just use the Composer:

    composer require atk14/cloudflare-env-adjuster

License
-------

CloudflareEnvAdjuster is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
    

      
