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
        // show add new employee form
        case 'newPatient':
            ob_start();
            ?>
            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Add New Patient</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="patform" name="patform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveNewPatient">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="pname" tabindex="1" autofocus="autofocus" name="pname" required="required" autocomplete="off" placeholder="Full Name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="addr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Address: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ageyr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Age: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageyr" required="required" autocomplete="off" name="ageyr" placeholder="Yrs" value="0">
                                                    <span class="input-group-addon">Years</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="agemonth" required="required" autocomplete="off" name="agemonth" placeholder="Months" value="0">
                                                    <span class="input-group-addon">Months</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageday" required="required" autocomplete="off" name="ageday" placeholder="Days" value="0">
                                                    <span class="input-group-addon">Days</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No.: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" autocomplete="off" name="contact" placeholder="Contact Number">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="rpass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Sex: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="sex" name="sex">
                                            <option value="F">Female</option>
                                            <option value="M">Male</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <button type="submit" name="savePat" id="savePat" class="btn btn-default">Save Patient</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Add a new patient - JK Diagnostics Admin Panel';
            break;

        // save the employee data posted from the new employee form
        case 'saveNewPatient':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['pname']) or empty($submittedData['pname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient name';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient address';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['ageyr']) or is_nan($submittedData['ageyr']) or is_nan($submittedData['agemonth']) or is_nan($submittedData['ageday'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient age';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['contact']) or is_nan($submittedData['contact'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee valid contact number';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            
            // make a data array to be saves as in database table
            $saveData = array(
                'p_name'        => ucwords($submittedData['pname']),
                'p_addr'        => $submittedData['addr'],
                'p_age_yr'      => $submittedData['ageyr'],
                'p_age_month'   => $submittedData['agemonth'],
                'p_age_day'    => $submittedData['ageday'],
                'p_phone'       => $submittedData['contact'],
                'p_sex'         => $submittedData['sex'],
                'p_status'      => '1',
                'p_created_by'  => $_SESSION['UID'],
                'p_created'     => DBTIMESTAMP,
                'p_last_edited' => DBTIMESTAMP,
                'p_last_edited_by'  => $_SESSION['UID']
            );
            // start a transaction with database
            DbOperations::getObject()->commitTransaction('start');
            // insert the data
            $success                = DbOperations::getObject()->insertData('patient_details', $saveData);
            if ($success) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->commitTransaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully added a new patient. Please assign tests for today';
                session_write_close();
                header("Location:patient.php?opt=addTests&pid=".$success);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->commitTransaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while adding a patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        // add tests for the patient created or present
        case 'addTests':
//            if (isset($_SESSION['ASSIGNED_TESTS'])) {
//                unset($_SESSION['ASSIGNED_TESTS']);
//            }
            $sql = 'select pid, p_name, p_addr, p_age_yr, p_age_month, p_age_day, p_phone, p_sex, p_created from patient_details where pid="' . $opt['pid'] . '"';
            $patData = DbOperations::getObject()->fetchData($sql);
            if (count($patData) < 1) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No patient id found';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            ob_start();
            ?>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Add Tests for Patient</div>
                        <div class="panel-body">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient ID: </strong>&nbsp;<?php echo date('dmy', strtotime($patData[0]['p_created'])) . $patData[0]['pid']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Name: </strong>&nbsp;<?php echo $patData[0]['p_name']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Address: </strong>&nbsp;<?php echo $patData[0]['p_addr']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Age: </strong>&nbsp;
                                    <?php
                                    echo ($patData[0]['p_age_yr'] === '0' ? '' : $patData[0]['p_age_yr'] . '&nbsp;Yrs');
                                    echo ($patData[0]['p_age_month'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_month'] . '&nbsp;Months');
                                    echo ($patData[0]['p_age_day'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_day'] . '&nbsp;Days');
                                    ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Contact No: </strong>&nbsp;<?php echo $patData[0]['p_phone']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Gender: </strong>&nbsp;<?php echo $patData[0]['p_sex']; ?>
                            </div>
                            
                            <form role="form" style="padding-top: 50px" id="testform" name="testform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=addPrintTestsForPatient" target="_blank">
                                <hr />
                                <div class="form-group">
                                    <label for="drname" class="col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label text-right">Referrer Dr. Name: </label>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" tabindex="1" autofocus="autofocus" id="drname" name="drname">
                                            <option value="">Select</option>
                                            <?php
                                            $dr = DbOperations::getObject()->fetchData('select dr_id, dr_name, dr_org from doctor_details order by dr_name');
                                            foreach ($dr as $drData):
                                            ?>
                                            <option value="<?php echo $drData['dr_id']; ?>"><?php echo $drData['dr_name'] . ($drData['dr_org'] ==='' ? '' : ' (' . $drData['dr_org'] .')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <button type="button" class="btn btn-info" id="addDoc" name="addDoc"  data-toggle="modal" data-target="#docModal">Add Doctor</button>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group" style="padding-top: 20px">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="cat" name="cat" onchange="loadTests();">
                                            <option value="">All Category</option>
                                            <?php
                                            $cat = DbOperations::getObject()->fetchData('select cat_id, cat_name from test_cats order by cat_name');
                                            foreach ($cat as $catData):
                                            ?>
                                            <option value="<?php echo $catData['cat_id']; ?>"><?php echo $catData['cat_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="subcat" name="subcat" onchange="loadTests();">
                                            <option value="">All Sub-Category</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="alphabets" name="alphabets" onchange="loadTests();">
                                            <option value="">All</option>
                                            <?php foreach(range('a','z') as $i): ?>
                                            <option value="<?php echo $i;?>"><?php echo strtoupper($i);?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <hr />
                                <hr/>
                                <div class="well well-sm" id="testList" style="height: 200px;overflow: auto">
                                    <?php
                                    $tests = DbOperations::getObject()->fetchData('select test_id, test_name, test_price from test_list order by test_name');
                                    foreach ($tests as $testData):
                                    ?>
                                    <div class="checkbox col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label for="test_<?php echo $testData['test_id'];?>">
                                            <input type="checkbox" data-price="<?php echo $testData['test_price'];?>" name="test[]" id="test_<?php echo $testData['test_id'];?>"<?php if (isset($_SESSION['ASSIGNED_TESTS']) and (array_search($testData['test_id'], $_SESSION['ASSIGNED_TESTS']) !== false)) echo ' checked="checked"'; ?> value="<?php echo $testData['test_id']; ?>" title="<?php echo $testData['test_name']; ?>" class="testchk"> <?php echo $testData['test_name']; ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div id="testcal">
                                    <?php
                                    if (isset($_SESSION['ASSIGNED_TESTS']) and is_array($_SESSION['ASSIGNED_TESTS'])) {
                                        $testList = '';
                                        $count = 0;
                                        $sumTests = 0;
                                        foreach ($_SESSION['ASSIGNED_TESTS'] as $testId) {
                                            ++$count;
                                            $sql = 'select test_id, test_name, test_price from test_list where test_id = "' . $testId . '"';
                                            $tests = DbOperations::getObject()->fetchData($sql);
                                            if (is_array($tests) and (count($tests) > 0)) {
                                                $sumTests += (float)$tests[0]['test_price'];
                                                ?>
                                                <div class="alert alert-info alert-dismissable testLine" id="selTst_<?php echo $tests[0]['test_id'];?>" style="padding:10px;margin:2px">
                                                    <div class="row">
                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><?php print($count);?></div>
                                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><?php echo $tests[0]['test_name'];?></div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right"><span class="fa fa-rupee"></span>&nbsp;<?php echo number_format((float)$tests[0]['test_price'], 2, '.', '');?></div>
                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                            <button type="button" class="close" style="right:0" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Total Sum</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"><span class="fa fa-rupee"></span>
                                                <input type="text" id="totVal" name="totVal" class="no-disp" readonly="readonly" value="<?php echo number_format((float)$sumTests, 2, '.', '');?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 text-right">Apply Discount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right">
                                                <input type="text" id="discVal" name="discVal" class="form-control" value="0">
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right">
                                                <select id="discType" name="discType" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="P">%</option>
                                                    <option value="R">Rs.</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3" style="padding-top:4px">
                                                <span class="fa fa-rupee"></span>
                                                <input type="text" id="totDiscVal" name="totDiscVal" class="no-disp" readonly="readonly" value="0.00">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Grand Total</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <span class="fa fa-rupee"></span>
                                                <input type="text" id="grTotVal" name="grTotVal" class="no-disp" readonly="readonly" value="<?php echo number_format((float)$sumTests, 2, '.', '');?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Payment Status</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <select id="payStat" name="payStat" class="form-control">
                                                    <option value="1">Paid</option>
                                                    <option value="0">Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Precedence No</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <select id="preced" name="preced" class="form-control">
                                                    <option value="">0</option>
                                                    <?php
                                                    $sql = 'select distinct(ptc_preced_no) from patient_test_calculations where date(ptc_dttm) = "' . DBDATE . '"';
                                                    $prevNos = DbOperations::getObject()->fetchData($sql);
                                                    $preserved = array();
                                                    foreach ($prevNos as $val) {
                                                        array_push($preserved, intval($val[0]));
                                                    }
                                                    array_unique($preserved);
                                                    for ($i = 1; $i < 51; ++$i) {
                                                        if (array_search($i, $preserved) === false) {
                                                    ?>
                                                    <option value="<?php print($i); ?>"><?php print($i); ?></option>
                                                    <?php
                                                        } else {
                                                            continue;
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="hidden" id="patid" name="patid" value="<?php print($opt['pid']); ?>">
                                    <button type="submit" name="savePrintPat" id="savePrintPat" class="btn btn-default">
                                        Save Patient &amp; Print Receipt
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-labelledby="addDoc" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="addDoc">Add A doctor Details</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="respond.php?opt=saveNewDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" autocomplete="off" placeholder="Full Name of the doctor">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Organization: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" required="required" autocomplete="off" placeholder="Organization/Hospital name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" autocomplete="off" placeholder="Contact No of the doctor">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" name="saveDoc" id="saveDoc">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Assign patient tests - JK Diagnostics Admin Panel';
            break;
            
        // save the added patient tests
        case 'addPrintTestsForPatient':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($_SESSION['ASSIGNED_TESTS']) or !is_array($_SESSION['ASSIGNED_TESTS'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must assign tests for the patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['patid']) or ($submittedData['patid'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must have a patient ID to assign the tests';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $count = 0;
            DbOperations::getObject()->commitTransaction('start');
            
            foreach ($_SESSION['ASSIGNED_TESTS'] as $testIds) {
                $fetchSql = 'select test_id, test_price from test_list where test_id = "' . $testIds . '"';
                $patTest = DbOperations::getObject()->fetchData($fetchSql);
                $saveData = array(
                    $submittedData['patid'],
                    $patTest[0]['test_id'],
                    $patTest[0]['test_price'],
                    DBTIMESTAMP,
                    $_SESSION['UID']
                );
                $sql = 'insert into patient_tests (pat_id, pt_test_id, pt_price, pt_dttm, pt_created_by) values (?, ?, ?, ?, ?)';
                $insPatTest = DbOperations::getObject()->runQuery($sql, $saveData);
                if ($insPatTest) {
                    ++$count;
                }
            }
            $discount = 0;
            if ($submittedData['discVal'] !== '') {
                if ($submittedData['discType'] === 'R') {
                    $discount = floatval($submittedData['discVal']);
                } else {
                    $discount = (floatval($submittedData['totVal']) * floatval($submittedData['discVal'])/100);
                }
            }
            $calcs = array(
                'ptc_pat_id'            => $submittedData['patid'],
                'ptc_dr_id'             => $_POST['drname'],
                'ptc_tot_price'         => $submittedData['totVal'],
                'ptc_discount'          => ceil($discount),
                'ptc_payment_status'    => $submittedData['payStat'],
                'ptc_preced_no'         => $submittedData['preced'],
                'ptc_staff_id'          => $_SESSION['UID'],
                'ptc_dttm'              => DBTIMESTAMP
            );
            $insPatTestCalc = DbOperations::getObject()->insertData('patient_test_calculations', $calcs);
            if (($count === count($_SESSION['ASSIGNED_TESTS'])) and $insPatTestCalc) {
                unset($_SESSION['ASSIGNED_TESTS']);
                DbOperations::getObject()->commitTransaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'Successfully added and sent to printer';
                session_write_close();
                header("Location:patient.php?opt=printPatReceipt&patid={$submittedData['patid']}.&dttm=". strtotime(DBTIMESTAMP));
                exit(0);
            } else {
                DbOperations::getObject()->commitTransaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error in saving the test details';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            exit;
            break;
            
        // Edit tests for the patient present
        case 'editTestsAssigned':
            if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Could not find any patient ID';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($opt['receiptDttm']) or empty($opt['receiptDttm'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Could not find any patient time reference';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $patSql = 'select pid, p_name, p_addr, p_age_yr, p_age_month, p_age_day, p_phone, p_sex, p_created from patient_details where pid="' . $opt['patid'] . '"';
            $patData = DbOperations::getObject()->fetchData($patSql);
            $patTcSql = 'select ptc_id, ptc_dr_id, ptc_discount, ptc_payment_status, ptc_preced_no from patient_test_calculations where ptc_pat_id = "' . $opt['patid'] . '" and ptc_dttm = "' . date('Y-m-d H:i:s', $opt['receiptDttm']) . '"';
            $patTcData = DbOperations::getObject()->fetchData($patTcSql);
            if (!isset($_SESSION['ASSIGNED_TESTS']) or !  is_array($_SESSION['ASSIGNED_TESTS'])) {
                $patTestSql = 'select pt_test_id from patient_tests where pt_dttm = "' . date('Y-m-d H:i:s', $opt['receiptDttm']) . '" and pat_id = "' . $opt['patid'] . '"';
                $patTestData = DbOperations::getObject()->fetchData($patTestSql);
                $_SESSION['ASSIGNED_TESTS'] = array();
                foreach ($patTestData as $value) {
                    array_push($_SESSION['ASSIGNED_TESTS'], $value['pt_test_id']);
                }
            }
            if (count($patData) < 1) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No patient id found';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            ob_start();
            ?>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Add Tests for Patient</div>
                        <div class="panel-body">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient ID: </strong>&nbsp;<?php echo date('dmy', strtotime($patData[0]['p_created'])) . $patData[0]['pid']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Name: </strong>&nbsp;<?php echo $patData[0]['p_name']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Address: </strong>&nbsp;<?php echo $patData[0]['p_addr']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Age: </strong>&nbsp;
                                    <?php
                                    echo ($patData[0]['p_age_yr'] === '0' ? '' : $patData[0]['p_age_yr'] . '&nbsp;Yrs');
                                    echo ($patData[0]['p_age_month'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_month'] . '&nbsp;Months');
                                    echo ($patData[0]['p_age_day'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_day'] . '&nbsp;Days');
                                    ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Contact No: </strong>&nbsp;<?php echo $patData[0]['p_phone']; ?>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <strong>Patient Gender: </strong>&nbsp;<?php echo $patData[0]['p_sex']; ?>
                            </div>
                            
                            <form role="form" style="padding-top: 50px" id="testform" name="testform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=editPrintTestsForPatient">
                                <hr />
                                <div class="form-group">
                                    <label for="drname" class="col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label text-right">Referrer Dr. Name: </label>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" tabindex="1" autofocus="autofocus" id="drname" name="drname">
                                            <option value="">Select</option>
                                            <?php
                                            $dr = DbOperations::getObject()->fetchData('select dr_id, dr_name, dr_org from doctor_details order by dr_name');
                                            foreach ($dr as $drData):
                                            ?>
                                            <option<?php echo ($patTcData[0]['ptc_dr_id'] === $drData['dr_id'] ? ' selected="selected"' : ''); ?> value="<?php echo $drData['dr_id']; ?>"><?php echo $drData['dr_name'] . ($drData['dr_org'] ==='' ? '' : ' (' . $drData['dr_org'] .')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <button type="button" class="btn btn-info" id="addDoc" name="addDoc"  data-toggle="modal" data-target="#docModal">Add Doctor</button>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group" style="padding-top: 20px">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="cat" name="cat" onchange="loadTests();">
                                            <option value="">All Category</option>
                                            <?php
                                            $cat = DbOperations::getObject()->fetchData('select cat_id, cat_name from test_cats order by cat_name');
                                            foreach ($cat as $catData):
                                            ?>
                                            <option value="<?php echo $catData['cat_id']; ?>"><?php echo $catData['cat_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="subcat" name="subcat" onchange="loadTests();">
                                            <option value="">All Sub-Category</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select class="form-control" id="alphabets" name="alphabets" onchange="loadTests();">
                                            <option value="">All</option>
                                            <?php foreach(range('a','z') as $i): ?>
                                            <option value="<?php echo $i;?>"><?php echo strtoupper($i);?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <hr />
                                <hr/>
                                <div class="well well-sm" id="testList" style="height: 200px;overflow: auto">
                                        <?php
                                        $tests = DbOperations::getObject()->fetchData('select test_id, test_name, test_price from test_list order by test_name');
                                        foreach ($tests as $testData):
                                        ?>
                                        <div class="checkbox col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                            <label for="test_<?php echo $testData['test_id'];?>">
                                                <input type="checkbox" data-price="<?php echo $testData['test_price'];?>" name="test[]" id="test_<?php echo $testData['test_id'];?>"<?php if (isset($_SESSION['ASSIGNED_TESTS']) and (array_search($testData['test_id'], $_SESSION['ASSIGNED_TESTS']) !== false)) echo ' checked="checked"'; ?> value="<?php echo $testData['test_id']; ?>" title="<?php echo $testData['test_name']; ?>" class="testchk"> <?php echo $testData['test_name']; ?>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                </div>
                                <div id="testcal">
                                    <?php
                                    if (isset($_SESSION['ASSIGNED_TESTS']) and is_array($_SESSION['ASSIGNED_TESTS'])) {
                                        $testList = '';
                                        $count = 0;
                                        $sumTests = 0;
                                        foreach ($_SESSION['ASSIGNED_TESTS'] as $testId) {
                                            ++$count;
                                            $sql = 'select test_id, test_name, test_price from test_list where test_id = "' . $testId . '"';
                                            $tests = DbOperations::getObject()->fetchData($sql);
                                            if (is_array($tests) and (count($tests) > 0)) {
                                                $sumTests += (float)$tests[0]['test_price'];
                                                ?>
                                                <div class="alert alert-info alert-dismissable testLine" id="selTst_<?php echo $tests[0]['test_id'];?>" style="padding:10px;margin:2px">
                                                    <div class="row">
                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"><?php print($count);?></div>
                                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7"><?php echo $tests[0]['test_name'];?></div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right"><span class="fa fa-rupee"></span>&nbsp;<?php echo number_format((float)$tests[0]['test_price'], 2, '.', '');?></div>
                                                        <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                                            <button type="button" class="close" style="right:0" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Total Sum</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"><span class="fa fa-rupee"></span>
                                                <input type="text" id="totVal" name="totVal" class="no-disp" readonly="readonly" value="<?php echo number_format((float)$sumTests, 2, '.', '');?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 text-right">Apply Discount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right">
                                                <input type="text" id="discVal" name="discVal" class="form-control" value="<?php echo $patTcData[0]['ptc_discount']; ?>">
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 text-right">
                                                <select id="discType" name="discType" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="P">%</option>
                                                    <option selected="selected" value="R">Rs.</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3" style="padding-top:4px">
                                                <span class="fa fa-rupee"></span>
                                                <input type="text" id="totDiscVal" name="totDiscVal" class="no-disp" readonly="readonly" value="<?php echo $patTcData[0]['ptc_discount'].'.00'; ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Grand Total</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <span class="fa fa-rupee"></span>
                                                <input type="text" id="grTotVal" name="grTotVal" class="no-disp" readonly="readonly" value="<?php echo number_format((floatval($sumTests) - floatval($patTcData[0]['ptc_discount'])), 2, '.', '');?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Payment Status</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <select id="payStat" name="payStat" class="form-control">
                                                    <option<?php echo ($patTcData[0]['ptc_payment_status']==='1' ? ' selected="selected"' : ''); ?> value="1">Paid</option>
                                                    <option<?php echo ($patTcData[0]['ptc_payment_status']==='0' ? ' selected="selected"' : ''); ?> value="0">Credit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-9 text-right">Precedence No</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                                                <select id="preced" name="preced" class="form-control">
                                                    <option value="">0</option>
                                                    <?php
                                                    $prevNos = DbOperations::getObject()->fetchData('select distinct(ptc_preced_no) from patient_test_calculations where date(ptc_dttm) = "' . DBDATE . '" and ptc_pat_id <> "' . $opt['patid'] . '"');
                                                    $preserved = array();
                                                    foreach ($prevNos as $val) {
                                                        array_push($preserved, intval($val[0]));
                                                    }
                                                    array_unique($preserved);
                                                    for ($i = 1; $i < 51; ++$i) {
                                                        if (array_search($i, $preserved) === false) {
                                                    ?>
                                                    <option<?php echo (intval($patTcData[0]['ptc_preced_no'])===$i ? ' selected="selected"' : ''); ?> value="<?php print($i); ?>"><?php print($i); ?></option>
                                                    <?php
                                                        } else {
                                                            continue;
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="hidden" id="patid" name="patid" value="<?php print($opt['patid']); ?>">
                                    <input type="hidden" id="rcptDttm" name="rcptDttm" value="<?php print($opt['receiptDttm']); ?>">
                                    <button type="submit" name="savePrintPat" id="savePrintPat" class="btn btn-default">
                                        Save Patient Tests Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-labelledby="addDoc" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="addDoc">Add A doctor Details</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="respond.php?opt=saveNewDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" autocomplete="off" placeholder="Full Name of the doctor">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Organization: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" required="required" autocomplete="off" placeholder="Organization/Hospital name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" autocomplete="off" placeholder="Contact No of the doctor">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" name="saveDoc" id="saveDoc">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Assign patient tests - JK Diagnostics Admin Panel';
            break;
            
        // save the added patient tests
        case 'editPrintTestsForPatient':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($_SESSION['ASSIGNED_TESTS']) or !is_array($_SESSION['ASSIGNED_TESTS'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must assign tests for the patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['patid']) or ($submittedData['patid'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must have a patient ID to assign the tests';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['rcptDttm']) or ($submittedData['rcptDttm'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must have a patient tests date time to assign the tests';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $count = 0;
            DbOperations::getObject()->commitTransaction('start');
            $testDelSql = 'delete from patient_tests where pat_id = ? and pt_dttm = ?';
            $delTests = DbOperations::getObject()->runQuery($testDelSql, array($submittedData['patid'], date('Y-m-d H:i:s', $submittedData['rcptDttm'])));
            $calcDelSql = 'delete from patient_test_calculations where ptc_pat_id = ? and ptc_dttm = ?';
            $calcDelTests = DbOperations::getObject()->runQuery($testDelSql, array($submittedData['patid'], date('Y-m-d H:i:s', $submittedData['rcptDttm'])));
            if ($delTests and $calcDelTests) {
				foreach ($_SESSION['ASSIGNED_TESTS'] as $testIds) {
					$fetchSql = 'select test_id, test_price from test_list where test_id = "' . $testIds . '"';
					$patTest = DbOperations::getObject()->fetchData($fetchSql);
					$saveData = array(
						$submittedData['patid'],
						$patTest[0]['test_id'],
						$patTest[0]['test_price'],
						date('Y-m-d H:i:s', $submittedData['rcptDttm']),
						$_SESSION['UID']
					);
					$sql = 'insert into patient_tests (pat_id, pt_test_id, pt_price, pt_dttm, pt_created_by) values (?, ?, ?, ?, ?)';
					$insPatTest = DbOperations::getObject()->runQuery($sql, $saveData);
					if ($insPatTest) {
						++$count;
					}
				}
				$discount = 0;
				if ($submittedData['discVal'] !== '') {
					if ($submittedData['discType'] === 'R') {
						$discount = floatval($submittedData['discVal']);
					} else {
						$discount = (floatval($submittedData['totVal']) * floatval($submittedData['discVal'])/100);
					}
				}
				$calcs = array(
					'ptc_pat_id'            => $submittedData['patid'],
					'ptc_dr_id'             => $_POST['drname'],
					'ptc_tot_price'         => $submittedData['totVal'],
					'ptc_discount'          => ceil($discount),
					'ptc_payment_status'    => $submittedData['payStat'],
					'ptc_preced_no'         => $submittedData['preced'],
					'ptc_staff_id'          => $_SESSION['UID'],
					'ptc_dttm'              => date('Y-m-d H:i:s', $submittedData['rcptDttm'])
				);
				$insPatTestCalc = DbOperations::getObject()->insertData('patient_test_calculations', $calcs);
				if (($count === count($_SESSION['ASSIGNED_TESTS'])) and $insPatTestCalc) {
					unset($_SESSION['ASSIGNED_TESTS']);
					DbOperations::getObject()->commitTransaction('on');
					$_SESSION['STATUS'] = 'success';
					$_SESSION['MSG']    = 'Successfully edited a patient data';
					session_write_close();
					header("Location:index.php");
					exit(0);
				} else {
					DbOperations::getObject()->commitTransaction('rollback');
					$_SESSION['STATUS'] = 'error';
					$_SESSION['MSG']    = 'Error in saving the editing details';
					session_write_close();
					header("Location:" . $_SERVER['HTTP_REFERER']);
					exit(0);
				}
			} else {
				DbOperations::getObject()->commitTransaction('rollback');
				$_SESSION['STATUS'] = 'error';
				$_SESSION['MSG']    = 'Error in saving the editing details : unable to delete previous data';
				session_write_close();
				header("Location:" . $_SERVER['HTTP_REFERER']);
				exit(0);
			}
            break;
            
        case 'deleteTests':
            if (!isset($opt['patid']) or ($opt['patid'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must have a patient ID to delete the tests';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($opt['dttm']) or ($opt['dttm'] === '')) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must have a patient tests date time to delete the tests';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            //die(date('Y-m-d H:i:s', $opt['dttm']));
            DbOperations::getObject()->commitTransaction('start');
            $testDelSql = 'delete from patient_tests where pat_id = ? and pt_dttm = ?';
            $delTests = DbOperations::getObject()->runQuery($testDelSql, array($opt['patid'], date('Y-m-d H:i:s', $opt['dttm'])));
            $testcalcDelSql = 'delete from patient_test_calculations where ptc_pat_id = ? and ptc_dttm = ?';
            $delcalcTests = DbOperations::getObject()->runQuery($testcalcDelSql, array($opt['patid'], date('Y-m-d H:i:s', $opt['dttm'])));
            if ($delTests and $delcalcTests) {
                DbOperations::getObject()->commitTransaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'Successfully deleted a patient test data';
                session_write_close();
                header("Location:index.php");
                exit(0);
            } else {
                DbOperations::getObject()->commitTransaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error in deleting the patient test details';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            exit;
            break;
        case 'printPatReceipt':
            
            if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Could not find any patient ID';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($opt['dttm']) or empty($opt['dttm'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Could not find any patient time reference';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $patSql = 'select pid, p_name, p_addr, p_age_yr, p_age_month, p_age_day, p_phone, p_sex, p_created from patient_details where pid="' . $opt['patid'] . '"';
            $patData = DbOperations::getObject()->fetchData($patSql);
            $patTcSql = 'select ptc_id, dr_name, ptc_tot_price, ptc_discount, ptc_payment_status, ptc_preced_no, ptc_dttm from patient_test_calculations left join doctor_details on ptc_dr_id = dr_id where ptc_pat_id = "' . $opt['patid'] . '" and ptc_dttm = "' . date('Y-m-d H:i:s', $opt['dttm']) . '"';//die($patTcSql);
            $patTcData = DbOperations::getObject()->fetchData($patTcSql);
            $patTestSql = 'select test_name, pt_price from patient_tests, test_list where test_id = pt_test_id and pt_dttm = "' . date('Y-m-d H:i:s', $opt['dttm']) . '" and pat_id = "' . $opt['patid'] . '"';
            $patTestData = DbOperations::getObject()->fetchData($patTestSql);
            $serialNo = ($patTcData[0]['ptc_preced_no'] !== '0' ? '<span class="serial-round">' . $patTcData[0]['ptc_preced_no'] . '</span>' : '<span class="fake-serial-space"></span>');
            ob_start();
            ?>
            <div class="panel-body">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Patient ID : 
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php echo date('dmy', strtotime($patData[0]['p_created'])) . $patData[0]['pid']; ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Date : 
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php echo date('d-m-Y', $opt['dttm']); ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Patient Name : 
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php echo $patData[0]['p_name']; ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Contact No. : 
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php echo $patData[0]['p_phone']; ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <strong>Age : </strong>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php
                    echo ($patData[0]['p_age_yr'] === '0' ? '' : $patData[0]['p_age_yr'] . '&nbsp;Yrs');
                    echo ($patData[0]['p_age_month'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_month'] . '&nbsp;Months');
                    echo ($patData[0]['p_age_day'] === '0' ? '' : '&nbsp;' . $patData[0]['p_age_day'] . '&nbsp;Days');
                    ?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Sex : 
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <?php echo ($patData[0]['p_sex'] === 'M' ? 'Male' : 'Female');?>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Patient Address : 
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    <?php echo $patData[0]['p_addr'];?>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    Ref. Doctor : 
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                    <?php echo $patTcData[0]['dr_name'];?>
                </div>
            </div>

            <table class="table table-squeezed">
                <thead>
                    <tr>
                        <th class="col-lg-9 col-md-9 col-sm-9 col-xs-9">Test Name</th>
                        <th class="col-lg-3 col-md-3 col-sm-3 col-xs-3 text-right">Test Price (<span class="fa fa-rupee"></span>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patTestData as $tests) { ?>
                    <tr>
                        <td><?php echo $tests['test_name']; ?></td>
                        <td class="text-right"><?php echo number_format(floatVal($tests['pt_price']), 2, '.', ','); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right">Total (<span class="fa fa-rupee"></span>) : </td>
                        <td class="text-right"><?php echo number_format(floatVal($patTcData[0]['ptc_tot_price']), 2, '.', ','); ?></td>
                    </tr>
                    <tr>
                        <td class="text-right">Grand Total (<?php echo ($patTcData[0]['ptc_payment_status'] === '1' ? 'Paid' : 'Credit'); ?>) (<span class="fa fa-rupee"></span>) : </td>
                        <td class="text-right">
                            <?php echo number_format((floatVal($patTcData[0]['ptc_tot_price']) - floatval($patTcData[0]['ptc_discount'])), 2, '.', ','); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            
            <?php
            // get contents from buffer
            $contents                           = ob_get_contents();
            // clean and end the buffer
            ob_end_clean();

            $replacementArray                   = array(
                'PageTitle'                     => 'Print Patient Reciept Copy - JK Diagnostics',
                'SerialNumber'                 => $serialNo,
                'CenterContents'                => $contents,
                'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'custom.min.css'),
                'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'custom.min.js')
            );

            assignTemplate($replacementArray, 'printTemplate.php');
            exit(0);
            break;
        // show the form to edit the selected employee
        case 'edit':
            if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No patient data found';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sql = 'select pid, p_name, p_addr, p_age_yr, p_age_month, p_age_day, p_phone, p_sex from patient_details where pid = "' . $opt['patid'] . '"';
            $pData = DbOperations::getObject()->fetchData($sql);
            ob_start();
            ?>
            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit Patient Details</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="patform" name="patform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedPatient">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="pname" name="pname" required="required" autocomplete="off" placeholder="Full Name" value="<?php echo $pData[0]['p_name']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="addr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Address: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"><?php echo $pData[0]['p_addr']; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ageyr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Age: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageyr" required="required" autocomplete="off" name="ageyr" placeholder="Yrs" value="<?php echo $pData[0]['p_age_yr']; ?>">
                                                    <span class="input-group-addon">Years</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="agemonth" required="required" autocomplete="off" name="agemonth" placeholder="Months" value="<?php echo $pData[0]['p_age_month']; ?>">
                                                    <span class="input-group-addon">Months</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageday" required="required" autocomplete="off" name="ageday" placeholder="Days" value="<?php echo $pData[0]['p_age_day']; ?>">
                                                    <span class="input-group-addon">Days</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No.: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" autocomplete="off" name="contact" placeholder="Contact Number" value="<?php echo $pData[0]['p_phone']; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="rpass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Sex: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="sex" name="sex">
                                            <option<?php echo ($pData[0]['p_sex'] === 'F' ? ' selected="selected"' : ''); ?> value="F">Female</option>
                                            <option<?php echo ($pData[0]['p_sex'] === 'M' ? ' selected="selected"' : ''); ?> value="M">Male</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="hidden" name="patid" id="patid" value="<?php echo $pData[0]['pid']; ?>">
                                        <button type="submit" name="savePat" id="savePat" class="btn btn-default">Save Patient</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle              = 'Edit Patient Details - JK Diagnostics Admin Panel';
            break;
            
        // TO SAVE EDITED PATIENT
        case 'saveEditedPatient':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['pname']) or empty($submittedData['pname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient name';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient address';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['ageyr']) or is_nan($submittedData['ageyr']) or is_nan($submittedData['agemonth']) or is_nan($submittedData['ageday'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter patient age';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['contact']) or is_nan($submittedData['contact'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee valid contact number';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            
            // make a data array to be saves as in database table
            $saveData = array(
                ucwords($submittedData['pname']),
                $submittedData['addr'],
                $submittedData['ageyr'],
                $submittedData['agemonth'],
                $submittedData['ageday'],
                $submittedData['contact'],
                $submittedData['sex'],
                DBTIMESTAMP,
                $_SESSION['UID'],
                $submittedData['patid']
            );
            // start a transaction with database
            DbOperations::getObject()->commitTransaction('start');
            $sql = 'update patient_details set p_name = ?, p_addr = ?, p_age_yr = ?, p_age_month = ?, p_age_day = ?, p_phone = ?, p_sex = ?, p_last_edited = ?, p_last_edited_by = ? where pid = ?';
            // update the data
            $success                = DbOperations::getObject()->runQuery($sql, $saveData);
            if ($success) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->commitTransaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully edited a patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->commitTransaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while editing a patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
            
        case 'del':
             if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No patient data found';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->commitTransaction('start');
            $sql                    = 'delete from patient_details where pid = ?';
            $tsql                   = 'delete from patient_tests where pat_id = ?';
            $tcsql                  = 'delete from patient_test_calculations where ptc_pat_id = ?';
            // update the data
            $success                = DbOperations::getObject()->runQuery($sql, array($opt['patid']));
            $tsuccess               = DbOperations::getObject()->runQuery($tsql, array($opt['patid']));
            $tcsuccess              = DbOperations::getObject()->runQuery($tcsql, array($opt['patid']));
            
            if ($success and $tsuccess and $tcsuccess) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->commitTransaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully deleted a patient and the assigned tests till date';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->commitTransaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while deleting a patient';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        
        default :
            
            break;
    }
} else {
    
}
// get contents from buffer
$contents                           = ob_get_contents();
// clean and end the buffer
ob_end_clean();

$replacementArray                   = array(
    'PageTitle'                     => $pageTitle,
    'ErrorMessages'                 => getAlertMsg(),
    'CenterContents'                => $contents,
    'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'jquery.dataTables.min.css', 'custom.min.css'),
    'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'bootstrap-typeahead.min.js', 'jquery.dataTables.min.js', 'custom.min.js')
);

assignTemplate($replacementArray);
// the ending php tag has been intentionally not used to avoid unwanted whitespaces before document starts
