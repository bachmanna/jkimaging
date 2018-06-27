<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
$newData = DbOperations::getObject()->fetchData('select * from test_list_new');
$updCount = 0;
$insCount = 0;
DbOperations::getObject()->commitTransaction('start');
foreach ($newData as $rows) {
    $sql = 'select test_id from test_list where test_name = "' . $rows['test_name'] . '" and under_cat = "' . $rows['under_cat'] . '" and under_subcat = "' . $rows['under_subcat'] . '"';
    $found = DbOperations::getObject()->fetchData($sql);
    if (count($found) > 0) {
        $sql = 'update test_list set test_price = ? where test_id = ?';
        $upd = DbOperations::getObject()->runQuery($sql, array($rows['test_price'], $found[0]['test_id']));
        ++$updCount;
    } else {
        $upd = DbOperations::getObject()->insertData('test_list', array('', $rows['test_name'], $rows['test_price'], $rows['under_cat'], $rows['under_subcat'], $rows['test_created_by'], $rows['test_created']));
        ++$insCount;
    }
}
echo $updCount . ' updated, ' . $insCount . ' inserted';