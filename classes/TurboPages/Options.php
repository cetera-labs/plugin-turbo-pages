<?php

namespace TurboPages;

class Options {

    use \Cetera\DbConnection;

    private static function getFilenameDefault(): String {
        return 'turbo-default.xml';
    }

    public static function getFilename(): String {
        $valueCurrent = self::configGet('filename');
        return empty($valueCurrent) ? self::getFilenameDefault() : $valueCurrent;
    }

    public static function getProtocol(): bool {
        return !self::configGet('protocol');
    }

    public static function getDirIDs(): Array {
        return self::configGet('dirs') ?? [];
    }

    public static function getMaterialIDs(): Array {
        return self::configGet('materials') ?? [];
    }

    public static function setFilename(String $filename) {
        self::configSet('filename', $filename);
    }

    public static function setProtocol(bool $protocol) {
        self::configSet('protocol', !$protocol);
    }

    public static function setDirIDs(Array $dir_ids) {
        self::configSet('dirs', $dir_ids);
    }

    public static function setMaterialIDs(Array $material_ids) {
        self::configSet('materials', $material_ids);
    }

    public static function getAllCatalogs(): \Cetera\Iterator\Catalog\Catalog {
        
        $allCatalogs = new \Cetera\Iterator\Catalog\Catalog();
        $allCatalogs->orderBy('b.lft', 'ASC');
        
        return $allCatalogs;

    }

    public static function getView(Array $ids, String $from) {

        if (count($ids) === 0) {
            return [];
        }

        $structure = ['dir_data' => 'id, name, tablename alias', 'materials' => 'id, name, alias'];
        $select = $structure[$from];

        $query = self::getDbConnection()->createQueryBuilder();
        $result = $query
            ->select($select)
            ->from($from)
            ->where('id in (' . implode(',', $ids) . ')')
            ->execute();

        $counter = 1;
        $view = [];
        while ($row = $result->fetch()) {
            $view[] = ['N' => $counter, 'id' => $row['id'], 'header' => $row['name'], 'alias' => $row['alias']];
            $counter++;
        }
    
        return $view;
    }

}
?>