<?php

namespace ERS;

abstract class DataManager {

    public function getById($id) {}

    public function deleteById($id) {}

    public function getMany() {}

    protected function _reformatSingle($data, $type)
    {
        $d = [
            'data' => [
                'type' => $type,
                'id' => intval($data['id']),
                'attributes' => $data
            ]
        ];
        unset ($d['data']['attributes']['id']);
        return $d;
    }

    protected function _reformatMultiple($data, $type) {
        $d = ['data' => []];
        foreach($data as $row) {
            $_d = [
                'type' => $type,
                'id' => intval($row['id']),
                'attributes' => $row
            ];
            unset ($_d['attributes']['id']);
            array_push($d['data'], $_d);
        }
        return $d;
    }

}