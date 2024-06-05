
<?php 

    include_once __DIR__."/../../../middlewares/isAdminAuthenticated.php";
    include_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

    Flight::group("/api/auth/me",function(){
     
        Flight::route("GET ", function(){

        });

        Flight::route("PUT ", function(){

        });

    }, [new NotSQLInjection() ,new AdminAuthenticated()]);