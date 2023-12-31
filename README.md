<pre>
  ┌───────┐
  │       │
  │  a:o  │  acolono.com
  │       │
  └───────┘
</pre>

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Features
 * Installation
 * Configuration
 * Usage


INTRODUCTION
------------
This module checks GET and HTTP Request parameters (like utm_source, utm_medium, HTTP_REFERER) from anonymous visitors, and saves those in a cookie for further processing.

When the visitor is going further on your website and is taking some actions like doing a purchase this information can be used for analytics. It makes initial paramaters persisistent for a session.

FEATURES
------------
* Can read `GET` and `HTTP Request` parameters, and store them into cookie
* Cookie lifetime is configurable (current session, custom, forever)
* (configurable) Respects browser `DNT` Setting (Do-Not-Track)
* Other modules can easily read cookie data using `persistent_visitor_parameters.cookie_manager` service

INSTALLATION
------------

* Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.

CONFIGURATION
-------------
* Configuration page is located here: `admin/config/persistent-visitor-parameters`
* Configure parameters you would like to track, for example `utm_source`, `utm_medium` (GET) or `HTTP_REFERER` (HTTP Request) parameters

To modify the cookie options, such as name or domain, you can override the `persistent_visitor_parameters.options` service parameter. For example, in `sites/default/services.yml` or `sites/development.services.yml`:

```
parameters:
  persistent_visitor_parameters.options:
    cookie_name: pvp_stored_variables
    cookie_domain: .example.com
```

USAGE
-------------
* Read already saved cookies using `\Drupal::service('persistent_visitor_parameters.cookie_manager')->getCookie()` inside your module, and process this data further as you need it

by acolono GmbH
---------------

~~we build your websites~~
we build your business

hello@acolono.com

www.acolono.com
www.twitter.com/acolono
www.drupal.org/acolono-gmbh