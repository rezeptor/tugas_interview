<?php
/**
 * Created by PhpStorm.
 * User: Rezeptor
 * Date: 15/04/2020
 * Time: 22:34
 */

namespace App\Http\Models;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Fetcher
{

    public function fetchProfile($query, $selectData = false, $limit = false, $conditionsData = false, $currentPage = false, $orderData = false) {
        try{

            $selectData  = substr($selectData, 1);
            $selectData  = substr($selectData, 0, -2);

            if ($selectData) {
                $query->select($selectData);
            }

            $conditionsData  = substr($conditionsData, 1);
            $conditionsData  = substr($conditionsData, 0, -1);
            $conditionsData  = str_replace('=>', ':', str_replace("'", '"',$conditionsData));
            $conditionsData  = json_encode($conditionsData, JSON_UNESCAPED_SLASHES);
            $conditionsData  = str_replace('\n', '', $conditionsData);
            $conditionsData  = urldecode(stripslashes($conditionsData));
            $conditionsData  = substr($conditionsData, 1);
            $conditionsData  = '{' . substr($conditionsData, 0, -1) . '}';
            $conditionsData  = json_decode($conditionsData);

            if (isset($conditionsData)) {
                foreach ($conditionsData as $conditions) {
                    $queryCondition = $this->getConditionQuery($query, $conditions);
                    $query          = $queryCondition['data'];
                }
            }

            $orderData  = substr($orderData, 1);
            $orderData  = substr($orderData, 0, -1);
            $orderData  = str_replace('[', '{', str_replace('=>', ':', str_replace(']', '}', str_replace("'", '"',$orderData))));
            $orderData  = json_encode($orderData, JSON_UNESCAPED_SLASHES);
            $orderData  = str_replace('\n', '', $orderData);
            $orderData  = urldecode(stripslashes($orderData));
            $orderData  = substr($orderData, 1);
            $orderData  = '[' . substr($orderData, 0, -1) . ']';
            $orderData  = json_decode($orderData);

            $newOrder   = array();
            foreach ($orderData as $item) {
                $newOrder[]    = [
                    'field' => $item->field,
                    'order' => $item->order
                ];
            }

            if (isset($newOrder)) {
                foreach ($newOrder as $orderVal) {
                    $field      = str_replace("'", '', $orderVal['field']);
                    $order      = str_replace("'", '', $orderVal['order']);
                    $query->orderBy($field, $order);
                }
            }

            $get    = $query->get()->toArray();
            if(empty($get))
                throw new Exception("Record not found");

            $count  = $query->count();

            $data   = [
                "sql"       => $query->toSql(),
                "limit"     => $limit,
                "totalRow"  => $count,
                "data"      => $get
            ];

            return [
                "status"    => 1,
                "data"      => $data
            ];

        } catch (Exception $e) {
            return [
                'status'    => 0,
                'message'   => $e->getMessage()
            ];
        }
    }

    public function getConditionQuery($query, $condition)
    {

        try {
            $type = $condition['type'];
            $data = $condition['data'];

            if (isset($condition['function'])) {
                $function = $condition['function'];
            }

            switch ($type) {
                case 'whereColumn':
                    $query->whereColumn($data);
                    break;

                case 'where':
                    $query->where($data);
                    break;

                case 'orWhere':
                    $query->orWhere($data[0], $data[1], $data[2]);
                    break;


                case 'whereNull':
                    $query->whereNull($data[0]);
                    break;

                case 'whereNotNull':
                    $query->whereNotNull($data[0]);
                    break;

                case 'whereIn':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->whereIn($tableName, $tableValue);
                    break;

                case 'orWhereIn':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->orWhereIn($tableName, $tableValue);
                    break;

                case 'whereNotIn':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->whereNotIn($tableName, $tableValue);
                    break;

                case 'OrWhereNotIn':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->OrwhereNotIn($tableName, $tableValue);
                    break;


                case 'whereNotBetween':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->whereNotBetween($tableName, $tableValue);
                    break;

                case 'whereBetween':
                    $tableName  = $data[0];
                    $tableValue = $data[1];
                    $query->whereBetween($tableName, $tableValue);
                    break;
            }

            if (isset($function)) {
                foreach ($function as $func) {
                    $subFunction = $this->getConditionQuery($query, $func);
                }
                $query->$type(function ($subFunction) {
                    $subFunction;
                });
            }

            return [
                "status"    => 1,
                "data"      => $query
            ];

        } catch (Exception $e) {
            return [
                'status'    => 0,
                'message'   => $e->getMessage()
            ];
        }
    }

}