# Sylius Mautic Plugin

[![GitHub license](https://img.shields.io/github/license/iam-sayco/SyliusMauticPlugin)](https://github.com/iam-sayco/SyliusMauticPlugin)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/iam-sayco/SyliusMauticPlugin/pulls)


**Sylius Mautic Plugin** is a plugin for the **Sylius** e-commerce platform that integrates your website with 
the marketing automation tool **Mautic**. With this integration, you can track user actions on your site in order to run
effective marketing campaigns.


## Installation

1. Install the plugin using Composer:

    ```bash
    composer require iam-sayco/sylius-mautic-plugin
    ```


2. Add the plugin to your `config/bundles.php`:

    ```php
    // config/bundles.php
    return [
        // ...
        Sayco\SyliusMauticPlugin\SaycoSyliusMauticPlugin::class => ['all' => true],
    ];
    ```
   

3. Register the plugin configs in the imports section of your `config/packages/_sylius.yaml`:

    ```yaml
    # config/packages/_sylius.yaml
    imports:
        ...
        - { resource: "@SaycoSyliusMauticPlugin/Resources/config/config.yml" }
    ```


4. Configure the plugin by creating `config/packages/sayco_sylius_mautic.yaml`:

    Nest under the `parameters` key the following configuration:

     ```yaml
     sayco_sylius_mautic.tracking_config:
     track_outbound: 'true'
     track_mailto: 'true'
     track_tel: 'true'
     track_download: 'true'
     track_download_extensions: [ ".pdf", ".zip", ".doc" ]
     ```

    The `tracking_host` parameter should be set to the URL of your Mautic instance.  It is used in the tracking 
    script embedded in your website to send user actions to Mautic.
    The other configuration parameters are used to enable or disable tracking of specific user actions.
   
    ```yaml
    # config/packages/sayco_sylius_mautic_plugin.yaml
    sayco_sylius_mautic.api.auth:
        baseUrl: 'https://your-mautic-instance.com'
        version: 'BasicAuth'
        userName: 'your-api-mautic-username'
        password: 'your-api-mautic-password'

    ```
    The `api.auth` parameter should be set to the authentication details of your Mautic API.
    Currently, only BasicAuth is supported. Create a new user in Mautic and setup the role and permissions for the user.
    The best would be to limited to the API operations only.


5. Install assets:

    This plugin uses the assets required for tracking user actions. To install them, run the following command:

    ```bash
    bin/console assets:install
    ```


## Usage
Just go to your Mautic and view the tracking data you have collected from your Sylius website.


## Roadmap
There are several features that are planned for this plugin, but have not yet been implemented (out of scope for MVP).
If you would like to contribute to this plugin, please consider working on one of these features:

1. Improve documentation.
2. Add tests coverage.
3. Add utility twig functions to easily embed content from Mautic such as forms and dynamic content.
4. Add ability to use Mautic e-mails for Sylius mail notifications.
5. Add ability to authenticate users in Sylius using Mautic OAuth.
6. Improve crosselling/associated products by using Mautic segments.
7. Add promotions conditions/rules based on Mautic segments.

If you have any other ideas for features, please open an issue to discuss them or propose a pull request.


## Contributing

Feel free to contribute to this plugin by opening issues or submitting pull requests. All help is appreciated, 
especially with documentation and testing.


## License

This plugin is released under the MIT License. See the LICENSE file for details.


## Contact
Made with ❤️ to Open Source by Mariusz Andrzejewski - [Sayco](http://saycode.pl).

[![Saycode](http://saycode.pl/sites/default/files/saycode-dark.png)](http://saycode.pl)
