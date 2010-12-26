<?php
    
    require_once(__DIR__.'/vendor/glue/glue.php');


    // routes

    glue("route")->get("/", function(){
        
        echo "Hello world"
    });
    
    //--
    
    glue("route")->get("404", function(){

        echo "Uuups...";
    });
    
    
    glue("route")->dispatch();