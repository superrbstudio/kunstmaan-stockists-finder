# KunstmaanStockistsFinderBundle

The KunstmaanStockistsFinderBundle is for adding a postcode search, with a list of stockists and their pins on a map.

## Installation

### Step 1: Install the Bundle

```bash
composer require superrb/kunstmaan-stockists-finder
```

### Step 2: Enable the Bundle

Enable the bundle in your `app/AppKernel.php` for your project

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Superrb\KunstmaanStockistsFinderBundle\SuperrbKunstmaanStockistsFinderBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Add the routes

Add the following to your `app/config/routes.yml`

```yml
superrbkunstmaanstockistsfinderbundle_stockist_admin_list:
    resource: @SuperrbKunstmaanStockistsFinderBundle/Controller/StockistAdminListController.php
    type:     annotation
    prefix:   /{_locale}/admin/stockist/
```

Remember to remove the `/{_locale}/` from the admin list route if you are using single language.

### Step 4: Generate Database Tables

You can use Doctrine Migrations or a schema update, it is your choice

```bash
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```
or
```bash
app/console doctrine:schema:update --force
```

### Step 5: Add required config

Turn on the timestampable Doctrine extension in `app/config/config.yml`

```yml
stof_doctrine_extensions:
    orm:
        default:
            timestampable: true
```

## Usage

### Outputting Stockists on the front end

```bash
app/console assets:install --symlink
```

load the new js file - add the following to .groundcontrolrc
```bash
"footer": [
    "web/bundles/superrbkunstmaanstockistsfinder/js/stockists-finder.js",
],
```

add the route
```twig
superrbkunstmaanstockistsfinderbundle_stockists_form:
    pattern: /stockistsFormSubmission
    defaults: { _controller: SuperrbKunstmaanStockistsFinderBundle:StockistsFinder:stockists }
```

Call the js function from your page
```twig
{% block scripts %}
    stockistsMap();
{% endblock %}
```

Make sure the map dimensions are set
```css
#map_wrapper {
    height: 400px;
}
#map_canvas {
    width: 100%;
    height: 100%;
}
```

Add the Search parameters
```yml
    stockistsfindersearchby:          'limit'
    stockistsfindersearchbyvalue:      '4'
```

You can output a list of stockists on the front end simply be rendering a controller action. This could also be added to a page part template to allow more control.

```twig
{{ render_esi(controller('SuperrbKunstmaanStockistsFinderBundle:StockistsFinder:stockists', { 'limit' : 12, 'template' : 'SuperrbKunstmaanStockistsFinderBundle:StockistsFinder:stockists.html.twig' } )) }}
```

## Issues and Troubleshooting

All issues: tech@superrb.com
