<?php

namespace TurboPages;

class View {

    use \Cetera\DbConnection;

    public static function get(Array $ids, String $from) {

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