<p align="center">
    <a href="http://koken.me">
        <img src="http://koken.me/img/koken-logo-head.svg" data-png-fallback="http://koken.me/img/koken-logo-head.png" alt="Koken" width="64" height="64">
    </a>
</p>

## About

A <a href="http://koken.me">Koken</a> plugin to easily add an <a href="http://instagram.com">Instagram</a> user's recent images feed.

- <a href="http://koken.me">Koken</a> is a CMS focused on photography
- <a href="http://instagram.com">Instagram</a> is a common photo-sharing application

## Installation

Koken does not have a store for 3rd party plugins, so we need to install it manually.

- <a href="http://koken.me/#dlkoken">Install Koken</a>
- Navigate to the plugins folder:

        cd /storage/plugins

- Git clone or download and unzip this package:
        
        git clone https://github.com/wilsonlewis/koken-instagram-feed.git
        
- Open a new browser window to login to your admin plugins section: 

        http://yoursite.com/admin/#/settings/plugins

- Click the **Enable** button next to this plugin to enable
- Click the **Set up** button to add an Instagram username


## Markup

Add an image feed using an 'instagram' tag in an <a href="http://help.koken.me/customer/portal/articles/632095-text-overview">essay or custom page</a>.


        // using global settings
        <instagram></instagram>
        
        // using optional overrides
        <instagram username="user" images="4"></instagram>
It can also be used in any <a href="http://help.koken.me/customer/portal/articles/828688-lens-templates">.lens template</a>.

## Attributes (optional)

username

- type: string
- value: a username as it appears in an instagram url
- default: null (set globally in plugin settings)

images

- type: integer
- value: the number of images shown
- default: 6 (set globally in plugin settings)


## License

This plugin is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).