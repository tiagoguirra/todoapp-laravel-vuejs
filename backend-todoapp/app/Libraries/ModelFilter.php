<?php
namespace App\Libraries;

use Auth;
use App\Libraries\ModelForApi;
use Carbon\Carbon;

class ModelFilter
{

    protected $model;
    protected $queryBuilder;
    protected $query;

    public function __construct($model, $query)
    {
        $this->model = $model;
        $this->queryBuilder = $model;
        $this->query = $query;
        return $this;
    }

    public static function setFilter($model, $query)
    {
        return new self($model, $query);
    }

    public function applySearch($param)
    {
        if (isset($this->query['search'])) {
            if (is_array($param)) {
                $querySearch = "(";
                $paramLenght = count($param);
                $i = 0;

                while ($i < $paramLenght) {
                    if ($i > 0) {
                        $querySearch .= " OR ";
                    }
                    $querySearch .= $param[$i] . " LIKE '%" . $this->query['search'] . "%'";
                    $i++;
                }
                $querySearch .= ")";
                $this->queryBuilder = $this->queryBuilder->whereRaw($querySearch);
            } else {
                $this->queryBuilder = $this->queryBuilder->where($param, 'LIKE', $this->query['search']);
            }
        }
        return $this;
    }
    public function applyStatus($param)
    {
        if (isset($this->query['status'])) {
            $status = $this->queryToStatus($this->query['status']);
            if ($status) {                
                $this->queryBuilder = $this->queryBuilder->whereIn($param,$status);
            }
        }
        return $this;
    }

    public function applyPriority($param)
    {
        if (isset($this->query['priority'])) {
            $status = $this->queryToStatus($this->query['priority']);
            if ($status) {                
                $this->queryBuilder = $this->queryBuilder->whereIn($param,$status);
            }
        }
        return $this;
    }

    public function applyShow($includeToShow = [])
    {
        if (isset($this->query['show_only'])) {
            $show_only = $this->queryToSelect($this->query['show_only'], $this->model->prefix,$this->model->getTable());                        
            $show_only = array_merge($show_only, $includeToShow);            
            $this->queryBuilder = $this->queryBuilder->select($show_only);
        }else{
            $select = array_merge([$this->model->getTable().'.*'],$includeToShow);
            $this->queryBuilder = $this->queryBuilder->select($select);
        }
        return $this;
    }

    public function applyOrder($notPrefix = [])
    {
        if(isset($this->query['order'])){
            $order = $this->queryToOrderBy($this->query['order'], isset($this->model->prefix)?$this->model->prefix:'', ',', $notPrefix);
            $this->queryBuilder = $this->queryBuilder->orderBy($order['column'],$order['order']);
        }
        return $this;
    }

    public function applyPeriod(){
        $period = $this->getPeriod();
        if($period!=null){
            $this->queryBuilder = $this->queryBuilder
            ->where($this->model->getTable().'.created_at','>=',$period['timeStart'])
            ->where($this->model->getTable().'.created_at','<=',$period['timeFinish']);
        }

        return $this;
    }    

    public function applyUser($column = 'user_id',$guard = 'api'){
        $this->queryBuilder = $this->queryBuilder->where($column,Auth::guard($guard)->user()->id);
        return $this;
    }
    public function getModel()
    {
        return $this->model;
    }
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
    public function get()
    {
        return $this->queryBuilder->get();
    }
    public function paginate($perpage)
    {
        return $this->queryBuilder->paginate($perpage);
    }

    public function makeToApi($paginateDefault = true, $perpageDefault = 30)
    {
        return ModelForApi::make($this->queryBuilder, $this->query, $paginateDefault, $perpageDefault);
    }

    public function findAndPaginate($paginateDefault = true, $perpageDefault = 30)
    {
        return ModelForApi::findWithPaginate($this->queryBuilder, $this->query, $paginateDefault, $perpageDefault);
    }

    public function findMake($paginateDefault = true, $perpageDefault = 30)
    {
        return ModelForApi::findMakeWithPaginate($this->queryBuilder, $this->query, $paginateDefault, $perpageDefault);
    }

    public function queryToKeyValue($string, $validValues, $delimiter = ',')
    {
        $keyValue = [];

        foreach ($validValues as $value) {
            $keyValue[$value] = false;
        }

        if ($value != null) {
            $arrayValue = explode($delimiter, $string);

            foreach ($arrayValue as $value) {
                if (array_key_exists($value, $keyValue)) {
                    $keyValue[$value] = true;
                }
            }
        }
        return $keyValue;
    }

    public function queryToSelect($string, $prefix = '',$tableName = '', $delimiter = ',')
    {
        $prefix = $prefix!=''? $prefix . '_' : null;
        $tableName = $tableName!=''? $tableName . '.' : null;

        if ($string != null) {
            $arrayValue = explode($delimiter, $string);

            if ($prefix != '' || $tableName!='') {
                $arrayReturns = [];
                foreach ($arrayValue as $value) {
                    $arrayReturns[] = $tableName . $prefix . $value;
                }
                return $arrayReturns;
            }

            return $arrayValue;
        }
        return [$tableName .'*'];
    }
    
    public function queryToStatus($string, $prefix = '',$delimiter = ',')
    {
        $prefix = $prefix ? $prefix . '_' : null;

        if ($string != null) {
            $arrayValue = explode($delimiter, $string);

            if ($prefix != null) {
                $arrayReturns = [];
                foreach ($arrayValue as $value) {
                    $arrayReturns[] = $prefix . $value;
                }
                return $arrayReturns;
            }

            return $arrayValue;
        }
        return false;
    }

    public function queryToArray($string, $prefix = null, $delimiter = ',')
    {
        $prefix = $prefix ? $prefix . '_' : null;

        if ($string != null) {
            $arrayValue = explode($delimiter, $string);

            if ($prefix != null) {
                $arrayReturns = [];
                foreach ($arrayValue as $value) {
                    $arrayReturns[] = $prefix . $value;
                }
                return $arrayReturns;
            }

            return $arrayValue;
        }
        return '*';
    }
    public function queryToOrderBy($string, $prefix = null, $delimiter = ',', $noPrefixe = [])
    {
        $prefix = $prefix ? $prefix . '_' : null;

        if ($string != null) {
            $arrayValue = explode($delimiter, $string);
            $column = trim($arrayValue[0]);
            $order = trim($arrayValue[1]);

            $orderReturn = [];
            $orderReturn['order'] = $order;
            if ($column != 'created_at' && $column != 'updated_at' && $prefix != null && !in_array($column, $noPrefixe)) {
                $orderReturn['column'] = $prefix . $column;
            } else {
                $orderReturn['column'] = $column;
            }
            return $orderReturn;
        }
        return ['column' => 'created_at', 'order' => 'desc'];
    }

    public function getPeriod(){
        if(isset($this->query['periodTime'])){
            $times = $this->queryToArray($this->query['periodTime']);
            if(is_array($times)){
                $timeStart = Carbon::parse($times[0]);
                $timeFinish = Carbon::parse($times[1]);                
                return [
                    'timeStart'=>$timeStart->toDateTimeString(),
                    'timeFinish'=>$timeFinish->toDateTimeString()
                ];
            }                        
        }else if(isset($this->query['period'])){            
            $timeStart = Carbon::now()->subDays($this->query['period'])->startOfDay();
            $timeFinish = Carbon::now()->endOfDay();            
            return [
                'timeStart'=>$timeStart->toDateTimeString(),
                'timeFinish'=>$timeFinish->toDateTimeString()
            ];
        }
        return null;
    }    
}
