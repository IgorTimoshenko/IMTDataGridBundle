[![Build Status](https://travis-ci.org/IgorTimoshenko/IMTDataGridBundle.png?branch=master)](https://travis-ci.org/IgorTimoshenko/IMTDataGridBundle)
[![Coverage Status](https://coveralls.io/repos/IgorTimoshenko/IMTDataGridBundle/badge.png?branch=master)](https://coveralls.io/r/IgorTimoshenko/IMTDataGridBundle)

# IMTDataGridBundle #

## Overview ##

This bundle provides integration with [IMTDataGrid][1]. The main purpose of this
library is to provide a simple, powerful and fully customizable tool for binding
grids on the client-side with data on the server. The library does not provide
any tools for rendering grids on the client-side. So you pick yourself library
that will render grids on the client-side (one of the such libraries is
[jqGrid][2]). However, the library provides the opportunity to create an object
of the grid, which contains the name, options, and a collection of columns.
Therefore, you can use this object in order to simplify rendering grids on the
client-side.

## Installation ##

### 1. Using Composer (recommended) ###

To install `IMTDataGridBundle` with [Composer][3] just add the following to
your `composer.json` file:

```json
{
    // ...
    "require": {
        // ...
        "imt/data-grid-bundle": "dev-master"
        // ...
    }
    // ...
}
```

Then, you can install the new dependencies by running [Composer][3]'s update
command from the directory where your `composer.json` file is located:

```sh
$ php composer.phar update imt/data-grid-bundle
```

Now, [Composer][3] will automatically download all required files, and install
them for you. All that is left to do is to update your `AppKernel.php` file, and
register the new bundle:

```php
<?php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new IMT\DataGridBundle\IMTDataGridBundle(),
    // ...
);
```

## Usage ##

Suppose you are building a simple blog and you need to have the grid on the
back-end, which will display information about the posts.

> It is further assumed that you are going to use the [jqGrid][2] library for
> rendering grids on the client-side, as well as [Doctrine ORM][4] as the data
> source.

To get started, you need to construct an object that will reflect the grid with
information about the posts. In order to construct this object you need to
create a new object of the grid using by the grid factory and setting the name,
options, and a collection of columns on the newly created object:

```php
<?php
namespace Acme\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use IMT\DataGrid\Column\Column;

class PostController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/posts/", name="acme_post_post_get_posts")
     * @Template("AcmePostBundle:Post:list.html.twig")
     */
    public function getPostsAction()
    {
        $grid = $this
            ->getDataGridFactory()
            ->create()
            ->setName('posts')
            ->setOptions(
                array(
                    'caption'  => 'Posts',
                    'datatype' => 'json',
                    'mtype'    => 'get',
                    'pager'    => '#posts_pager',
                    'rowList'  => array(
                        10,
                        20,
                        30,
                    ),
                    'rowNum'   => 10,
                    'url'      => $this
                        ->getRouter()
                        ->generate('acme_post_post_get_posts'),
                )
            )
            ->addColumn(
                new Column(
                    array(
                        'index' => 'p.id',
                        'label' => 'Id',
                        'name'  => 'post_id',
                    )
                )
            )
            ->addColumn(
                new Column(
                    array(
                        'index' => 'p.title',
                        'label' => 'Title',
                        'name'  => 'title',
                    )
                )
            );

        return array(
            'grid' => $grid->createView(),
        );
    }

    /**
     * @return \IMT\DataGrid\Factory\FactoryInterface
     */
    private function getDataGridFactory()
    {
        return $this->get('imt_data_grid.factory');
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    private function getRouter()
    {
        return $this->get('router');
    }
}
```

An object of the column in the constructor method takes an array of options.
There are four options: `index`, `label`, `name`, `template`. The first three
are required. You can also pass more options, if necessary. For instance, if
needed pass them to the library on the client-side.

Once the construction of the object that reflects the grid is finished, you need
to call the `createView` method which will create an object of the grid view.
You can pass this object into the template to configure rendering the grid on
the client-side:

```html
{# in AcmePostBundle:Post:list.html.twig #}
{% extends '::base.html.twig' %}

{% block stylesheets %}
    {{ parent() }}

    {% stylesheets
        filter='?cssrewrite,?yui_css'
        output='css/jqGrid.css'
        '@AcmePostBundle/Resources/public/js/lib/jqGrid/4.4.5/css/ui.jqgrid.css'
    %}
        <link rel="stylesheet" href="{{ asset_url }}" />
    {% endstylesheets %}
{% endblock %}

{% block body %}
    <table id="{{ grid.getName }}"><tr><td/></tr></table>
    <div id="{{ grid.getName }}_pager"></div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}

    {% javascripts
         filter='?yui_js'
         output='js/jqGrid.js'
        '@AcmePostBundle/Resources/public/js/lib/jqGrid/4.4.5/js/i18n/grid.locale-en.js'
        '@AcmePostBundle/Resources/public/js/lib/jqGrid/4.4.5/js/jquery.jqGrid.min.js'
    %}
        <script src="{{ asset_url }}"></script>
    {% endjavascripts %}

    <script>
        {% set colModel = [] %}
        {% set colNames = [] %}
        {% for column in grid.getColumns %}
            {% set colModel = colModel|merge([column.toArray]) %}
            {% set colNames = colNames|merge([column.get('label')]) %}
        {% endfor %}

        {% set options = grid.getOptions %}
        {% set options = options|merge({'colModel':colModel}) %}
        {% set options = options|merge({'colNames':colNames}) %}

        $(function(){
            $('#' + '{{ grid.getName }}').jqGrid({{ options|json_encode|raw }});
            $('#' + '{{ grid.getName }}').jqGrid('filterToolbar',{stringResult:true});
            $('#' + '{{ grid.getName }}').jqGrid('navGrid','{{ '#' ~ grid.getName ~ '_pager' }}');
        });
    </script>
{% endblock %}
```

Because we specified the `url` option during the construction of the object that
reflects the grid, after rendering the grid on the client-side will be made
an additional request to the server to retrieve data. You need to pass the data
source into the object that reflects the grid and also bind with it the request
that came to the server. All that is left to do is to get data from the data
source and return them in the required data format:

```php
<?php
namespace Acme\PostBundle\Controller;

// ...
use IMT\DataGrid\DataSource\Doctrine\ORM\DataSource;
use IMT\DataGrid\HttpFoundation\JqGridRequest;
// ...
// in the `getPostsAction` method before getting the grid view
if ($this->getRequest()->isXmlHttpRequest()) {
    $queryBuilder = $this
        ->getDoctrine()
        ->getEntityManager()
        ->createQueryBuilder()
        ->select(
            'p',
            'p.id AS post_id',
            'p.title'
        )
        ->from('AcmePostBundle:Post', 'p');

    $grid
        ->setDataSource(new DataSource($queryBuilder))
        ->bindRequest(new JqGridRequest($this->getRequest()));

    return new Response(
        json_encode($grid->getData()),
        200,
        array(
            'Content-Type' => 'application/json',
        )
    );
}
```

As a result, you should see on the client-side the grid with information about
the posts. As you can see, to create the grid that is bound with data on the
server and with the ability to search is very easy.

## License ##

This bundle is released under the MIT license. See the complete license in the
bundle:

    Resources/meta/LICENSE

[1]: http://github.com/IgorTimoshenko/IMTDataGrid
[2]: http://github.com/tonytomov/jqGrid
[3]: http://getcomposer.org
[4]: http://github.com/doctrine/doctrine2