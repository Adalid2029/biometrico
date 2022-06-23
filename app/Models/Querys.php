<?php

namespace App\Models;

use CodeIgniter\Database\Database;

class Querys extends Database
{
    public $db = null;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function selectTable($tabla, $campos = '*', $condicion = [], $orden = '', $agrupar = '')
    {
        $builder = $this->db->table($tabla);
        $builder->select($campos);
        empty($orden) ?: $builder->orderBy($orden);
        empty($agrupar) ?: $builder->groupBy($agrupar);

        // return var_dump($builder->getCompiledSelect());

        return empty($condicion) ? $builder->get() : $builder->getWhere($condicion);
    }
    public function insertTable($tabla, $datos)
    {
        $builder = $this->db->table($tabla);
        return $builder->insert($datos) ? $this->db->insertID() : $this->db->error();
    }
    public function updateTable($table, $data, $condition)
    {
        $builder = $this->db->table($table);
        return $builder->update($data, $condition);
    }
    public function deleteTable($table, $condition = [])
    {
        return $this->db->table($table)->delete($condition);
    }

    public function updateIfExistOrInsert($table, $condicion, $data = [])
    {
        $exist = $this->selectTable($table, '*', $condicion)->getRowArray();
        if (is_null($exist)) {
            return $this->insertTable($table, $data);
        } else {
            return $this->updateTable($table, $data, $condicion);
        }
    }
    function searchGlobal($table, $condition, $columns, $groupBy = '', $orderyBy = '', $limit = 0, $offset = 0)
    {
        $builder = $this->db->table($table);
        $builder->select($columns);
        empty($groupBy) ?: $builder->groupBy($groupBy);
        empty($orderyBy) ?: $builder->orderBy($orderyBy);
        empty($limit) ?: $builder->limit($limit);
        empty($offset) ?: $builder->offset($offset);
        foreach ($condition as  $value) {

            if (isset($value['divide']) && $value['divide']) {
                $columns = [];
                foreach (explode(isset($value['explode']) ? $value['explode'] : " ", $value['dato']) as $v) {
                    if (!empty($v)) $columns[] = $v;
                }
                switch ($value['expression']) {
                    case 'groupStart':
                        $builder->groupStart();
                        break;
                    case 'orGroupStart':
                        $builder->orGroupStart();
                        break;
                    case 'notGroupStart':
                        $builder->notGroupStart();
                        break;
                    case 'orNotGroupStart':
                        $builder->orNotGroupStart();
                        break;
                    default:
                        $builder->groupStart();
                        break;
                }
                foreach ($this->AllPermutations($columns) as $u) {
                    $builder->orLike("CONCAT(" . implode(",' ',", $value['columns']) . ")", join(isset($value['join']) ? $value['join'] : ' ', $u), isset($value['match']) ? $value['match'] : 'both', isset($value['escape']) ? $value['escape'] : false);
                }
                $builder->groupEnd();
            } else {
                foreach (isset($value['columns']) ?  $value['columns'] : [] as $val) {
                    switch ($value['expression']) {
                        case 'like':
                            $builder->like($val . '::text', $value['dato'], 'both', false);
                            break;
                        case 'orLike':
                            $builder->orLike($val . '::text', $value['dato'], 'both', false);
                            break;
                        case 'notLike':
                            $builder->notLike($val . '::text', $value['dato'], 'both', false);
                            break;
                        case 'orNotLike':
                            $builder->orNotLike($val . '::text', $value['dato'], 'both', false);
                            break;
                        case 'where':
                            $builder->where([$val => $value['dato']]);
                            break;
                        case 'whereIn':
                            $builder->whereIn($val, $value['dato']);
                            break;
                        default:
                            $builder->like($val . '::text', $value['dato'], 'both', false);
                            break;
                    }
                }
            }
        }
        // $this->db->get();
        // return var_dump($this->db->last_query());
        return $builder->get()->getResultArray();
    }
    function AllPermutations($InArray, $InProcessedArray = array())
    {
        $ReturnArray = array();
        foreach ($InArray as $Key => $value) {
            $CopyArray = $InProcessedArray;
            $CopyArray[$Key] = $value;
            $TempArray = array_diff_key($InArray, $CopyArray);
            if (count($TempArray) == 0) {
                $ReturnArray[] = $CopyArray;
            } else {
                $ReturnArray = array_merge($ReturnArray, $this->AllPermutations($TempArray, $CopyArray));
            }
        }
        return $ReturnArray;
    }
}
