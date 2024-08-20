<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Test\Mock;

use Config\App;

class MockCLIConfig extends App
{
    public string $baseURL          = 'http://example.com/';
    public string $uriProtocol      = 'REQUEST_URI';
    public $cookiePrefix     = '';
    public $cookieDomain     = '';
    public $cookiePath       = '/';
    public $cookieSecure     = false;
    public $cookieHTTPOnly   = false;
    public $cookieSameSite   = 'Lax';
    public $proxyIPs         = '';
    public $CSRFTokenName    = 'csrf_test_name';
    public $CSRFCookieName   = 'csrf_cookie_name';
    public $CSRFExpire       = 7200;
    public $CSRFRegenerate   = true;
    public $CSRFExcludeURIs  = ['http://example.com'];
    public $CSRFSameSite     = 'Lax';
    public $CSPEnabled       = false;
    public string $defaultLocale    = 'en';
    public $negotiateLocale  = false;
    public $supportedLocales = [
        'en',
        'es',
    ];
}
