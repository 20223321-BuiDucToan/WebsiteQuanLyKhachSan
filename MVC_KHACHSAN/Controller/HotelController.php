<?php
require_once __DIR__ . '/../Model/HotelModel.php';

class HotelController
{
    private $model;

    public function __construct()
    {
        $this->model = new HotelModel();
    }

    public function index()
    {
        $rooms = $this->model->getAllRooms();
        require __DIR__ . '/../View/hotelView.php';
    }
}