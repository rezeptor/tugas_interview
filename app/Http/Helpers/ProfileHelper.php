<?php
/**
 * Created by PhpStorm.
 * User: Rezeptor
 * Date: 15/04/2020
 * Time: 21:11
 */

namespace App\Http\Helpers;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Profile as Model;

class ProfileHelper
{

    public function getDataByParams($selectData, $limit, $conditionsData, $currentPage, $orderData) {
        try {
            $profileM       = new Model();
            $query          = DB::table('tbl_profile');
            if (empty($query))
                throw new Exception("Query for target table cannot be empty");

            $getData        = $profileM->getProfileByParams($query, $selectData, $limit, $conditionsData, $currentPage, $orderData);

            if ($getData['status'] != 1)
                throw new Exception($getData['message']);

            return [
                'status'    => 1,
                'data'      => $getData['data']
            ];

        } catch (Exception $e) {
            return [
                'status'    => 0,
                'message'   => $e->getMessage()
            ];
        }
    }

}