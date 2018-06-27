<?php

// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
if (isLogged() !== '1'){redirectUser();}
// extract get data to get option by user
$opt = DataFilter::getObject()->cleanData($_GET);

if (isset($opt['opt']) and ! empty($opt['opt'])) {
    switch ($opt['opt']) {
        // show add new employee form
        case 'newPatient':
            ob_start();
            ?>
            <div class="col-xs-12">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Add New Patient</div>
                        <div class="panel-body">
                            <form class="form-horizontal ajaxfrm" role="form" id="patform" name="patform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveNewPatient">
                                <div class="form-group">
                                    <label for="name" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Date &amp; Time :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <div class="input-group datetimepicker">
                                        <input type="text" class="form-control" id="pdate" name="pdate" autocomplete="off" placeholder="Date &amp; Time of Test">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <label for="pin" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Patient Type :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="ptype" id="pin" value="IP" onchange="if ($(this).prop('checked') === true) $('#patid, #billno').removeAttr('readonly');"> Indoor
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="ptype" id="pout" value="OP" checked="checked" onchange="if ($(this).prop('checked') === true) $('#patid, #billno').attr('readonly', 'readonly');"> Outdoor
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patid" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Patient ID :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="patid" name="patid" autocomplete="off" placeholder="Patient ID" readonly="readonly">
                                    </div>
                                    <label for="billno" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Bill No :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="billno" name="billno" autocomplete="off" placeholder="Patient Bill No." readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Name :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="pname" name="pname" autofocus="autofocus" required="required" autocomplete="off" placeholder="Full Name">
                                    </div>
                                    <label for="addr" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Address :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ageyr" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Age :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageyr" required="required" autocomplete="off" name="ageyr" placeholder="Yrs" value="0" min="0">
                                                    <span class="input-group-addon">Yrs</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="agemonth" required="required" autocomplete="off" name="agemonth" placeholder="Months" value="0" min="0">
                                                    <span class="input-group-addon">Mons</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageday" required="required" autocomplete="off" name="ageday" placeholder="Days" value="0" min="0">
                                                    <span class="input-group-addon">Days</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <label for="contact" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Contact No. :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" autocomplete="off" name="contact" placeholder="Contact Number">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sexm" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Sex :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sexm" value="M" checked="checked"> Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sexf" value="F"> Female
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sext" value="T"> Transgender
                                        </label>
                                    </div>
                                    <label for="drname" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Consultant : </label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="drname" name="drname">
                                            <?php
                                            $sql = 'select dr_id, dr_name, dr_org from doctor_details order by dr_name';
                                            $dr = DbOperations::getObject()->fetchData($sql);
                                            foreach ($dr as $drData):
                                            ?>
                                            <option<?php if ($drData['dr_id']==='177') echo ' selected="selected"'; ?> value="<?php echo $drData['dr_id']; ?>"><?php echo $drData['dr_name'] . ($drData['dr_org'] ==='' ? '' : ' (' . $drData['dr_org'] .')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <a href="javascript:void(0)" class="help-block" data-toggle="modal" data-target="#docModal">Not in the list ? Add Doctor.</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cat" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Select Tests : </label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
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
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12 col-lg-offset-2 col-md-offset-4 col-sm-offset-5">
                                        <select class="form-control" id="alphabets" name="alphabets" onchange="loadTests();">
                                            <option value="">All</option>
                                            <?php foreach(range('a','z') as $i): ?>
                                            <option value="<?php echo $i;?>"><?php echo strtoupper($i);?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group well well-sm" id="testList" style="height: 150px;overflow: auto">
                                    <?php
                                    $sql = 'select test_id, cat_name, test_name, test_price, test_oprice'
                                        . ' from test_list left join test_cats on under_cat = cat_id order by test_name';
                                    $tests = DbOperations::getObject()->fetchData($sql);
                                    foreach ($tests as $testData):
                                    ?>
                                    <label for="test_<?php echo $testData['test_id'];?>" class="checkbox col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                        <input type="checkbox" data-ip-price="<?php echo $testData['test_price'];?>" data-op-price="<?php echo $testData['test_oprice'];?>" data-test-name="<?php echo $testData['cat_name'] . ' - ' . $testData['test_name']; ?>" name="test[]" id="test_<?php echo $testData['test_id']; ?>" value="<?php echo $testData['test_id']; ?>" title="<?php echo $testData['test_name']; ?>" class="testchk"> <?php echo $testData['cat_name'] . ' - ' . $testData['test_name']; ?>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                                <input type="hidden" name="tid" id="tid" value="">
                                <input type="hidden" name="totpr" id="totpr" value="">
                                <div class="form-group" id="testcal"></div>
                                <div class="form-group" id="testtotsdiscs"></div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6" for="disc">Apply Discount : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="disc" id="disc" class="form-control text-right" value="0" onblur="updateTotPrice();" placeholder="Discount Amount">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6" for="discrem">Remarks : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="discrem" id="discrem" class="form-control" value="" placeholder="Discount Remarks">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-5 col-md-3 col-sm-6 col-xs-12 help-block text-right">
                                        To be filled only if paid by debit/credit card
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="trid" id="trid" class="form-control" placeholder="Transaction ID">
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="invnum" id="invnum" class="form-control" placeholder="Invoice/Reference Num">
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="cardamt" id="cardamt" class="form-control text-right" value="0" placeholder="Paid Amount" onblur="updateTotPrice();">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6" for="grtot">Grand Total : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" name="grtot" id="grtot" class="form-control text-right" value="" readonly="readonly" placeholder="Gr.Amount Auto Calculated">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6" for="credval">Credit : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="credval" id="credval" class="form-control text-right" value="0" onblur="updateTotPrice();" placeholder="Credit Amount">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-offset-3 col-md-offset-3" for="paid">Paid : <span id="paidstr"></span></label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" name="paid" id="paid" class="form-control text-right" value="" readonly="readonly" placeholder="Paid Amt. Auto Calculated">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-9 col-md-offset-9 col-sm-offset-7 col-lg-3 col-md-3 col-sm-5 col-xs-12">
                                        <button type="submit" name="savePat" id="savePat" class="btn btn-primary"><i class="fa fa-save"></i> Save Data</button>&nbsp;
                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="window.location.reload();"><i class="fa fa-refresh"></i> Reset Form</a>
                                    </div>
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
                            <h4 class="modal-title" id="addDoc">Add Doctor Details</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="respond.php?opt=saveNewDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Name : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" placeholder="Full Name of the doctor (without prefix Dr.)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Organization : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" placeholder="Organization/Hospital name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Contact No : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" placeholder="Contact No of the doctor">
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
            $pageTitle = 'Add a new patient - JK Imaging Admin Dashboard';
            break;

        // save the employee data posted from the new employee form
        case 'saveNewPatient':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['ptype']) or empty($submittedData['ptype'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must select patient type'
                ]));
            }
            if (!isset($submittedData['pname']) or empty($submittedData['pname'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient name'
                ]));
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient address'
                ]));
            }
            if (!isset($submittedData['ageyr']) or is_nan($submittedData['ageyr']) or is_nan($submittedData['agemonth']) or is_nan($submittedData['ageday'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient age'
                ]));
            }
            if (!isset($submittedData['contact']) or empty($submittedData['contact'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter employee valid contact number'
                ]));
            }
            if (!isset($submittedData['tid']) or empty($submittedData['tid'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'Please select tests for the patient'
                ]));
            }
            if ((intval($submittedData['disc']) > 0) and empty($submittedData['discrem'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'Please enter remarks / discount details'
                ]));
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // insert the data
            DbOperations::getObject()->buildInsertQuery('patient_data');
            // make a data array to be saves as in database table
            $patData = [
                null,
                ucwords($submittedData['pname']),
                ucwords($submittedData['addr']),
                $submittedData['ageyr'],
                $submittedData['agemonth'],
                $submittedData['ageday'],
                $submittedData['contact'],
                $submittedData['sex'],
                DBTIMESTAMP,
                $_SESSION['UID'],
            ];
            $pid = DbOperations::getObject()->runQuery($patData);
            DbOperations::getObject()->buildInsertQuery('patient_test_calculations');
            $patSpData = [
                null,
                $pid,
                $submittedData['patid'],
                $submittedData['billno'],
                ((isset($submittedData['pdate']) and !empty($submittedData['pdate'])) ?
                    date('Y-m-d H:i:s', strtotime($submittedData['pdate'])) :
                    DBTIMESTAMP),
                $submittedData['ptype'],
                $submittedData['drname'],
                $submittedData['totpr'],
                $submittedData['disc'],
                $submittedData['discrem'],
                $submittedData['trid'],
                $submittedData['invnum'],
                $submittedData['cardamt'],
                $submittedData['credval'],
                $_SESSION['UID'],
                DBTIMESTAMP,
                1
            ];
            $pspid = DbOperations::getObject()->runQuery($patSpData);
            DbOperations::getObject()->buildInsertQuery('patient_tests');
            //$tsts = explode(',', $submittedData['tid']);
            $alltstCount = count($submittedData['tstid']);
            $tsucCount = 0;
            foreach ($submittedData['tstid'] as $tk => $tst) {
                if (!empty($tst) and isset($submittedData['tstopr'][$tk])) {
                    $tstArr = [
                        null, $pid, $tst, $submittedData['tstpr'][$tk], $submittedData['tstopr'][$tk],
                        ((isset($submittedData['pdate']) and !empty($submittedData['pdate'])) ?
                            date('Y-m-d H:i:s', strtotime($submittedData['pdate'])) :
                            DBTIMESTAMP),
                        $_SESSION['UID'], 1
                    ];
                    $tsuc = DbOperations::getObject()->runQuery($tstArr);
                    if ($tsuc !== false) {
                        ++$tsucCount;
                    }
                }
            }
            //$success = DbOperations::getObject()->runQuery($saveData);
            if (($pid !== false) and ($pspid !== false) and ($alltstCount === $tsucCount)) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = 'Successfully added a patient data';
                session_write_close();
                die(json_encode([
                    'status' => 'success',
                    'msg' => 'You have successfully added a new patient.',
                    'url' => 'patient.php?opt=printPatReceipt&patid=' . $pid
                ]));
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                die(json_encode([
                    'status' => 'success',
                    'msg' => 'Error occured while adding a patient'
                ]));
            }
            break;
        
            
        case 'showPatDetDateSpecific':
            $pageTitle = 'View Patient Details - JK Imaging User Dashboard';
            $patSql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex,staff_name,'
                . ' pcreated from patient_data left join staff_users on staff_id = pcreatedby where pid= ?';
            $patData = DbOperations::getObject()->fetchData($patSql, [$opt['patid']]);
            $patTcSql = 'select ptc_id, dr_name, dr_org, ptc_tot_price, ptc_discount, ptc_credit, ptc_disc_remark,'
                . ' ptc_hosp_pid, ptc_dttm, ptc_hosp_billno, ptc_card_tid, ptc_card_ref, ptc_card_amt, ptc_hosp_pattype '
                . ' from patient_test_calculations left join doctor_details on ptc_dr_id = dr_id '
                . 'where ptc_pat_id = ? and ptc_status = ?';//die($patTcSql);
            $patTcData = DbOperations::getObject()->fetchData($patTcSql, [$opt['patid'], 1]);
            $patTestSql = 'select cat_name, test_name, pt_oprice '
                . 'from patient_tests left join test_list on test_id = pt_test_id '
                . 'left join test_cats on under_cat = cat_id '
                . 'where pat_id = ? and pt_status = ?';
            $patTestData = DbOperations::getObject()->fetchData($patTestSql, [$opt['patid'], 1]);
            if ($patTcData[0]['ptc_hosp_pattype'] === 'OP') {
                //$opSerNo = DbOperations::getObject()->fetchData('select count(ptc_pat_id) as totpatcount from patient_test_calculations where ptc_hosp_pattype = ? and ptc_id < ?', ['OP', $patTcData[0]['ptc_id']]);
                //$rcptNo = (intval($opSerNo[0]['totpatcount'])+1);
                $patType = 'OP-';
            } else {
                //$rcptNo = $patTcData[0]['ptc_id'];
                $patType = 'IP-';
            }
			$rcptNo = $patTcData[0]['ptc_id'];
            switch (strlen($rcptNo)) {
                case 1:
                    $rcptNo = '000' . $rcptNo;
                    break;
                case 2:
                    $rcptNo = '00' . $rcptNo;
                    break;
                case 3:
                    $rcptNo = '0' . $rcptNo;
                    break;
                default :
                    break;
            }
            $rcptNo = $patType . $rcptNo;
            ob_start();
            ?>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 col-lg-offset-1 col-md-offset-1 col-sm-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">View Patient details</div>
                <table class="table table-bordered table-squeezed">
                <tbody>
                    <tr>
                        <th>Bill No. : </th>
                        <td><?php echo (($patTcData[0]['ptc_hosp_billno'] === '') ? $rcptNo : $patTcData[0]['ptc_hosp_billno']); ?></td>
                        <th>Patient ID : </th>
                        <td><?php echo (($patTcData[0]['ptc_hosp_pid'] === '') ? date('dmy', strtotime($patData[0]['pcreated'])) . $patData[0]['pid'] : $patTcData[0]['ptc_hosp_pid']); ?></td>
                    </tr>
                    <tr>
                        <th>Patient : </th>
                        <td colspan="3">
                            <?php 
                            echo $patData[0]['pname'] . ' (';
                            echo (($patData[0]['pagey'] === '0') ? '' : $patData[0]['pagey'] . '&nbsp;Yrs');
                            echo (($patData[0]['pagem'] === '0') ? '' : '&nbsp;' . $patData[0]['pagem'] . '&nbsp;Months');
                            echo (($patData[0]['paged'] === '0') ? '' : '&nbsp;' . $patData[0]['paged'] . '&nbsp;Days');
                            echo ' / ' . $patData[0]['psex'] . ')';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Contact : </th>
                        <td><?php echo $patData[0]['pcont']; ?></td>
                        <th>Consultant : </th>
                        <td>
                            Dr.<?php echo $patTcData[0]['dr_name'] . (($patTcData[0]['dr_org'] !== '') ? ' (' . $patTcData[0]['dr_org'] . ')' : '');?>
                        </td>
                    </tr>
                    <tr>
                        <th>Patient Address : </th>
                        <td colspan="3"><?php echo $patData[0]['paddr'];?></td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-squeezed" style="border-top:#ccc 1px solid">
                <thead>
                    <tr>
                        <th style="width:80%">Test Name</th>
                        <th>Test Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patTestData as $tests) { ?>
                    <tr>
                        <td><?php echo $tests['cat_name'] . ' - ' . $tests['test_name']; ?></td>
                        <td class="text-right"><i class="fa fa-rupee"></i>&nbsp;<?php echo number_format(floatVal($tests['pt_oprice']), 2, '.', ','); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot style="border-top:2px #111 solid">
                    <tr>
                        <td class="text-right">
                            Total : 
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_tot_price']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php if ($patTcData[0]['ptc_discount'] !== '0') { ?>
                    <tr>
                        <td class="text-right">
                            Discount (<?php echo $patTcData[0]['ptc_disc_remark']; ?>) : 
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_discount']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if ($patTcData[0]['ptc_card_amt'] !== '0') { ?>
                    <tr>
                        <td class="text-right">
                            <i class="fa fa-credit-card"></i> Tr.ID : <?php echo $patTcData[0]['ptc_card_tid'];?>&nbsp;|&nbsp;
                            Ref.No. : <?php echo $patTcData[0]['ptc_card_ref'];?> :
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_card_amt']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th class="text-right">Grand Total : </th>
                        <th class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php
                            $grtot = (floatVal($patTcData[0]['ptc_tot_price']) - floatval($patTcData[0]['ptc_discount']));
                            echo number_format($grtot, 2, '.', ',');
                            ?>
                        </th>
                    </tr>
                    <?php if ($patTcData[0]['ptc_credit'] !== '0') { ?>
                    <tr>
                        <td class="text-right">
                            <i class="fa fa-exclamation-triangle"></i> Credit : 
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_credit']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th class="text-right">
                            Paid :&nbsp;
                            <?php
                            $paid = (floatVal($grtot) - floatVal($patTcData[0]['ptc_credit']));
                            //echo number_format((floatVal($grtot) - floatVal($patTcData[0]['ptc_credit'])), 2, '.', ',');
                            if ($patTcData[0]['ptc_card_amt'] !== '0') {
                                echo '<i class="fa fa-credit-card"></i> ' . number_format(floatVal($patTcData[0]['ptc_card_amt']), 2, '.', ',');
                                if (floatVal($patTcData[0]['ptc_card_amt']) !== $paid) {
                                    echo ' + <i class="fa fa-money"></i> ' . number_format(($paid - floatVal($patTcData[0]['ptc_card_amt'])), 2, '.', ',') . ' = ';
                                } else {
                                    echo ' = ';
                                }
                            } else {
                                echo '';
                            }
                            ?>
                        </th>
                        <th class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format($paid, 2, '.', ','); ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
            <?php
            break;
        case 'printPatReceipt':
            
            if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Could not find any patient ID';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $patSql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex,staff_name,'
                . ' pcreated from patient_data left join staff_users on staff_id = pcreatedby where pid= ?';
            $patData = DbOperations::getObject()->fetchData($patSql, [$opt['patid']]);
            $patTcSql = 'select ptc_id, dr_name, dr_org, ptc_tot_price, ptc_discount, ptc_credit, ptc_disc_remark,'
                . ' ptc_hosp_pid, ptc_dttm, ptc_hosp_billno, ptc_card_tid, ptc_card_ref, ptc_card_amt, ptc_hosp_pattype '
                . ' from patient_test_calculations left join doctor_details on ptc_dr_id = dr_id '
                . 'where ptc_pat_id = ? and ptc_status = ?';//die($patTcSql);
            $patTcData = DbOperations::getObject()->fetchData($patTcSql, [$opt['patid'], 1]);
            $patTestSql = 'select cat_name, test_name, pt_oprice '
                . 'from patient_tests left join test_list on test_id = pt_test_id '
                . 'left join test_cats on under_cat = cat_id '
                . 'where pat_id = ? and pt_status = ?';
            $patTestData = DbOperations::getObject()->fetchData($patTestSql, [$opt['patid'], 1]);
            if ($patTcData[0]['ptc_hosp_pattype'] === 'OP') {
                //$opSerNo = DbOperations::getObject()->fetchData('select count(ptc_pat_id) as totpatcount from patient_test_calculations where ptc_hosp_pattype = ? and ptc_id < ?', ['OP', $patTcData[0]['ptc_id']]);
                //$rcptNo = (intval($opSerNo[0]['totpatcount'])+1);
                $patType = 'OP-';
            } else {
                //$rcptNo = $patTcData[0]['ptc_id'];
                $patType = 'IP-';
            }
			$rcptNo = $patTcData[0]['ptc_id'];
            switch (strlen($rcptNo)) {
                case 1:
                    $rcptNo = '000' . $rcptNo;
                    break;
                case 2:
                    $rcptNo = '00' . $rcptNo;
                    break;
                case 3:
                    $rcptNo = '0' . $rcptNo;
                    break;
                default :
                    break;
            }
            $rcptNo = $patType . $rcptNo;
            ob_start();
            ?>
            <table class="table table-bordered table-squeezed">
                <tbody>
                    <tr>
                        <th>Bill No. : </th>
                        <td><?php echo (($patTcData[0]['ptc_hosp_billno'] === '') ? $rcptNo : $patTcData[0]['ptc_hosp_billno']); ?></td>
                        <th>Patient ID : </th>
                        <td><?php echo (($patTcData[0]['ptc_hosp_pid'] === '') ? date('dmy', strtotime($patData[0]['pcreated'])) . $patData[0]['pid'] : $patTcData[0]['ptc_hosp_pid']); ?></td>
                    </tr>
                    <tr>
                        <th>Patient : </th>
                        <td colspan="3">
                            <?php 
                            echo $patData[0]['pname'] . ' (';
                            echo (($patData[0]['pagey'] === '0') ? '' : $patData[0]['pagey'] . '&nbsp;Yrs');
                            echo (($patData[0]['pagem'] === '0') ? '' : '&nbsp;' . $patData[0]['pagem'] . '&nbsp;Months');
                            echo (($patData[0]['paged'] === '0') ? '' : '&nbsp;' . $patData[0]['paged'] . '&nbsp;Days');
                            echo ' / ' . $patData[0]['psex'] . ')';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Contact : </th>
                        <td><?php echo $patData[0]['pcont']; ?></td>
                        <th>Consultant : </th>
                        <td>
                            Dr.<?php echo $patTcData[0]['dr_name'] . (($patTcData[0]['dr_org'] !== '') ? ' (' . $patTcData[0]['dr_org'] . ')' : '');?>
                        </td>
                    </tr>
                    <tr>
                        <th>Patient Address : </th>
                        <td colspan="3"><?php echo $patData[0]['paddr'];?></td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-squeezed" style="border-top:#ccc 1px solid">
                <thead>
                    <tr>
                        <th style="width:80%">Test Name</th>
                        <th>Test Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patTestData as $tests) { ?>
                    <tr>
                        <td><?php echo $tests['cat_name'] . ' - ' . $tests['test_name']; ?></td>
                        <td class="text-right"><i class="fa fa-rupee"></i>&nbsp;<?php echo number_format(floatVal($tests['pt_oprice']), 2, '.', ','); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot style="border-top:2px #111 solid">
                    <tr>
                        <td class="text-right">
                            Total : 
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_tot_price']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php if ($patTcData[0]['ptc_discount'] !== '0') { ?>
                    <tr>
                        <td class="text-right">
                            Discount (<?php echo $patTcData[0]['ptc_disc_remark']; ?>) :
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_discount']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    
                    <tr>
                        <th class="text-right">Grand Total : </th>
                        <th class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php
                            $grtot = (floatVal($patTcData[0]['ptc_tot_price']) - floatval($patTcData[0]['ptc_discount']));
                            echo number_format($grtot, 2, '.', ',');
                            ?>
                        </th>
                    </tr>
                    <?php if ($patTcData[0]['ptc_credit'] !== '0') { ?>
                    <tr>
                        <td class="text-right">
                            <i class="fa fa-exclamation-triangle"></i> Credit : 
                        </td>
                        <td class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format(floatVal($patTcData[0]['ptc_credit']), 2, '.', ','); ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th class="text-right">
                            Paid :
                            <?php
                            $paid = (floatVal($grtot) - floatVal($patTcData[0]['ptc_credit']));
                            //echo number_format((floatVal($grtot) - floatVal($patTcData[0]['ptc_credit'])), 2, '.', ',');
                            if ($patTcData[0]['ptc_card_amt'] !== '0') {
                                echo ' <i class="fa fa-credit-card"></i> ' . number_format(floatVal($patTcData[0]['ptc_card_amt']), 2, '.', ',');
                                if (floatVal($patTcData[0]['ptc_card_amt']) !== $paid) {
                                    echo ' + <i class="fa fa-money"></i> ' . number_format(($paid-floatVal($patTcData[0]['ptc_card_amt'])), 2, '.', ',') . ' = ';
                                } else {
                                    echo ' = ';
                                }
                            } else {
                                echo '';
                            }
                            ?>
                        </th>
                        <th class="text-right">
                            <i class="fa fa-rupee"></i>&nbsp;
                            <?php echo number_format($paid, 2, '.', ','); ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
            
            <?php
            // get contents from buffer
            $contents = ob_get_contents();
            // clean and end the buffer
            ob_end_clean();
            $replacementArray = array(
                'PageTitle' => 'Print Patient Reciept Copy - JK Imaging',
                'RcptNo' => '<small class="pull-right" style="font-family:arial;margin-top:-15px">Time:' . date('d/m/Y h:i:s A', strtotime($patData[0]['pcreated'])) . '&nbsp;No. ' . $rcptNo . '</small>',
                'CenterContents' => $contents,
				'EnteredBy' => $patData[0]['staff_name'],
                'CSSHelpers' => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'custom.min.css'),
                'JSHelpers' => array('jquery.min.js', 'bootstrap.min.js', 'custom.min.js')
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
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sql = 'select * from patient_data where pid = ?';
            $pData = DbOperations::getObject()->fetchData($sql, [$opt['patid']]);
            $sql = 'select pt_test_id, pt_oprice, pt_price, cat_name, test_name '
                . 'from patient_tests left join test_list on test_id = pt_test_id '
                . 'left join test_cats on cat_id = under_cat where pat_id = ?';
            $tData = DbOperations::getObject()->fetchData($sql, [$opt['patid']]);
            $sql = 'select * from patient_test_calculations where ptc_pat_id = ?';
            $ptData = DbOperations::getObject()->fetchData($sql, [$opt['patid']]);//var_dump($ptData[0]['ptc_tot_price']);exit;
            $selectedTstIds = [];
            foreach ($tData as $tst) {
                $selectedTstIds[] = $tst['pt_test_id'];
            }
            ob_start();
            ?>
            <div class="col-xs-12">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Edit Patient - <?php echo $pData[0]['pname'];?></div>
                        <div class="panel-body">
                            <form class="form-horizontal ajaxfrm" role="form" id="patform" name="patform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedPatient">
                                <div class="form-group">
                                    <label for="name" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Date &amp; Time :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <div class="input-group datetimepicker">
                                            <input type="text" class="form-control" id="pdate" name="pdate" autocomplete="off" placeholder="Date &amp; Time of Test" value="<?php echo date('d-m-Y h:i A', strtotime($ptData[0]['ptc_hosp_dttm'])); ?>">
                                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                        </div>
                                    </div>
                                    <label for="pin" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Patient Type :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="ptype" id="pin" value="IP"<?php if ($ptData[0]['ptc_hosp_pattype'] === 'IP') echo ' checked="checked"';?> onchange="if ($(this).prop('checked') === true) $('#patid, #billno').removeAttr('readonly');"> Indoor
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="ptype" id="pout" value="OP"<?php if ($ptData[0]['ptc_hosp_pattype'] === 'OP') echo ' checked="checked"';?> onchange="if ($(this).prop('checked') === true) $('#patid, #billno').attr('readonly', 'readonly');"> Outdoor
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patid" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Patient ID :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="patid" name="patid" autocomplete="off" placeholder="Patient ID" value="<?php echo $ptData[0]['ptc_hosp_pid']; ?>"<?php if ($ptData[0]['ptc_hosp_pattype'] === 'OP') echo ' readonly="readonly"';?>>
                                    </div>
                                    <label for="billno" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label">Bill No :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="billno" name="billno" autocomplete="off" placeholder="Patient Bill No." value="<?php echo $ptData[0]['ptc_hosp_billno']; ?>"<?php if ($ptData[0]['ptc_hosp_pattype'] === 'OP') echo ' readonly="readonly"';?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Name :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="pname" name="pname" autofocus="autofocus" required="required" autocomplete="off" placeholder="Full Name" value="<?php echo $pData[0]['pname']; ?>">
                                    </div>
                                    <label for="addr" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Address :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"><?php echo $pData[0]['paddr']; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ageyr" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Age :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageyr" required="required" autocomplete="off" name="ageyr" placeholder="Yrs" value="<?php echo (!empty($pData[0]['pagey']) ? $pData[0]['pagey'] : '0'); ?>" min="0">
                                                    <span class="input-group-addon">Yrs</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="agemonth" required="required" autocomplete="off" name="agemonth" placeholder="Months" value="<?php echo (!empty($pData[0]['pagem']) ? $pData[0]['pagem'] : '0'); ?>" min="0">
                                                    <span class="input-group-addon">Mons</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="ageday" required="required" autocomplete="off" name="ageday" placeholder="Days" value="<?php echo (!empty($pData[0]['paged']) ? $pData[0]['paged'] : '0'); ?>" min="0">
                                                    <span class="input-group-addon">Days</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <label for="contact" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Contact No. :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" autocomplete="off" name="contact" placeholder="Contact Number" value="<?php echo $pData[0]['pcont']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sexm" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Sex :</label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sexm" value="M"<?php if ($pData[0]['psex'] === 'M') echo ' checked="checked"';?>> Male
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sexf" value="F"<?php if ($pData[0]['psex'] === 'F') echo ' checked="checked"';?>> Female
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="sex" id="sext" value="T"<?php if ($pData[0]['psex'] === 'T') echo ' checked="checked"';?>> Transgender
                                        </label>
                                    </div>
                                    <label for="drname" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Consultant : </label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="drname" name="drname">
                                            <?php
                                            $sql = 'select dr_id, dr_name, dr_org from doctor_details order by dr_name';
                                            $dr = DbOperations::getObject()->fetchData($sql);
                                            foreach ($dr as $drData):
                                            ?>
                                            <option<?php if ($ptData[0]['ptc_dr_id']===$drData['dr_id']) echo ' selected="selected"'; ?> value="<?php echo $drData['dr_id']; ?>"><?php echo $drData['dr_name'] . ($drData['dr_org'] ==='' ? '' : ' (' . $drData['dr_org'] .')'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <a href="javascript:void(0)" class="help-block" data-toggle="modal" data-target="#docModal">Not in the list ? Add Doctor.</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cat" class="col-lg-2 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Select Tests : </label>
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12">
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
                                    <div class="col-lg-4 col-md-8 col-sm-7 col-xs-12 col-lg-offset-2 col-md-offset-4 col-sm-offset-5">
                                        <select class="form-control" id="alphabets" name="alphabets" onchange="loadTests();">
                                            <option value="">All</option>
                                            <?php foreach(range('a','z') as $i): ?>
                                            <option value="<?php echo $i;?>"><?php echo strtoupper($i);?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group well well-sm" id="testList" style="height: 150px;overflow: auto">
                                    <?php
                                    $sql = 'select test_id, cat_name, test_name, test_price, test_oprice'
                                        . ' from test_list left join test_cats on under_cat = cat_id order by test_name';
                                    $tests = DbOperations::getObject()->fetchData($sql);
                                    foreach ($tests as $testData) {
                                    ?>
                                    <label for="test_<?php echo $testData['test_id'];?>" class="checkbox col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                        <input type="checkbox" data-ip-price="<?php echo $testData['test_price'];?>" data-op-price="<?php echo $testData['test_oprice'];?>" data-test-name="<?php echo $testData['cat_name'] . ' - ' . $testData['test_name']; ?>" name="test[]" id="test_<?php echo $testData['test_id']; ?>" value="<?php echo $testData['test_id']; ?>" title="<?php echo $testData['test_name']; ?>" class="testchk"<?php if (in_array($testData['test_id'], $selectedTstIds) === true) echo ' checked="checked"'; ?>> <?php echo $testData['cat_name'] . ' - ' . $testData['test_name']; ?>
                                    </label>
                                    <?php } ?>
                                </div>
                                <input type="hidden" name="tid" id="tid" value="<?php echo implode(',', $selectedTstIds);?>">
                                <input type="hidden" name="totpr" id="totpr" value="<?php echo $ptData[0]['ptc_tot_price'];?>">
                                <input type="hidden" name="pid" id="pid" value="<?php echo $pData[0]['pid'];?>">
                                <div class="form-group" id="testcal">
                                    <?php foreach ($tData as $tst) { ?>
                                    <div class="alert alert-success alert-dismissable testLine" id="selTst_<?php echo $tst['pt_test_id'] ?>" style="padding:5px;margin:2px"><div class="container"><div class="col-xs-8"><?php echo $tst['cat_name'] . ' - ' . $tst['test_name'];?></div><div class="col-xs-3 text-right"><span class="fa fa-rupee"></span>&nbsp;<span class="price"><?php echo $tst['pt_oprice'];?>.00</span><input type="hidden" name="tstid[]" value="<?php echo $tst['pt_test_id'];?>"><input type="hidden" name="tstpr[]" value="<?php echo $tst['pt_price'];?>"><input type="hidden" name="tstopr[]" value="<?php echo $tst['pt_oprice'];?>"></div><div class="col-xs-1"><button type="button" class="close" style="right:0" data-dismiss="alert" aria-hidden="true">&times;</button></div></div></div>
                                    <script>setTimeout("closeTestLineToUncheck('<?php echo $tst['pt_test_id'] ?>')", 2000);</script>
                                    <?php } ?>
                                </div>
                                <div class="form-group" id="testtotsdiscs"></div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6">Apply Discount : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="disc" id="disc" class="form-control text-right" value="<?php echo $ptData[0]['ptc_discount'];?>" onblur="updateTotPrice();" placeholder="Discount Amt">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6">Remarks : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="discrem" id="discrem" class="form-control" value="<?php echo $ptData[0]['ptc_disc_remark'];?>" placeholder="Discount Remarks">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-5 col-md-3 col-sm-6 col-xs-12 help-block text-right">
                                        To be filled only if paid by debit/credit card
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="trid" id="trid" class="form-control" placeholder="Transaction ID" value="<?php echo $ptData[0]['ptc_card_tid'];?>">
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                        <input type="text" name="invnum" id="invnum" class="form-control" placeholder="Invoice/Reference Num" value="<?php echo $ptData[0]['ptc_card_ref'];?>">
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="cardamt" id="cardamt" class="form-control text-right" placeholder="Paid Amount" onblur="updateTotPrice();" value="<?php print($ptData[0]['ptc_card_amt']);?>">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6">Grand Total : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" name="grtot" id="grtot" class="form-control text-right" value="<?php echo (floatval($ptData[0]['ptc_tot_price'])-floatval($ptData[0]['ptc_discount']));?>" readonly="readonly" placeholder="Grand Total (Automatic)">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3 col-md-3 col-sm-6 col-xs-12 col-lg-offset-6 col-md-offset-6">Credit : </label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" min="0" name="credval" id="credval" class="form-control text-right" value="<?php echo $ptData[0]['ptc_credit'];?>" onblur="updateTotPrice();" placeholder="Credit Amount">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-6 col-md-6 col-sm-6 col-xs-12 col-lg-offset-3 col-md-offset-3" for="paid">Paid : <span id="paidstr"></span></label>
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" name="paid" id="paid" class="form-control text-right" value="" readonly="readonly" placeholder="Paid Amt. Auto Calculated">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-9 col-md-offset-9 col-sm-offset-7 col-lg-3 col-md-3 col-sm-5 col-xs-12">
                                        <button type="submit" name="savePat" id="savePat" class="btn btn-primary"><i class="fa fa-save"></i> Save Data</button>&nbsp;
                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="window.location.reload();"><i class="fa fa-refresh"></i> Reset Form</a>
                                    </div>
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
                            <h4 class="modal-title" id="addDoc">Add Doctor Details</h4>
                        </div>
                        <div class="modal-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="respond.php?opt=saveNewDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Name : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" placeholder="Full Name of the doctor (without prefix Dr.)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Organization : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" placeholder="Organization/Hospital name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label">Contact No : </label>
                                    <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" placeholder="Contact No of the doctor">
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
            $pageTitle = 'Edit Patient Details - JK Imaging User Dashboard';
            break;
            
        // TO SAVE EDITED PATIENT
        case 'saveEditedPatient':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            // validate the data
            if (!isset($submittedData['pid']) or empty($submittedData['pid'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'Sorry, but patient ID not found'
                ]));
            }
            if (!isset($submittedData['ptype']) or empty($submittedData['ptype'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must select patient type'
                ]));
            }
            if (!isset($submittedData['pname']) or empty($submittedData['pname'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient name'
                ]));
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient address'
                ]));
            }
            if (!isset($submittedData['ageyr']) or is_nan($submittedData['ageyr']) or is_nan($submittedData['agemonth']) or is_nan($submittedData['ageday'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter patient age'
                ]));
            }
            if (!isset($submittedData['contact']) or empty($submittedData['contact'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'You must enter employee valid contact number'
                ]));
            }
            if (!isset($submittedData['tid']) or empty($submittedData['tid'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'Please select tests for the patient'
                ]));
            }
            if ((intval($submittedData['disc']) > 0) and empty($submittedData['discrem'])) {
                die(json_encode([
                    'status' => 'error',
                    'msg' => 'Please enter remarks / discount details'
                ]));
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // insert the data
            DbOperations::getObject()->buildUpdateQuery('patient_data', ['pname', 'paddr', 'pagey', 'pagem', 'paged', 'pcont', 'psex'], ['pid']);
            // make a data array to be saves as in database table
            $patData = [
                ucwords($submittedData['pname']),
                ucwords($submittedData['addr']),
                $submittedData['ageyr'],
                $submittedData['agemonth'],
                $submittedData['ageday'],
                $submittedData['contact'],
                $submittedData['sex'],
                $submittedData['pid'],
            ];
            $pid = DbOperations::getObject()->runQuery($patData);
            DbOperations::getObject()->buildUpdateQuery('patient_test_calculations', ['ptc_hosp_pid', 'ptc_hosp_billno', 'ptc_hosp_pattype', 'ptc_dr_id', 'ptc_tot_price', 'ptc_discount', 'ptc_disc_remark', 'ptc_card_tid', 'ptc_card_ref', 'ptc_card_amt', 'ptc_credit', 'ptc_staff_id'], ['ptc_pat_id']);
            $patSpData = [
                $submittedData['patid'],
				//date('Y-m-d H:i:s', strtotime($submittedData['pdate'])),
                $submittedData['billno'],
                $submittedData['ptype'],
                $submittedData['drname'],
                $submittedData['totpr'],
                $submittedData['disc'],
                $submittedData['discrem'],
                $submittedData['trid'],
                $submittedData['invnum'],
                $submittedData['cardamt'],
                $submittedData['credval'],
                $_SESSION['UID'],
                $submittedData['pid']
            ];
            $pspid = DbOperations::getObject()->runQuery($patSpData);
            DbOperations::getObject()->buildDeleteQuery('patient_tests', ['pat_id']);
            DbOperations::getObject()->runQuery([$submittedData['pid']]);
            DbOperations::getObject()->buildInsertQuery('patient_tests');
            //$tsts = explode(',', $submittedData['tid']);
            $alltstCount = count($submittedData['tstid']);
            $tsucCount = 0;
            foreach ($submittedData['tstid'] as $tk => $tst) {
                if (!empty($tst) and isset($submittedData['tstopr'][$tk])) {
                    $tstArr = [
                        null, $submittedData['pid'], $tst, $submittedData['tstpr'][$tk], $submittedData['tstopr'][$tk],
                        ((isset($submittedData['pdate']) and !empty($submittedData['pdate'])) ?
                            date('Y-m-d H:i:s', strtotime($submittedData['pdate'])) :
                            DBTIMESTAMP),
                        $_SESSION['UID'], 1
                    ];
                    $tsuc = DbOperations::getObject()->runQuery($tstArr);
                    if ($tsuc !== false) {
                        ++$tsucCount;
                    }
                }
            }
            //$success = DbOperations::getObject()->runQuery($saveData);
            if (($pid !== false) and ($pspid !== false) and ($alltstCount === $tsucCount)) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = 'Successfully edited the patient data';
                session_write_close();
                die(json_encode([
                    'status' => 'success',
                    'msg' => 'You have successfully edited the patient.',
                    'url' => 'index.php'
                ]));
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                die(json_encode([
                    'status' => 'success',
                    'msg' => 'Error occured while editing the patient'
                ]));
            }
            break;
        case 'del':
             if (!isset($opt['patid']) or empty($opt['patid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No patient data found';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // update the data
            DbOperations::getObject()->buildDeleteQuery('patient_data', ['pid']);
            $success = DbOperations::getObject()->runQuery([$opt['patid']]);
            DbOperations::getObject()->buildDeleteQuery('patient_tests', ['pat_id']);
            $tsuccess = DbOperations::getObject()->runQuery([$opt['patid']]);
            DbOperations::getObject()->buildDeleteQuery('patient_test_calculations', ['ptc_pat_id']);
            $tcsuccess = DbOperations::getObject()->runQuery([$opt['patid']]);
            
            if (($success !== false) and ($tsuccess !== false) and ($tcsuccess !== false)) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully deleted a patient and the assigned tests';
                session_write_close();
                header('Location:index.php');
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while deleting the patient';
                session_write_close();
                header('Location:index.php');
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
    'CSSHelpers'                    => array('bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'dataTables.bootstrap.min.css','bootstrap-datetimepicker.min.css', 'custom.min.css'),
    'JSHelpers'                     => array('jquery.min.js', 'bootstrap.min.js', 'jquery.dataTables.min.js', 'dataTables.bootstrap.min.js','moment.min.js', 'bootstrap-datetimepicker.min.js', 'custom.js')
);

assignTemplate($replacementArray);
// the ending php tag has been intentionally not used to avoid unwanted whitespaces before document starts
