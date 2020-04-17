<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function jsonConverter($data) {
        if (empty($data)) {
            return [
                'status'    => 0,
                'message'   => response()->json($data['message'])
            ];
        }

        return [
            'status'    => 1,
            'json'      => response()->json($data)
        ];
    }

}
