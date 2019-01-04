<?php

namespace App\Libraries;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ModelForApi
{
    public static function findWithPaginate($data, $query, $paginateDefault = true, $perpageDefault = 30)
    {
        $paginate = isset($query['paginate']) ? $query['paginate'] : $paginateDefault;
        $perpage = isset($query['perpage']) ? $query['perpage'] : $perpageDefault;

        if ($paginate===true) {           
            return $data->paginate($perpage);
        } else {
            return $data->get();
        }

    }

    public static function findMakeWithPaginate($data, $query, $paginateDefault = true, $perpageDefault = 30)
    {
        $paginate = isset($query['paginate']) ? $query['paginate'] : $paginateDefault;
        $perpage = isset($query['perpage']) ? $query['perpage'] : $perpageDefault;
        if ($paginate===true) {
            return self::make($data->paginate($perpage), $query);
        } else {
            return self::getElement($data->get(), $query);
        }

    }

    /**
     * This function return to api a structure with object name and paginate
     *
     * @param: model response
     * @return: Array name with items and paginate
     */

    public static function make($data, $query = null, $paginateDefault = true, $perpageDefault = 30)
    {

        if ($data == null) {
            return null;
        } elseif ($data instanceof Collection) {
            if ($data->isEmpty()) {
                return null;
            }
        } else if ($data instanceof LengthAwarePaginator && $data[0] == null) {
            return null;
        }
        //return $data;
        if ($data instanceof Builder || $data instanceof BelongsToMany) {
            //case it's a non object resolved, resolve and call self            
            $resolve = self::findWithPaginate($data, $query, $paginateDefault, $perpageDefault);
            return self::make($resolve);

        } else if ($data instanceof LengthAwarePaginator) {
            $objectName = self::getClassName($data, true);

            $formattedData = [
                $objectName => self::arrayToApi($data),
                'paginate' => self::getPagination($data, $query),
            ];
            return $formattedData;

        } elseif ($data instanceof Collection) {
            $objectName = self::getClassName($data, true);
            $formattedData = [
                $objectName => self::arrayToApi($data),
                'paginate' => null,
            ];
            return $formattedData;

        } else {
            $objectName = self::getClassName($data);
            $formattedData = [
                $objectName => self::resolveObject($data),
                'paginate' => null,
            ];
            return $formattedData;
        }
    }
    /**
     * This function return to api a structure with object name
     *
     * @param: model response
     * @return: Array name with items
     */
    public static function get($data)
    {

        if ($data == null) {
            return null;
        } elseif ($data instanceof Collection || $data instanceof LengthAwarePaginator) {
            if ($data->isEmpty()) {
                return null;
            }
        }

        if ($data instanceof Collection || $data instanceof LengthAwarePaginator) {
            return self::arrayToApi($data);
        } else {
            return self::resolveObject($data);
        }
    }
    /**
     * This function return to api a structure with object result
     *
     * @param: model response
     * @return: Array results
     */
    public static function getElement($data)
    {

        if ($data == null) {
            return null;
        } elseif ($data instanceof Collection || $data instanceof LengthAwarePaginator) {
            if ($data->isEmpty()) {
                return null;
            }
        }

        if ($data instanceof Collection) {
            $objectName = self::getClassName($data, true);
            $formattedData = [
                $objectName => self::arrayToApi($data),
            ];
            return $formattedData;

        } else {
            $objectName = self::getClassName($data);
            $formattedData = [
                $objectName => self::resolveObject($data),
            ];
            if ($formattedData == null) {
                return null;
            }
            return $formattedData;
        }
    }
    /**
     * This function return for api a structure of paginate
     *
     * @param: model response
     * @return: Array name with items and paginate
     */
    public static function paginate($data, $query = null)
    {
        if ($data instanceof LengthAwarePaginator) {

            return self::getPagination($data, $query);
        } else {
            return null;
        }
    }
    /**
     * This function return for api a structure paginate with object name
     *
     * @param: model response
     * @return: Array paginate
     */
    public static function paginateElement($data, $query = null)
    {
        if ($data instanceof LengthAwarePaginator) {

            return [
                'paginate' => self::getPagination($data, $query),
            ];

        } else {
            return [
                'paginate' => null,
            ];
        }
    }
    private static function getClassName($object, $isCollection = false)
    {

        if ($isCollection) {
            return self::getClassName($object[0]);
        } else {

            $className = get_class($object);
            $className = trim($className);
            $className = strrchr($className, '\/');
            $className = stripslashes($className);
        }
        return strtolower($className);
    }

    private static function arrayToApi($array)
    {
        $arrayToApi = [];

        foreach ($array as $item) {
            $arrayToApi[] = self::resolveObject($item);
        }
        if ($arrayToApi == null) {
            return null;
        }
        return $arrayToApi;
    }
    private static function getPagination($data, $query = null)
    {           

        if ($query) {
            $data->appends($query);
        }
        $data->setPath('');
        $pages = UrlWindow::make($data);
        

        if (count($pages['first']) < 2) {
            return null;
        }

        $paginate = [
            'current' => $data->currentPage(),
            'count' => $data->count(),
            'perPage' => $data->perPage(),
            'total' => $data->total(),
            'previous' => $data->previousPageUrl(),
            'next' => $data->nextPageUrl(),
            'pages' => $pages,
        ];
        return $paginate;
    }

    private static function resolveObject($data)
    {
        $attibutes = $data->toArray();
        $prefix = isset($data->prefix) ? $data->prefix . '_' : '';
        $attibutesToApi = [];
        foreach ($attibutes as $key => $attr) {
            if (isset($data->prefix)) {
                $fieldName = str_replace($data->prefix . '_', '', $key);
                $attibutesToApi[$fieldName] = $attr;
            } else {
                $attibutesToApi[$key] = $attr;
            }
        }
        return $attibutesToApi;
    }

}
