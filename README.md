[![Dependencies Status](https://d2xishtp1ojlk0.cloudfront.net/d/9434281)](http://depending.in/IgorTimoshenko/IMTDataGridBundle)

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
create the grid builder that will be responsible for creation of the grid:

```php
<?php
namespace Acme\PostBundle\Grid\Builder;

use Symfony\Component\Routing\RouterInterface;

use Doctrine\ORM\EntityManager;

use IMT\DataGrid\Builder\AbstractBuilder;
use IMT\DataGrid\Column\Column;
use IMT\DataGrid\DataSource\Doctrine\ORM\DataSource;

class PostBuilder extends AbstractBuilder
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * The constructor method
     *
     * @param RouterInterface $router
     * @param EntityManager   $entityManager
     */
    public function __construct(
        RouterInterface $router,
        EntityManager $entityManager
    ) {
        $this->router        = $router;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritDoc}
     */
    public function buildColumns()
    {
        $this
            ->dataGrid
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
    }

    /**
     * {@inheritDoc}
     */
    public function buildDataSource()
    {
        $queryBuilder = $this
            ->entityManager
            ->createQueryBuilder()
            ->select(
                'p',
                'p.id AS post_id',
                'p.title'
            )
            ->from('AcmePostBundle:Post', 'p');

        $this->dataGrid->setDataSource(new DataSource($queryBuilder));
    }

    /**
     * {@inheritDoc}
     */
    public function buildOptions()
    {
        $this
            ->dataGrid
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
                        ->router
                        ->generate('acme_post_post_get_posts'),
                )
            );
    }
}
```

> An object of the column in the constructor method takes an array of options.
> There are four options: `index`, `label`, `name`, and `template`. The first
> three are required. You can also pass more options, if necessary. For
> instance, if needed pass them to the library on the client-side.

All that is left to do is to get the grid manager in the contoller and build the
grid using by the grid builder:

```php
<?php
namespace Acme\PostBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use IMT\DataGrid\HttpFoundation\JqGridRequest;

use Acme\PostBundle\Grid\Builder\PostBuilder;

class PostController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/posts/", name="acme_post_post_get_posts")
     * @Template("AcmePostBundle:Post:list.html.twig")
     */
    public function getPostsAction(Request $request)
    {
        $dataGrid = $this
            ->getGridManager()
            ->setBuilder(
                new PostBuilder(
                    $this->getRouter(),
                    $this->getDoctrine()->getEntityManager()
                )
            )
            ->buildDataGrid()
            ->getDataGrid();

        if ($request->isXmlHttpRequest()) {
            $dataGrid->bindRequest(new JqGridRequest($request));

            return new Response(
                json_encode($dataGrid->getData()),
                200,
                array(
                    'Content-Type' => 'application/json',
                )
            );
        }

        return array(
            'grid' => $dataGrid->createView(),
        );
    }

    /**
     * @return \IMT\DataGrid\Manager\ManagerInterface
     */
    private function getGridManager()
    {
        return $this->get('imt_data_grid.manager');
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

> Because was specified the `url` option for the grid, after rendering the grid
> on the client-side will be made an additional request to the server to
> retrieve data. So you need to bind with the grid the request that came to the
> server.

Once the construction of the grid using by the grid builder is finished, you
need to call the `createView` method which will create an object of the grid
view. You can pass this object into the template to configure rendering the grid
on the client-side:

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

That is all. You should see on the client-side the grid with information about
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