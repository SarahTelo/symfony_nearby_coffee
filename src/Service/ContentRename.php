<?php

namespace App\Service;

/**
 ** Service permettant de renomer des données en BDD avant de les transmettre au front
 */
class ContentRename 
{

    /**
     ** Fonction de renomage
     *
     * @param array $arrayToRename
     * @return array
     */
    public function renamedRoles(array $arrayToRename) {

        $renamedStrings = [];
        foreach ($arrayToRename as $value) 
        {
            if ($value == 'ROLE_ADMIN') {
                $renamedStrings[] = 'Administrateur';
            } elseif ($value == 'ROLE_RESPONSIBLE') {
                $renamedStrings[] = 'Responsable';
            } else {
                continue;
            }
        }

        return $renamedStrings;

    }
}