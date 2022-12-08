<?php

/**
 * Apache 2.0
 * Copyright 2022 Beijing Volcano Engine Technology Co., Ltd.
 */

namespace DataTester\Utils;

class MetaUtils
{
    /**
     * json to object
     * @param $entity
     * @param $entityClass
     * @return mixed
     */
    public static function generateEntity($entity, $entityClass)
    {
        if ($entity instanceof $entityClass) {
            $entityObject = $entity;
        } else {
            $entityObject = new $entityClass;
            foreach ($entity as $key => $value) {
                $propSetter = 'set'. MetaUtils::transferCamelString($key);
                if (method_exists($entityObject, $propSetter)) {
                    $entityObject->$propSetter($value);
                }
            }
        }
        return $entityObject;
    }

    /**
     * json array to object array
     * @param $entities
     * @param $entityClass
     * @return array
     */
    public static function generateEntityArray($entities, $entityClass): array
    {
        $entityArray = [];
        foreach ($entities as $value) {
            $entityObject = MetaUtils::generateEntity($value, $entityClass);
            array_push($entityArray, $entityObject);
        }
        return $entityArray;
    }

    /**
     * json array to object map
     * @param $map
     * @param $entityClass
     * @return array
     */
    public static function map2EntityMap($map, $entityClass): array
    {
        $entityMap = [];
        foreach ($map as $key => $value) {
            $entityObject = self::generateEntity($value, $entityClass);
            $entityMap[$key] = $entityObject;
        }
        return $entityMap;
    }

    /**
     * transfer abc_def_xxx => AbcDefXxx
     * @param $rawStr
     * @return string
     */
    private static function transferCamelString($rawStr): string
    {
        return str_replace(" ", "", ucwords(str_replace("_", " ", strtolower($rawStr))));
    }
}