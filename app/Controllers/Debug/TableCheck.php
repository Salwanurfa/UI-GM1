<?php

namespace App\Controllers\Debug;

use App\Controllers\BaseController;

class TableCheck extends BaseController
{
    public function checkMasterB3()
    {
        $db = \Config\Database::connect();
        
        echo "<h2>MASTER_LIMBAH_B3 TABLE STRUCTURE</h2>";
        
        // Get field names
        $fields = $db->getFieldNames('master_limbah_b3');
        echo "<h3>Columns:</h3><ul>";
        foreach ($fields as $field) {
            echo "<li>$field</li>";
        }
        echo "</ul>";
        
        echo "<h3>Sample Data:</h3>";
        $query = $db->query("SELECT * FROM master_limbah_b3 LIMIT 3");
        $results = $query->getResultArray();
        echo "<pre>";
        print_r($results);
        echo "</pre>";
        
        echo "<h2>LIMBAH_B3 TABLE STRUCTURE</h2>";
        $fields2 = $db->getFieldNames('limbah_b3');
        echo "<h3>Columns:</h3><ul>";
        foreach ($fields2 as $field) {
            echo "<li>$field</li>";
        }
        echo "</ul>";
        
        echo "<h3>Sample Join Query:</h3>";
        $query2 = $db->query("
            SELECT limbah_b3.*, master_limbah_b3.* 
            FROM limbah_b3 
            LEFT JOIN master_limbah_b3 ON master_limbah_b3.id = limbah_b3.master_b3_id 
            LIMIT 1
        ");
        $result2 = $query2->getResultArray();
        echo "<pre>";
        print_r($result2);
        echo "</pre>";
    }
}
