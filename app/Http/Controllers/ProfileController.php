<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Http\Helpers\ProfileHelper as Helper;

class ProfileController extends Controller
{

    public function getProfileByParams(Request $request) {
        try {
            $profileHelper  = new Helper();
            $params         = $request->all();

            $selectData     = $params['select'];
            $limit          = $params['limit'];
            $conditionsData = $params['conditions'];
            $currentPage    = $params['current_page'];
            $orderData      = $params['order'];

            $getData        = $profileHelper->getDataByParams($selectData, $limit, $conditionsData, $currentPage, $orderData);
            if ($getData['status'] != 1)
                throw new Exception($getData['message']);

            $jsonConvert    = $this->jsonConverter($getData);
            if ($jsonConvert['status'] != 1)
                throw new Exception($jsonConvert['message']);

            return $jsonConvert['data'];

        } catch (Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => $e->getMessage()
                ], 201);
        }
    }

}
