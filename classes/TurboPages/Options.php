<?php

namespace TurboPages;

class Options {

    use \Cetera\DbConnection;

    private static function getFilenameDefault() {
        return 'turbo-default.xml';
    }

    public static function getFilename() {
          return empty($valueCurrent) ? self::getFilenameDefault() : $valueCurrent;
    }

    public function getDirIDs() {
        return self::configGet('dirs') ?? [];
    }

    public function getMaterialIDs() {
        return self::configGet('materials') ?? [];
    }

    public function setFilename(String $filename) {
        self::configSet('filename', $filename);
    }

    public function setDirIDs(Array $dir_ids) {
        self::configSet('dirs', $dir_ids);
    }

    public function setMaterialIDs(Array $material_ids) {
        self::configSet('materials', $material_ids);
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
