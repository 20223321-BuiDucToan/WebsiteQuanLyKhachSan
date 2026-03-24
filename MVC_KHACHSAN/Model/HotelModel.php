<?php
class HotelModel
{
    private $rooms = [
        [
            'id' => 1,
            'name' => 'Phòng Deluxe',
            'type' => 'Deluxe',
            'price' => 1200000,
            'status' => 'Trống'
        ],
        [
            'id' => 2,
            'name' => 'Phòng Standard',
            'type' => 'Standard',
            'price' => 800000,
            'status' => 'Đã đặt'
        ]
    ];

    public function getAllRooms()
    {
        return $this->rooms;
    }
}