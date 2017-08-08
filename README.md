<p align="center"><img width="293" height="117" src="logo.png"></p>

## What is Laravel Vue Blade Directive?

Originally inspired by [Faking Server-Side Rendering With Vue.js and Laravel by Anthony Gore](https://vuejsdevelopers.com/2017/04/09/vue-laravel-fake-server-side-rendering/), Laravel Vue Blade Directive package is meant to provide tools to build static PHP and Vue Templates in tandem.

This is not meant to replace a full SSR application, but to facilitate jankless Vue Components within Blade Templates. For example, a dynamically updated sidebar component, that is statically rendered by PHP on first load.

The goal instead is to be capable of writing a single Component in a blade file, include it with `@vueComponent(sidebar)` and have it dynamically produce both the static PHP, and the vuejs template. This will then cleanly hydrate on the Vue instance with no jank.


## Proof Of Concept Demo

[Original POC Repo](https://github.com/unr/laravel-vue-hydrate)

![Example Of Jankless Vue Component](https://camo.githubusercontent.com/d217ca1d6120a7adc217027bb4f38e948eba237c/687474703a2f2f756e722e696d2f3244315932773048316e33722f636f6e74656e74)

## Installation

You can install the package via composer:

```bash
composer require lootmarket/laravel-vue-blade-component
```

### Provider

Then add the ServiceProvider to your `config/app.php` file:

```php
'providers' => [
    ...

    LootMarket\VueComponent\VueComponentServiceProvider::class

    ...
];
```


## How to build a Jankless Vue Component in Blade Templates

> Coming soon, I swear.

`@vueComponent(component-path)`

`@vue(jsVariable, $phpVariable)`

`@v(jsVariableString)`

## About LootMarket

LootMarket is a development team based in Toronto, Ontario focused on creating great experiences around esports. In our quest for the ultimate PHP & Vue experience, we've created this.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
