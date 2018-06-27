<?php
// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
// throw out the user if it is not admin
if (isLogged() !== '1') {
    header("Location:" . ACCESS_URL);
    exit (0);
}
// extract get data to get option by user
$opt                                = DataFilter::getObject()->cleanData($_GET);

if (isset($opt['opt']) and ! empty($opt['opt'])) {
    switch ($opt['opt']) {
        
        case 'oneDay':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['dt']) or ($submittedData['dt'] === '')) {
                $dtRep = DBDATE;
            } else {
                $dtRep = date('Y-m-d', strtotime($submittedData['dt']));
            }
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">View all income one day</div>
                    <div class="panel-body">
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center">
                            <thead>
                                <tr>
                                    <th>Test Category</th>
                                    <th>Number of Tests</th>
                                    <th>Total Price (<span class="fa fa-rupee"></span>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                                $catData = DbOperations::getObject()->fetchData($sql);
                                $grTotTst = 0;
                                $grTotPrice = 0;
                                foreach ($catData as $cats) {
                                    
                                    $tsql = 'select'
                                        . ' count(pt_id) as no_of_tests,'
                                        . ' sum(pt_price) as tot_rs'
                                        . ' from patient_tests, test_list'
                                        . ' where'
                                        . ' pt_id = test_id'
                                        . ' and under_cat = "' . $cats['cat_id'] . '"'
                                        . ' and date(pt_dttm) = "' . $dtRep . '"';
                                    
                                    $incDat = DbOperations::getObject()->fetchData($tsql);
                                    $grTotTst += intval($incDat[0]['no_of_tests']);
                                    $grTotPrice += floatval($incDat[0]['tot_rs']);
                                    ?>
                                <tr>
                                    <td><?php echo $cats['cat_name']; ?></td>
                                    <td class="text-right"><?php echo $incDat[0]['no_of_tests']; ?></td>
                                    <td class="text-right"><span class="fa fa-rupee"></span>&nbsp;<?php echo number_format(floatval($incDat[0]['tot_rs']), 2, '.', ','); ?></td>
                                </tr>
                                    <?php
                                }
                                $psql = 'select'
                                    . ' sum(ptc_tot_price) as sum_total_price,'
                                    . ' sum(ptc_discount) as sum_total_discount'
                                    . ' from patient_test_calculations'
                                    . ' where'
                                    . ' date(ptc_dttm) = "' . $dtRep . '"';
                                $totDat = DbOperations::getObject()->fetchData($psql);
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right">Sum Totals :</th>
                                    <th class="text-right"><?php print($grTotTst); ?></th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format(floatval($grTotPrice), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="2">Total Discount :</th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format(floatval($totDat[0]['sum_total_discount']), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="2">Grand Total Income :</th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format((floatval($grTotPrice) - floatval($totDat[0]['sum_total_discount'])), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <?php if (floatval($grTotPrice) !== floatval($totDat[0]['sum_total_price'])) { ?>
                                <tr>
                                    <th class="text-center text-danger" colspan="3">The income data mismatches, there seems an error in entry.</th>
                                </tr>
                                <?php } ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'View all income in one day - JK Diagnostics Admin Panel';
            break;
        
        case 'sdate':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Select a date to view income details</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="anotherpatform" name="anotherpatform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=oneDay">
                            <div class="form-group">
                                <label for="dt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="dt" name="dt" required="required" autocomplete="off" placeholder="Date to show">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="showDet" id="showDet" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'Select a date to view the income - JK Diagnostics Admin Panel';
            break;
        
        
        case 'dt2dt':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Select a date range to view income details</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="daterangeinc" name="daterangeinc" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=dateRange">
                            <div class="form-group">
                                <label for="sdt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Start Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="sdt" name="sdt" required="required" autocomplete="off" placeholder="Date to start">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">End Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="edt" name="edt" required="required" autocomplete="off" placeholder="Date to end">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="showDet" id="showDet" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'Select a date range to view the income - JK Diagnostics Admin Panel';
            break;
        
        case 'dateRange':
            
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['sdt']) or ($submittedData['sdt'] === '') or !isset($submittedData['edt']) or ($submittedData['edt'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter a date range';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sdt = strtotime($submittedData['sdt']);
            $edt = strtotime($submittedData['edt']);
            ob_start();
            ?>
            
            <p class="text-center">
                The Income Details between Dt.<?php echo date('d-m-Y', $sdt); ?> and <?php echo date('d-m-Y', $edt); ?>
                &nbsp;(All Figures in <span class="fa fa-rupee"></span>)
            </p>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-squeezed center">
                <thead>
                    <tr>
                        <td>Date</td>
                        <?php
                        $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                        $catData = DbOperations::getObject()->fetchData($sql);
                        $grTstPrice = array();
                        foreach ($catData as $cats) {
                            if (!isset($grTstPrice[$cats['cat_id']])) {
                                // to save category wise price
                                $grTstPrice[$cats['cat_id']] = 0;
                            }
                        ?>
                        <td><?php echo $cats['cat_name'] ?></td>
                        <?php } ?>
                        <td>Sum
                        <td>Tot. Discount</td>
                        <td>Gr. Tot.</td>
                    </tr>
                </thead>
                <tbody>

                        <?php
                        // to traverse through date
                        $dttm = $sdt;
                        // to store grand totla discount
                        $grTotDisc = 0;
                        // to save full final grand total
                        $grTotFinalPrice = 0;

                        // to save error income dates
                        $errDates = array();

                        while ($dttm < $edt) {
                            ?>
                    <tr>
                        <td><?php echo date('d/m/y', $dttm);?></td>
                        <?php

                            $grTotPrice = 0;

                            foreach ($catData as $cats) {

                                $tsql = 'select'
                                    . ' sum(pt_price) as tot_rs'
                                    . ' from patient_tests, test_list'
                                    . ' where'
                                    . ' pt_id = test_id'
                                    . ' and under_cat = "' . $cats['cat_id'] . '"'
                                    . ' and date(pt_dttm) = "' . date('Y-m-d', $dttm) . '"';

                                $incDat = DbOperations::getObject()->fetchData($tsql);
                                $grTotPrice += floatval($incDat[0]['tot_rs']);
                                $grTstPrice[$cats['cat_id']] += floatval($incDat[0]['tot_rs']);
                                $grTotFinalPrice += floatval($incDat[0]['tot_rs']);

                        ?>
                        <td class="text-right"><?php echo number_format(floatval($incDat[0]['tot_rs']), 2, '.', ','); ?></td>
                        <?php } ?>
                        <td class="text-right"><?php echo number_format(floatval($grTotPrice), 2, '.', ','); ?></td>
                            <?php
                            $psql = 'select'
                                . ' sum(ptc_tot_price) as sum_total_price,'
                                . ' sum(ptc_discount) as sum_total_discount'
                                . ' from patient_test_calculations'
                                . ' where'
                                . ' date(ptc_dttm) = "' . date('Y-m-d', $dttm) . '"';
                            $totDat = DbOperations::getObject()->fetchData($psql);
                            $grTotDisc += floatval($totDat[0]['sum_total_discount']);

                            if (floatval($grTotPrice) !== floatval($totDat[0]['sum_total_price'])) {
                                array_push($errDates, date('d-m-Y', $dttm));
                            }
                            ?>
                        <td class="text-right"><?php echo number_format(floatval($totDat[0]['sum_total_discount']), 2, '.', ','); ?></td>
                        <td class="text-right">
                            <?php echo number_format((floatval($grTotPrice) - floatval($totDat[0]['sum_total_discount'])), 2, '.', ','); ?>
                        </td>
                    </tr>
                        <?php
                        $dttm += 24 * 60 * 60;
                        }
                        ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th>Gr. Totals :</th>
                        <?php foreach ($grTstPrice as $catId => $catInc) { ?>
                            <th class="text-right">
                                <?php echo number_format(floatval($catInc), 2, '.', ','); ?>
                            </th>
                        <?php } ?>
                        <th class="text-right">
                            <?php echo number_format(floatval($grTotFinalPrice), 2, '.', ','); ?>
                        </th>
                        <th class="text-right">
                            <?php echo number_format(floatval($grTotDisc), 2, '.', ','); ?>
                        </th>
                        <th class="text-right">
                            <?php echo number_format((floatval($grTotFinalPrice) - floatval($grTotDisc)), 2, '.', ','); ?>
                        </th>
                    </tr>
                    <?php if (count($errDates) > 0) { ?>
                    <tr>
                        <th class="text-center text-danger" colspan="<?php print(count($grTstPrice)+4); ?>">
                            The income data mismatches, there seems an error in entry in dates: <?php echo implode(', ', $errDates);?>
                        </th>
                    </tr>
                    <?php } ?>
                </tfoot>
            </table>

            <?php
            // get contents from buffer
            $contents                           = ob_get_contents();
            // clean and end the buffer
            ob_end_clean();

            $replacementArray                   = array(
                'PageTitle'                     => 'Income Details Between two date range - JK Diagnostics',
                'CenterContents'                => $contents,
                'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'custom.min.css'),
                'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'custom.min.js')
            );

            assignTemplate($replacementArray, 'incomeTemplate.php');
            exit(0);
            break;
            
        case 'doctorToday':
            
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['dt']) or ($submittedData['dt'] === '')) {
                $dtRep = DBDATE;
            } else {
                $dtRep = date('Y-m-d', strtotime($submittedData['dt']));
            }
            ob_start();
            $sql = 'select distinct(ptc_dr_id) as drid, dr_name from patient_test_calculations, doctor_details where dr_id = ptc_dr_id and date(ptc_dttm) = "'. $dtRep .'"';
            $drDat = DbOperations::getObject()->fetchData($sql);
            if (count($drDat) < 1) {
                die('<h1>No doctor data to show</h1>');
            }
            $grTotInc = 0;
            $grTotDisc = 0;
            foreach ($drDat as $dr) {
                
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">Income by Dr. <?php echo $dr['dr_name']; ?> on Dt. <?php echo date('d-m-Y', strtotime($dtRep)); ?></div>

                    <?php
                    $drTot = 0;
                    $drTotDisc = 0;
                    $patSql = 'select'
                        . ' distinct(ptc_pat_id) as patid,'
                        . ' p_name'
                        . ' from patient_details, patient_test_calculations'
                        . ' where ptc_dr_id = "' . $dr['drid'] . '"'
                        . ' and ptc_pat_id = pid'
                        . ' and  date(ptc_dttm) = "'. $dtRep .'"';
                    //die($patSql);
                    $patData = DbOperations::getObject()->fetchData($patSql);
                    $patCount = 0;
                    foreach ($patData as $pat) {
                        $fetchDiscSql = 'select sum(ptc_discount) as totdisc'
                            . ' from patient_test_calculations where ptc_pat_id = "' . $pat['patid'] . '"'
                            . ' and date(ptc_dttm) = "'. $dtRep .'"'
                            . ' and ptc_dr_id = "' . $dr['drid'] . '"';
                        $priceDisc = DbOperations::getObject()->fetchData($fetchDiscSql);
                        $drTotDisc += floatval($priceDisc[0]['totdisc']);
                        $grTotDisc += floatval($priceDisc[0]['totdisc']);
                        ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <caption><?php echo $pat['p_name']; ?></caption>
                        <thead>
                            <tr>
                                <th class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    Tests Done
                                </th>
                                <th class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    Price
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totPrice = 0;
                            $ptestsql = 'select'
                                . ' test_name,'
                                . ' pt_price'
                                . ' from'
                                . ' test_list, patient_tests, patient_test_calculations'
                                . ' where '
                                . ' pat_id = "' . $pat['patid'] . '"'
                                . ' and pt_test_id = test_id'
                                . ' and ptc_dr_id = "' . $dr['drid'] . '"'
                                . ' and pt_dttm = ptc_dttm'
                                . ' and date(pt_dttm) = "' . $dtRep . '"';
                            //die($ptestsql);
                            $ptTestDat = DbOperations::getObject()->fetchData($ptestsql);
                            foreach ($ptTestDat as $tstDat) {
                                $totPrice += floatval($tstDat['pt_price']);
                                $grTotInc += floatval($tstDat['pt_price']);
                                $drTot += floatval($tstDat['pt_price']);
                            ?>
                            <tr>
                                <td><?php echo $tstDat['test_name'];?></td>
                                <td class="text-right"><?php echo number_format(floatval($tstDat['pt_price']), 2, '.', ',');?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Income from the patient</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($totPrice), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <td>Total Discount to the patient</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($priceDisc[0]['totdisc']), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <th>Grand total income from the patient</th>
                                <th class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format((floatval($totPrice) - floatval($priceDisc[0]['totdisc'])), 2, '.', ',');?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php } ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <tbody>
                            <tr>
                                <td class="col-lg-8 col-md-8 col-sm-8 col-xs-8">Grand total income via the doctor</td>
                                <td class="text-right col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($drTot), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <td>Grand total discount granted</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($drTotDisc), 2, '.', ',');?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-light">
                                <td>Grand Income</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format((floatval($drTot) - floatval($drTotDisc)), 2, '.', ',');?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php } if (intval($grTotInc) > 0) { ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-squeezed center">
                    <tbody>
                        <tr>
                            <td>Grand total income by all</td>
                            <td class="text-right"><?php echo number_format(floatval($grTotInc), 2, '.', ',');?></td>
                        </tr>
                        <tr>
                            <td>Grand total discount</td>
                            <td class="text-right"><?php echo number_format(floatval($grTotDisc), 2, '.', ',');?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-light">
                            <td>Grand Income</td>
                            <td class="text-right"><?php echo number_format((floatval($grTotInc) - floatval($grTotDisc)), 2, '.', ',');?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            }
            $sqlDoc = 'select dr_name, dr_org, dr_phone, staff_name, dr_created from doctor_details, staff_users where date(dr_created) = "' . $dtRep . '" and dr_created_by = staff_id';
            $docDat = DbOperations::getObject()->fetchData($sqlDoc);
            if (count($docDat) > 0) {
                $cnt = 0;
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">New Doctor Added on Dt. <?php echo date('d-m-Y', strtotime($dtRep)); ?></div>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <thead>
                            <tr>
                                <th>Sl.</th>
                                <th>Name</th>
                                <th>Org./Deptt./Area</th>
                                <th>Contact</th>
                                <th>Entered By</th>
                                <th>Create Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docDat as $doc) { ?>
                            <tr>
                                <td><?php print(++$cnt); ?></td>
                                <td><?php echo $doc['dr_name'] ?></td>
                                <td><?php echo $doc['dr_org'] ?></td>
                                <td><?php echo $doc['dr_phone'] ?></td>
                                <td><?php echo $doc['staff_name'] ?></td>
                                <td><?php echo date('h:i:s A', strtotime($doc['dr_created'])); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            }
            // get contents from buffer
            $contents                           = ob_get_contents();
            // clean and end the buffer
            ob_end_clean();

            $replacementArray                   = array(
                'PageTitle'                     => 'Income Details of doctor in a specific day - JK Diagnostics',
                'CenterContents'                => $contents,
                'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'custom.min.css'),
                'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'custom.min.js')
            );

            assignTemplate($replacementArray, 'doctorIncomeTemplate.php');
            exit(0);
            break;
            
        case 'doctorSdate':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Select a date to view income details via doctors</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="docIncform" name="docIncform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=doctorToday">
                            <div class="form-group">
                                <label for="dt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="dt" name="dt" required="required" autocomplete="off" placeholder="Date to show">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="showDet" id="showDet" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'Select a date to view the income via doctor - JK Diagnostics Admin Panel';
            break;
        
        case 'doctorDt2Dt':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Select a date range to view income details via doctors</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="daterangeinc" name="daterangeinc" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=docdateRange">
                            <div class="form-group">
                                <label for="sdt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Start Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="sdt" name="sdt" required="required" autocomplete="off" placeholder="Date to start">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edt" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">End Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="edt" name="edt" required="required" autocomplete="off" placeholder="Date to end">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="showDet" id="showDet" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'Select a date range to view the income via doctor - JK Diagnostics Admin Panel';
            break;
        
        case 'docdateRange':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['sdt']) or ($submittedData['sdt'] === '') or !isset($submittedData['edt']) or ($submittedData['edt'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter a date range';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sdt = strtotime($submittedData['sdt']);
            $edt = strtotime($submittedData['edt']);
            ob_start();
            $sql = 'select distinct(ptc_dr_id) as drid, dr_name from patient_test_calculations, doctor_details where dr_id = ptc_dr_id and date(ptc_dttm) between "'. date('Y-m-d', $sdt) .'" and "' . date('Y-m-d', $edt) . '"';
            $drDat = DbOperations::getObject()->fetchData($sql);
            if (count($drDat) < 1) {
                die('<h1>No doctor data to show</h1>');
            }
            $grTotInc = 0;
            $grTotDisc = 0;
            foreach ($drDat as $dr) {
                
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">Income by Dr. <?php echo $dr['dr_name']; ?> between Dt. <?php echo date('d-m-Y', $sdt); ?> to <?php echo date('d-m-Y', $edt); ?></div>

                    <?php
                    $drTot = 0;
                    $drTotDisc = 0;
                    $patSql = 'select'
                        . ' distinct(ptc_pat_id) as patid,'
                        . ' p_name'
                        . ' from patient_details, patient_test_calculations'
                        . ' where ptc_dr_id = "' . $dr['drid'] . '"'
                        . ' and ptc_pat_id = pid'
                        . ' and  date(ptc_dttm) between "'. date('Y-m-d', $sdt) .'" and "' . date('Y-m-d', $edt) . '"'
                        . ' order by p_name';
                    //die($patSql);
                    $patData = DbOperations::getObject()->fetchData($patSql);
                    $patCount = 0;
                    foreach ($patData as $pat) {
                        $fetchDiscSql = 'select sum(ptc_discount) as totdisc'
                            . ' from patient_test_calculations where ptc_pat_id = "' . $pat['patid'] . '"'
                            . ' and date(ptc_dttm) between "'. date('Y-m-d', $sdt) .'" and "' . date('Y-m-d', $edt) . '"'
                            . ' and ptc_dr_id = "' . $dr['drid'] . '"';
                        $priceDisc = DbOperations::getObject()->fetchData($fetchDiscSql);
                        $drTotDisc += floatval($priceDisc[0]['totdisc']);
                        $grTotDisc += floatval($priceDisc[0]['totdisc']);
                        ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <caption><?php echo $pat['p_name']; ?></caption>
                        <thead>
                            <tr>
                                <th class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    Date
                                </th>
                                <th class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                    Tests Done
                                </th>
                                <th class="col-lg-2 col-md-2 col-sm-4 col-xs-2">
                                    Price
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totPrice = 0;
                            $ptestsql = 'select'
                                . ' ptc_dttm,'
                                . ' test_name,'
                                . ' pt_price'
                                . ' from'
                                . ' test_list, patient_tests, patient_test_calculations'
                                . ' where '
                                . ' pat_id = "' . $pat['patid'] . '"'
                                . ' and pt_test_id = test_id'
                                . ' and ptc_dr_id = "' . $dr['drid'] . '"'
                                . ' and pt_dttm = ptc_dttm'
                                . ' and date(pt_dttm) between "'. date('Y-m-d', $sdt) .'" and "' . date('Y-m-d', $edt) . '"'
                                . ' order by pt_dttm';
                            //die($ptestsql);
                            $ptTestDat = DbOperations::getObject()->fetchData($ptestsql);
                            foreach ($ptTestDat as $tstDat) {
                                $totPrice += floatval($tstDat['pt_price']);
                                $grTotInc += floatval($tstDat['pt_price']);
                                $drTot += floatval($tstDat['pt_price']);
                            ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($tstDat['ptc_dttm']));?></td>
                                <td><?php echo $tstDat['test_name'];?></td>
                                <td class="text-right"><?php echo number_format(floatval($tstDat['pt_price']), 2, '.', ',');?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total Income from the patient</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($totPrice), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Total Discount to the patient</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($priceDisc[0]['totdisc']), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">Grand total income from the patient</th>
                                <th class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format((floatval($totPrice) - floatval($priceDisc[0]['totdisc'])), 2, '.', ',');?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    <?php } ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <tbody>
                            <tr>
                                <td class="col-lg-10 col-md-10 col-sm-10 col-xs-10">Grand total income via the doctor</td>
                                <td class="text-right col-lg-2 col-md-2 col-sm-2 col-xs-2">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($drTot), 2, '.', ',');?>
                                </td>
                            </tr>
                            <tr>
                                <td>Grand total discount granted</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format(floatval($drTotDisc), 2, '.', ',');?>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-light">
                                <td>Grand Income</td>
                                <td class="text-right">
                                    <span class="fa fa-rupee"></span>&nbsp;
                                    <?php echo number_format((floatval($drTot) - floatval($drTotDisc)), 2, '.', ',');?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php } if (intval($grTotInc) > 0) { ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-squeezed center">
                    <tbody>
                        <tr>
                            <td>Grand total income by all</td>
                            <td class="text-right"><?php echo number_format(floatval($grTotInc), 2, '.', ',');?></td>
                        </tr>
                        <tr>
                            <td>Grand total discount</td>
                            <td class="text-right"><?php echo number_format(floatval($grTotDisc), 2, '.', ',');?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-light">
                            <td>Grand Income</td>
                            <td class="text-right"><?php echo number_format((floatval($grTotInc) - floatval($grTotDisc)), 2, '.', ',');?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            }
            $sqlDoc = 'select dr_name, dr_org, dr_phone, staff_name, dr_created from doctor_details, staff_users where date(dr_created) between "'. date('Y-m-d', $sdt) .'" and "' . date('Y-m-d', $edt) . '" and dr_created_by = staff_id order by dr_created';
            $docDat = DbOperations::getObject()->fetchData($sqlDoc);
            if (count($docDat) > 0) {
                $cnt = 0;
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading text-center">New Doctor Added between Dt. <?php echo date('d-m-Y', $sdt); ?> to <?php echo date('d-m-Y', $edt); ?></div>
                    <table cellpadding="0" cellspacing="0" border="0" class="table table-squeezed">
                        <thead>
                            <tr>
                                <th>Sl.</th>
                                <th>Name</th>
                                <th>Org./Deptt./Area</th>
                                <th>Contact</th>
                                <th>Entered By</th>
                                <th>Create Date Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($docDat as $doc) { ?>
                            <tr>
                                <td><?php print(++$cnt); ?></td>
                                <td><?php echo $doc['dr_name'] ?></td>
                                <td><?php echo $doc['dr_org'] ?></td>
                                <td><?php echo $doc['dr_phone'] ?></td>
                                <td><?php echo $doc['staff_name'] ?></td>
                                <td><?php echo date('d/m/Y h:i:s A', strtotime($doc['dr_created'])); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            }
            // get contents from buffer
            $contents                           = ob_get_contents();
            // clean and end the buffer
            ob_end_clean();

            $replacementArray                   = array(
                'PageTitle'                     => 'Income Details of doctor in a specific date range - JK Diagnostics',
                'CenterContents'                => $contents,
                'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'custom.min.css'),
                'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'custom.min.js')
            );

            assignTemplate($replacementArray, 'doctorIncomeTemplate.php');
            exit(0);
            break;
        
        default:
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['dt']) or ($submittedData['dt'] === '')) {
                $dtRep = DBDATE;
            } else {
                $dtRep = date('Y-m-d', strtotime($submittedData['dt']));
            }
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">View all income by you today</div>
                    <div class="panel-body">
                        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center">
                            <thead>
                                <tr>
                                    <th>Test Category</th>
                                    <th>Number of Tests</th>
                                    <th>Total Price (<span class="fa fa-rupee"></span>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                                $catData = DbOperations::getObject()->fetchData($sql);
                                $grTotTst = 0;
                                $grTotPrice = 0;
                                foreach ($catData as $cats) {

                                    $tsql = 'select'
                                        . ' count(pt_id) as no_of_tests,'
                                        . ' sum(pt_price) as tot_rs'
                                        . ' from patient_tests, test_list'
                                        . ' where'
                                        . ' pt_id = test_id'
                                        . ' and under_cat = "' . $cats['cat_id'] . '"'
                                        . ' and date(pt_dttm) = "' . $dtRep . '"'
                                        . ' and pt_created_by = "' . $_SESSION['UID'] . '"';

                                    $incDat = DbOperations::getObject()->fetchData($tsql);
                                    $grTotTst += intval($incDat[0]['no_of_tests']);
                                    $grTotPrice += floatval($incDat[0]['tot_rs']);
                                    ?>
                                <tr>
                                    <td><?php echo $cats['cat_name']; ?></td>
                                    <td class="text-right"><?php echo $incDat[0]['no_of_tests']; ?></td>
                                    <td class="text-right"><span class="fa fa-rupee"></span>&nbsp;<?php echo number_format(floatval($incDat[0]['tot_rs']), 2, '.', ','); ?></td>
                                </tr>
                                    <?php
                                }
                                $psql = 'select'
                                    . ' sum(ptc_tot_price) as sum_total_price,'
                                    . ' sum(ptc_discount) as sum_total_discount'
                                    . ' from patient_test_calculations'
                                    . ' where'
                                    . ' date(ptc_dttm) = "' . $dtRep . '"'
                                    . ' and ptc_staff_id = "' . $_SESSION['UID'] . '"';
                                $totDat = DbOperations::getObject()->fetchData($psql);
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="text-right">Sum Totals :</th>
                                    <th class="text-right"><?php print($grTotTst); ?></th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format(floatval($grTotPrice), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="2">Total Discount :</th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format(floatval($totDat[0]['sum_total_discount']), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-right" colspan="2">Grand Total Income :</th>
                                    <th class="text-right">
                                        <span class="fa fa-rupee"></span>&nbsp;
                                        <?php echo number_format((floatval($grTotPrice) - floatval($totDat[0]['sum_total_discount'])), 2, '.', ','); ?>
                                    </th>
                                </tr>
                                <?php if (floatval($grTotPrice) !== floatval($totDat[0]['sum_total_price'])) { ?>
                                <tr>
                                    <th class="text-center text-danger" colspan="3">The income data mismatches, there seems an error in entry.</th>
                                </tr>
                                <?php } ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle                      = 'View all income by you - JK Diagnostics Admin Panel';
            break;
    }
} else {
    $submittedData = DataFilter::getObject()->cleanData($_POST);
    if (!isset($submittedData['dt']) or ($submittedData['dt'] === '')) {
        $dtRep = DBDATE;
    } else {
        $dtRep = date('Y-m-d', strtotime($submittedData['dt']));
    }
    ob_start();
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">View all income by you today</div>
            <div class="panel-body">
                <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center">
                    <thead>
                        <tr>
                            <th>Test Category</th>
                            <th>Number of Tests</th>
                            <th>Total Price (<span class="fa fa-rupee"></span>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                        $catData = DbOperations::getObject()->fetchData($sql);
                        $grTotTst = 0;
                        $grTotPrice = 0;
                        foreach ($catData as $cats) {

                            $tsql = 'select'
                                . ' count(pt_id) as no_of_tests,'
                                . ' sum(pt_price) as tot_rs'
                                . ' from patient_tests, test_list'
                                . ' where'
                                . ' pt_id = test_id'
                                . ' and under_cat = "' . $cats['cat_id'] . '"'
                                . ' and date(pt_dttm) = "' . $dtRep . '"'
                                . ' and pt_created_by = "' . $_SESSION['UID'] . '"';

                            $incDat = DbOperations::getObject()->fetchData($tsql);
                            $grTotTst += intval($incDat[0]['no_of_tests']);
                            $grTotPrice += floatval($incDat[0]['tot_rs']);
                            ?>
                        <tr>
                            <td><?php echo $cats['cat_name']; ?></td>
                            <td class="text-right"><?php echo $incDat[0]['no_of_tests']; ?></td>
                            <td class="text-right"><span class="fa fa-rupee"></span>&nbsp;<?php echo number_format(floatval($incDat[0]['tot_rs']), 2, '.', ','); ?></td>
                        </tr>
                            <?php
                        }
                        $psql = 'select'
                            . ' sum(ptc_tot_price) as sum_total_price,'
                            . ' sum(ptc_discount) as sum_total_discount'
                            . ' from patient_test_calculations'
                            . ' where'
                            . ' date(ptc_dttm) = "' . $dtRep . '"'
                            . ' and ptc_staff_id = "' . $_SESSION['UID'] . '"';
                        $totDat = DbOperations::getObject()->fetchData($psql);
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right">Sum Totals :</th>
                            <th class="text-right"><?php print($grTotTst); ?></th>
                            <th class="text-right">
                                <span class="fa fa-rupee"></span>&nbsp;
                                <?php echo number_format(floatval($grTotPrice), 2, '.', ','); ?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-right" colspan="2">Total Discount :</th>
                            <th class="text-right">
                                <span class="fa fa-rupee"></span>&nbsp;
                                <?php echo number_format(floatval($totDat[0]['sum_total_discount']), 2, '.', ','); ?>
                            </th>
                        </tr>
                        <tr>
                            <th class="text-right" colspan="2">Grand Total Income :</th>
                            <th class="text-right">
                                <span class="fa fa-rupee"></span>&nbsp;
                                <?php echo number_format((floatval($grTotPrice) - floatval($totDat[0]['sum_total_discount'])), 2, '.', ','); ?>
                            </th>
                        </tr>
                        <?php if (floatval($grTotPrice) !== floatval($totDat[0]['sum_total_price'])) { ?>
                        <tr>
                            <th class="text-center text-danger" colspan="3">The income data mismatches, there seems an error in entry.</th>
                        </tr>
                        <?php } ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    $pageTitle                      = 'View all income by you - JK Diagnostics Admin Panel';
}
// get contents from buffer
$contents                           = ob_get_contents();
// clean and end the buffer
ob_end_clean();

$replacementArray                   = array(
    'PageTitle'                     => $pageTitle,
    'ErrorMessages'                 => getAlertMsg(),
    'CenterContents'                => $contents,
    'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'bootstrap-datetimepicker.min.css', 'jquery.dataTables.min.css', 'custom.min.css'),
    'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'bootstrap-typeahead.min.js', 'moment.min.js', 'bootstrap-datetimepicker.min.js', 'jquery.dataTables.min.js', 'custom.min.js')
);

assignTemplate($replacementArray);
// the ending php tag has been intentionally not used to avoid unwanted whitespaces before document starts
