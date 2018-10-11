<?php

namespace App\Models;

use App\Models\BaseModel;

class payOrders extends BaseModel
{

    protected $connection = 'mysql_opgroup';

    public function index()
    {
        $this->platArr = $this->getPlatsGamesServers(1);
        $this->gamesArr = $this->getGames2(1);
    }
}
