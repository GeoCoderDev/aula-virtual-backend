<?php

require __DIR__."/../../middlewares/isSuperadminAuthenticated.php";



Flight::group("/api/admins",  function(){


    Flight::route("POST ", function(){

        Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["Hey"=>"juanito"]));

    });

    Flight::route("GET ", function(){

        echo "Hola superadmin";

    });

},[new SuperadminAuthenticated()]);


