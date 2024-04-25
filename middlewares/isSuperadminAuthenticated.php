<?php



class SuperadminAuthenticated {

    public function __construct() {

    }

    public function before($params) {
        Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["usuariooooo_id"=>2345]));
    }

    public function after($params) {
        Flight::request()->data->setData(array_merge(Flight::request()->data->getData(),["password"=>23464644545]));
        Flight::json(Flight::request()->data->getData(), 200);
    }
}
