<?php
/**
 * Created by PhpStorm.
 * User: Rezeptor
 * Date: 15/04/2020
 * Time: 22:27
 */

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Fetcher;
use Exception;

class Profile extends Model
{

    private $tbl = 'tbl_profile';

    public function getProfileByParams($query, $selectData, $limit, $conditionsData, $currentPage, $orderData) {
        try {
            $modelF     = new Fetcher();
            $fetchData  = $modelF->fetchProfile($query, $selectData, $limit, $conditionsData, $currentPage, $orderData);
            if ($fetchData['status'] != 1)
                throw new Exception($fetchData['message']);

            $dataVal    = $fetchData['data'];
            $totalData  = $dataVal['totalRow'];
            $totalPages = ceil($totalData / $limit);
            $data       = $fetchData['data'];
            $splitData  = array_chunk($data, $limit);
            $arrayPage  = $currentPage - 1;
            $totalRow   = $splitData[$arrayPage];
            $data       = $splitData[$arrayPage];

            $result     = [
                "limit"         => $limit,
                "total_row"     => count($totalRow),
                "total_data"    => $totalData,
                "total_page"    => $totalPages,
                "current_page"  => $currentPage,
                "data"          => $data
            ];

            return [
                'status'    => 1,
                'data'      => $result
            ];

        } catch (Exception $e) {
            return [
                'status'    => 0,
                'message'   => $e->getMessage()
            ];
        }
    }

}

