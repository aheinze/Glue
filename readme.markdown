# Getting Started #

Glue is a web framework for quickly creating web applications in PHP with minimal effort inspired by sinatra:

    <?php
     
    require_once("glue/glue.php");
     
    glue("route")->get("/", function(){
        echo "Hello world!";
    });
     
    glue("route")->dispatch();


# Routes #

In Glue, a route is an HTTP method paired with an URL matching pattern:

    <?php
     
    glue("route")->get("/", function(){
        // .. show something ..
    });
     
    glue("route")->post("/", function(){
        // .. create something ..
    });
     
    glue("route")->put("/", function(){
        // .. update something ..
    });
     
    glue("route")->delete("/", function(){
        // .. annihilate something ..
    });
     
    glue("route")->get("404", function(){
     
        echo "Uuups, route not found!";
    });

Routes are matched in the order they are defined. The first route that matches the request is invoked.
Route patterns may include named parameters, accessible via the params array:

    <?php
     
    glue("route")->get("/hello/:name", function($params){
        // matches "GET /hello/foo" and "GET /hello/bar"
        // $params["name"] is 'foo' or 'bar'
    });

Route patterns may also include splat (or wildcard) parameters, accessible via the $params[:splat] array.

    <?php
     
    glue("route")->get("/say/*/to/*", function($params){
        // matches /say/hello/to/world
        // $params[":splat"] => array('hello', 'world')
    });

Route matching with Regular Expressions:

    <?php
     
    glue("route")->get("#/hello/([\w]+)#", function($params){
        // matches /hello/mia
        // $params[":captures"] => array('mia')
    });


# Conditions #

Routes may include a variety of matching conditions:

    <?php
     
    glue("route")->get("/ie", function($params){
     
        echo "Only for the IE's ....";
     
    }, strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'));


# Templates #


In general you can utilize any template engine you want. Glue provides a simple template engine:

    <?php
     
    glue("route")->get("/", function(){
     
            $data = array(
                "name"  => 'Frank', 
                "title" => 'Template demo'
            );
     
            echo glue("template")->render("views/view.php with views/layout.php", $data);
    });

views/view.php

    <p>
        Hello <?php echo $name;?>.
    </p>


views/layout.php

    <!DOCTYPE HTML>
    <html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $title;?></title>
    </head>
    <body>
        <?php echo $content_for_layout;?>
    </body>
    </html>


# Database access? Sure! #


A simple database helper provides you access to mysql (more to come).

    <?php
     
    glue("pdo")->addSource("default", array(
          'dns'       => 'mysql:host=localhost;dbname=test',
          'user'      => 'root',
          'password'  => '',
          'options'   => array()
    ));
     
     
    glue("route")->get("/items/ids", function($params){
     
        //get an array of all ids
        $ids = glue("pdo")->src("default")->find(array(
            'table' => 'test'
            'fields' => 'id'
        ));
     
        //...
    });


# You like OO style? Extend GlueBase! #


Define your routes in the comment for your method.

    <?php
     
    require_once("glue/glue.php");
     
     
    class App extends GlueBase {
     
        /**
            @Get /
        */
        public function index() {
            echo "Hello world!";
        }
     
        /**
            @Get /test/:name
        */
        public function test($params) {
            echo $params['name'];
        }
    };
     
    App:run();