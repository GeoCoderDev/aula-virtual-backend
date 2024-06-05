
<?php 

    include_once __DIR__."/../../../middlewares/isTeacherAuthenticated.php";
    include_once __DIR__."/../../../middlewares/isNotSQLInjection.php";

    Flight::group("/api/auth/me",function(){
     
        Flight::route("GET ", function(){

        });

        Flight::route("PUT ", function(){

        });

    }, [new NotSQLInjection() ,new TeacherAuthenticated()]);