<?php
/**
 * This is the common responder file to respond requests all over the project
 * and it contains the required actions with reuest data that will be commonly used
 * New functionality and actions can be added using class files 
 * 
 * @author Kirti Kumar Nayak <admin@thebestfreelancer.in>
 * @license http://thebestfreelancer.in The Best Freelancer. India
 * @version Build 1.0
 * @package RepresentativeDatabase
 * @copyright (c) 2014, The Best Freelancer
 * @outputBuffering disabled
 */
// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
// check if user authorized to perform this operation
if (isLogged() !== '1'){redirectUser();}
// extract get data to get option by user
$opt = DataFilter::getObject()->cleanData($_GET);
if (isset($opt['opt'])) {
    // switch to data recieved
    switch ($opt['opt']) {
        // if patient names are to be suggested
        case 'patNameSuggest':
            // blank array to hold the patient names
            $names = array();
            // get all the patient names from database
            $patientArr = DbOperations::getObject()->fetchData('select pid, p_name, p_phone from patient_details order by p_name');
            // run a loop to get the patient names
            foreach ($patientArr as $patData) {
                array_push(
                    $names,
                    [
                        'id' => $patData['pid'],
                        'name' => $patData['p_name']
                    ]
                );
            }
            // echo the json formatted data for auto suggestion
            echo json_encode($names);
            break;
        // if opted to save the doctor data
        case 'saveNewDoc':
            $respMsg = array();
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            // validate the data
            if (!isset($submittedData['dname']) or empty($submittedData['dname'])) {
                $respMsg['status'] = 'error';
                $respMsg['message'] = 'You must Enter doctor name';
                die(json_encode($respMsg));
            }
            if (!isset($submittedData['org']) or empty($submittedData['org'])) {
                $respMsg['status'] = 'error';
                $respMsg['message'] = 'You must Enter doctor organization';
                die(json_encode($respMsg));
            }
            if (!isset($submittedData['cont']) or empty($submittedData['cont'])) {
                $respMsg['status'] = 'error';
                $respMsg['message'] = 'You must Enter doctor contact number';
                die(json_encode($respMsg));
            }

            // make a data array to be saves as in database table
            $saveData = [
                null,
                ucwords($submittedData['dname']),
                ucwords($submittedData['org']),
                $submittedData['cont'],
                $_SESSION['UID'],
                DBTIMESTAMP
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // insert the data
            DbOperations::getObject()->buildInsertQuery('doctor_details');
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $respMsg['status'] = 'success';
                $respMsg['message'] = 'Doctor details saved';
                die(json_encode($respMsg));
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $respMsg['status'] = 'error';
                $respMsg['message'] = 'Doctor details could not be saved for some error';
                die(json_encode($respMsg));
            }
            break;

        // reload doctor selectbox
        case 'reloadDrSelectBox':
            $dr = DbOperations::getObject()->fetchData('select dr_id, dr_name, dr_org from doctor_details order by dr_name');
            ?>
            <option value="">Select</option>
            <?php
            foreach ($dr as $drData):
                ?>
                <option value="<?php echo $drData['dr_id']; ?>"><?php echo $drData['dr_name'] . ($drData['dr_org'] === '' ? '' : ' (' . $drData['dr_org'] . ')'); ?></option>
                <?php
            endforeach;
            break;

        case 'loadTests':
            // clean the submitted data
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            $condArr = [];
            $where = [];
            if (!empty($submittedData['catid'])) {
                $where[] = 'under_cat = ?';
                $condArr[] = $submittedData['catid'];
            }
            if (!empty($submittedData['alphabet'])) {
                $where[] = 'test_name like ?';
                $condArr[] = $submittedData['alphabet'] . '%';
            }
            if (count($where) > 0) {
                $wh = ' where ' . implode(' and ', $where);
            } else {
                $wh = '';
            }
            
            $sql = 'select test_id, cat_name, test_name, test_price, test_oprice  from test_list '
                . ' left join test_cats on under_cat = cat_id ' . $wh . ' order by test_name';
            //die($sql);
            $testList = '';
            $tests = DbOperations::getObject()->fetchData($sql, $condArr);
            foreach ($tests as $testData):
                $testList .= '<label for="test_' . $testData['test_id'] . '" class="checkbox col-lg-3 col-md-4 col-sm-6 col-xs-12">';
                $testList .= '<input type="checkbox" name="test[]" data-ip-price="';
                $testList .= $testData['test_price'] .'" data-op-price="'. $testData['test_oprice'];
                $testList .= '" data-test-name="'. $testData['cat_name'] . ' - ' . $testData['test_name'];
                $testList .= '" id="test_' . $testData['test_id'] . '"';
                $testList .= ' value="' . $testData['test_id'] . '" title="' . $testData['test_name'] . '" class="testchk"> ';
                $testList .= $testData['cat_name'] . ' - ' . $testData['test_name'];
                $testList .= '</label>';
            endforeach;
            die($testList);
            break;
        
// CO313076220 - BSNL Complain No
        case 'allTests':
            $sql = 'select test_id, test_name, cat_name, test_price, test_oprice, staff_name, test_created'
            . ' from test_list left join test_cats on test_list.under_cat = cat_id'
            . ' left join staff_users on test_created_by = staff_id order by test_name';
            $testData = DbOperations::getObject()->fetchData($sql);
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    $aaData[] = [
                        utf8_encode($data['test_name']),
                        $data['cat_name'],
                        number_format(((float) $data['test_price']), 2, '.', ','),
                        number_format(((float) $data['test_oprice']), 2, '.', ','),
                        $data['staff_name'],
                        date("d-m-Y h:i:s A", strtotime($data['test_created'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="Edit This Test Details" class="tip btn btn-warning" href="tests.php?opt=edit&tid=' . $data['test_id'] . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Test Details" class="tip btn btn-danger" href="tests.php?opt=del&tid='
                        .$data['test_id'].'" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a><div>'
                    ];
                }
            }
            //print_r($aaData);exit;
            // create an array suitable for data
            $outPutArray = ['aaData' => $aaData];
            // output the array in json format
            die(json_encode($outPutArray));
            break;
            
        case 'allDocs':
            $sql = 'select dr_id, dr_name, dr_org, dr_created, staff_name from doctor_details left join staff_users on staff_id = dr_created_by order by dr_name';
            $testData = DbOperations::getObject()->fetchData($sql);
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    $aaData[] = [
                        utf8_encode($data['dr_name']),
                        $data['dr_org'],
                        $data['staff_name'],
                        date("d-m-Y", strtotime($data['dr_created'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="Edit This Doctor Details" class="tip btn btn-warning" href="doctor.php?opt=edit&did=' . $data['dr_id'] . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Doctor Details" class="tip btn btn-danger" href="doctor.php?opt=del&did='.$data['dr_id'].'" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            //print_r($aaData);exit;
            // create an array suitable for data
            $outPutArray = ['aaData' => $aaData];
            // output the array in json format
            die(json_encode($outPutArray));
            break;
            
        case 'allEmps':
            $sql = 'select staff_id, staff_name, staff_address, staff_contact, staff_privilage, is_active, staff_created'
            . ' from staff_users where staff_id <> ? order by staff_name';
            $testData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            $aaData = [];
            $previl = 'Reception';
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    switch ($data['staff_privilage']) {
                        case '1':
                            $previl = 'Administrator';
                            break;
                        case '2':
                            $previl = 'User';
                            break;

                        default:
                            $previl = 'Reception';
                            break;
                    }
                    $aaData[] = [
                        $data['staff_name'],
                        $data['staff_address'],
                        $data['staff_contact'],
                        $previl,
                        ($data['is_active'] === '1' ? 'Active' : 'Inactive'),
                        date("d-m-Y", strtotime($data['staff_created'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="Edit This Staff Details" class="tip btn btn-warning" href="employee.php?opt=edit&eid=' . $data['staff_id'] . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        ($data['is_active'] === '1' ?
                        '<a title="Deactivate This Staff" class="tip btn btn-warning" href="employee.php?opt=disapprove&eid=' . $data['staff_id'] . '">' .
                        '<span class="glyphicon glyphicon-ban-circle"></span>' .
                        '</a>'
                        :
                        '<a title="Activate This Staff" class="tip btn btn-success" href="employee.php?opt=approve&eid=' . $data['staff_id'] . '">' .
                        '<span class="glyphicon glyphicon-ok"></span>' .
                        '</a>') .
                        
                        '<a title="Delete This Staff Details" class="tip btn btn-danger" href="employee.php?opt=del&eid='.$data['staff_id'].'" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            //print_r($aaData);exit;
            // create an array suitable for data
            $outPutArray = ['aaData' => $aaData];
            // output the array in json format
            die(json_encode($outPutArray));
            break;
            
        case 'allPats':
            $sql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex, pcreated from patient_data order by pcreated desc';
            $testData = DbOperations::getObject()->fetchData($sql);
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    $aaData[] = [
                        $data['pname'],
                        $data['paddr'],
                        ($data['pagey'] === '0' ? '' : $data['pagey'] . '&nbsp;Yrs').
                        ($data['pagem'] === '0' ? '' : '&nbsp;' . $data['pagem'] . '&nbsp;Months').
                        ($data['paged'] === '0' ? '' : '&nbsp;' . $data['paged'] . '&nbsp;Days').
                        '&nbsp;/&nbsp;' . $data['psex'],
                        $data['pcont'],
                        date('d-m-Y h:i:s A', strtotime($data['pcreated'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="Edit This Patient Details" class="tip btn btn-warning" href="patient.php?opt=edit&patid=' . $data['pid'] . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Patient Details" class="tip btn btn-danger" href="patient.php?opt=del&patid='.$data['pid'].'" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            //print_r($aaData);exit;
            // create an array suitable for data
            $outPutArray = ['aaData' => $aaData];
            // output the array in json format
            die(json_encode($outPutArray));
            break;
        
        case 'allPatsToday':
            $sql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex, staff_name, '
            . ' pcreated, ptc_tot_price, ptc_discount, ptc_credit, ptc_hosp_pattype,'
            . ' ptc_hosp_pid, ptc_hosp_billno, ptc_hosp_dttm, ptc_dttm, ptc_id '
            . ' from patient_data left join patient_test_calculations'
            . ' on pid = ptc_pat_id left join staff_users on staff_id = pcreatedby where date(ptc_hosp_dttm) = ? '
            . 'and ptc_status = ? order by ptc_dttm desc';
            $testData = DbOperations::getObject()->fetchData($sql, [DBDATE, 1]);
            //$opSerRes = DbOperations::getObject()->prepareQuery('select count(ptc_pat_id) as totpatcount from patient_test_calculations where ptc_hosp_pattype = ? and ptc_id < ?');
            
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    if ($data['ptc_hosp_pattype'] === 'OP') {
                        //$opSerNo = DbOperations::getObject()->fetchData('', ['OP', $data['pid']], false, $opSerRes);
                        //$rcptNo = (intval($opSerNo[0]['totpatcount'])+1);
                        $patType = 'OP-';
                    } else {
                        //$rcptNo = $data['pid'];
                        $patType = 'IP-';
                    }
					$rcptNo = $data['ptc_id'];
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
                    //$ptidRcpt = $data['ptc_hosp_pid'] . ' / ' . $data['ptc_hosp_billno'];
                    $aaData[] = [
                        //date('dmy', strtotime($data['ptc_dttm'])) . $data['pid'],
                        $data['pname'],
                        ($data['pagey'] === '0' ? '' : $data['pagey'] . '&nbsp;Yrs').
                        ($data['pagem'] === '0' ? '' : '&nbsp;' . $data['pagem'] . '&nbsp;Months').
                        ($data['paged'] === '0' ? '' : '&nbsp;' . $data['paged'] . '&nbsp;Days').
                        '&nbsp;/&nbsp;' . $data['psex'],
                        $data['pcont'],
                        number_format((floatval($data['ptc_tot_price']) - floatval($data['ptc_discount'])), 2, '.', ','),
                        date('dmy', strtotime($data['ptc_hosp_dttm'])) . $data['pid'] . ' / ' . $rcptNo,
						$data['staff_name'],
                        date("h:i:s A", strtotime($data['ptc_hosp_dttm'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="View This Patient test details" class="tip btn btn-default" href="patient.php?opt=showPatDetDateSpecific&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-search"></span>' .
                        '</a>' .
                        '<a title="Print This Patient Receipt" class="tip btn btn-default" href="patient.php?opt=printPatReceipt&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-print"></span>' .
                        '</a>' .
                        '<a title="Edit This Patient Details" class="tip btn btn-warning" href="patient.php?opt=edit&patid=' . $data['pid'] . '&receiptDttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Patient" class="tip btn btn-danger" href="patient.php?opt=del&patid='.$data['pid'].'&dttm=' . strtotime($data['ptc_dttm']) . '" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            // output the array in json format
            die(json_encode(['aaData' => $aaData]));
            break;
            
        case 'showAnotherDatePatients':
            if (!isset($opt['ts']) or ($opt['ts'] === '')) {
                die(json_encode(['aaData' => []]));
            }
            $sql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex, staff_name, '
            . ' pcreated, ptc_tot_price, ptc_discount, ptc_credit, ptc_hosp_pattype,'
            . ' ptc_hosp_pid, ptc_hosp_billno, ptc_hosp_dttm, ptc_dttm '
            . ' from patient_data left join patient_test_calculations'
            . ' on pid = ptc_pat_id left join staff_users on staff_id = pcreatedby where date(ptc_hosp_dttm) = ? '
            . 'and ptc_status = ? order by ptc_dttm desc';
            $testData = DbOperations::getObject()->fetchData($sql, [date('Y-m-d', $opt['ts']), 1]);
            $opSerRes = DbOperations::getObject()->prepareQuery('select count(ptc_pat_id) as totpatcount from patient_test_calculations where ptc_hosp_pattype = ? and ptc_id < ?');
            
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    if ($data['ptc_hosp_pattype'] === 'OP') {
                        $opSerNo = DbOperations::getObject()->fetchData('', ['OP', $data['pid']], false, $opSerRes);
                        $rcptNo = (intval($opSerNo[0]['totpatcount'])+1);
                        $patType = 'OP-';
                    } else {
                        $rcptNo = $data['pid'];
                        $patType = 'IP-';
                    }
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
                    $aaData[] = [
                        //date('dmy', strtotime($data['ptc_dttm'])) . $data['pid'],
                        $data['pname'],
                        ($data['pagey'] === '0' ? '' : $data['pagey'] . '&nbsp;Yrs').
                        ($data['pagem'] === '0' ? '' : '&nbsp;' . $data['pagem'] . '&nbsp;Months').
                        ($data['paged'] === '0' ? '' : '&nbsp;' . $data['paged'] . '&nbsp;Days').
                        '&nbsp;/&nbsp;' . $data['psex'],
                        $data['pcont'],
                        number_format((floatval($data['ptc_tot_price']) - floatval($data['ptc_discount'])), 2, '.', ','),
                        date('dmy', strtotime($data['ptc_hosp_dttm'])) . $data['pid'] . ' / ' . $rcptNo,
						$data['staff_name'],
                        date("h:i:s A", strtotime($data['ptc_hosp_dttm'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="View This Patient test details" class="tip btn btn-default" href="patient.php?opt=showPatDetDateSpecific&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-search"></span>' .
                        '</a>' .
                        '<a title="Print This Patient Receipt" class="tip btn btn-default" href="patient.php?opt=printPatReceipt&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-print"></span>' .
                        '</a>' .
                        '<a title="Edit This Patient Test Details" class="tip btn btn-warning" href="patient.php?opt=edit&patid=' . $data['pid'] . '&receiptDttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Patient Tests today" class="tip btn btn-danger" href="patient.php?opt=del&patid='.$data['pid'].'&dttm=' . strtotime($data['ptc_dttm']) . '" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            // output the array in json format
            die(json_encode(['aaData' => $aaData]));
            break;
            
        case 'showRangeDatePatients':
            if (!isset($opt['sts']) or ($opt['sts'] === '')) {
                die(json_encode(array('aaData' => array())));
            }
            if (!isset($opt['ets']) or ($opt['ets'] === '')) {
                die(json_encode(array('aaData' => array())));
            }
            $sql = 'select pid, pname, paddr, pagey, staff_name,'
            . ' pagem, paged, pcont, psex,ptc_hosp_pattype,'
            . ' pcreated, ptc_tot_price, ptc_discount, '
            . ' ptc_hosp_pid, ptc_hosp_billno, ptc_hosp_dttm, ptc_dttm '
            . ' from patient_data left join patient_test_calculations'
            . ' on pid = ptc_pat_id  left join staff_users on staff_id = pcreatedby'
            . ' where date(ptc_hosp_dttm) between ? '
            . ' and ? and ptc_status = ? '
            . 'order by ptc_dttm desc';
			$opSerRes = DbOperations::getObject()->prepareQuery('select count(ptc_pat_id) as totpatcount from patient_test_calculations where ptc_hosp_pattype = ? and ptc_id < ?');
            $testData = DbOperations::getObject()->fetchData($sql, [date('Y-m-d', $opt['sts']), date('Y-m-d', $opt['ets']), 1]);
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
					if ($data['ptc_hosp_pattype'] === 'OP') {
                        $opSerNo = DbOperations::getObject()->fetchData('', ['OP', $data['pid']], false, $opSerRes);
                        $rcptNo = (intval($opSerNo[0]['totpatcount'])+1);
                        $patType = 'OP-';
                    } else {
                        $rcptNo = $data['pid'];
                        $patType = 'IP-';
                    }
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
                    $aaData[] = [
                        date('dmy', strtotime($data['ptc_dttm'])) . $data['pid'],
                        $data['pname'],
                        ($data['pagey'] === '0' ? '' : $data['pagey'] . '&nbsp;Yrs').
                        ($data['pagem'] === '0' ? '' : '&nbsp;' . $data['pagem'] . '&nbsp;Months').
                        ($data['paged'] === '0' ? '' : '&nbsp;' . $data['paged'] . '&nbsp;Days').
                        '&nbsp;/&nbsp;' . $data['psex'],
                        $data['pcont'],
                        number_format((floatval($data['ptc_tot_price']) - floatval($data['ptc_discount'])), 2, '.', ','),
						date('dmy', strtotime($data['ptc_hosp_dttm'])) . $data['pid'] . ' / ' . $rcptNo,
                        //$data['ptc_hosp_pid'] . ' / ' . $data['ptc_hosp_billno'],
                        date("d/m/y h:i:s A", strtotime($data['ptc_hosp_dttm'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="View This Patient test details" class="tip btn btn-default" href="patient.php?opt=showPatDetDateSpecific&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-search"></span>' .
                        '</a>' .
                        '<a title="Print This Patient Receipt" class="tip btn btn-default" href="patient.php?opt=printPatReceipt&patid=' . $data['pid'] . '&dttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-print"></span>' .
                        '</a>' .
                        '<a title="Edit This Patient Test Details" class="tip btn btn-warning" href="patient.php?opt=edit&patid=' . $data['pid'] . '&receiptDttm=' . strtotime($data['ptc_dttm']) . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Patient Tests today" class="tip btn btn-danger" href="patient.php?opt=del&patid='.$data['pid'].'&dttm=' . strtotime($data['ptc_dttm']) . '" onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            die(json_encode(['aaData' => $aaData]));
            break;
            
        case 'mySelf':
            $sql = 'select pid, pname, paddr, pagey, pagem, paged, pcont, psex, pcreated'
            . ' from patient_data where pcreatedby = ? order by pcreated desc';
            $testData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            $aaData = [];
            if (count($testData) > 0) {
                foreach ($testData as $key => $data) {
                    $aaData[] = [
                        $data['pname'],
                        $data['paddr'],
                        ($data['pagey'] === '0' ? '' : $data['pagey'] . '&nbsp;Yrs').
                        ($data['pagem'] === '0' ? '' : '&nbsp;' . $data['pagem'] . '&nbsp;Months').
                        ($data['paged'] === '0' ? '' : '&nbsp;' . $data['paged'] . '&nbsp;Days').
                        '&nbsp;/&nbsp;' . $data['psex'],
                        $data['pcont'],
                        date('d-m-Y h:i:s A', strtotime($data['pcreated'])),
                        '<div class="btn-group btn-group-xs" role="group" aria-label="tools"><a title="Edit This Patient Details" class="tip btn btn-warning" href="patient.php?opt=edit&patid=' . $data['pid'] . '">' .
                        '<span class="glyphicon glyphicon-pencil"></span>' .
                        '</a>' .
                        '<a title="Delete This Patient Details" class="tip btn btn-danger" href="patient.php?opt=del&patid='.$data['pid'].' onclick="confirmDelete(this,event);">' .
                        '<span class="fa fa-trash"></span></a></div>'
                    ];
                }
            }
            //print_r($aaData);exit;
            // create an array suitable for data
            $outPutArray = ['aaData' => $aaData];
            // output the array in json format
            die(json_encode($outPutArray));
            break;
            
        case 'updateAccount':
            
            // clean the submitted data
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            // validate the data
            if (!isset($submittedData['name']) or empty($submittedData['name'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You must Enter your name';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['email']) or ( strlen(filter_var($submittedData['email'], FILTER_VALIDATE_EMAIL)) < 1)) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You must Enter your valid E-mail';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['uname']) or empty($submittedData['uname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You must Enter your Username';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['opass']) or empty($submittedData['opass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You must Enter your Old Password';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['npass']) or empty($submittedData['npass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You must Enter your New Password';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // check if the old password matches or not
            $sql = 'select uid from users_data where uname = ? and pass = ?';
            $passCheck = DbOperations::getObject()->fetchData(
                $sql, [$_SESSION['USERNAME'], DataFilter::getObject()->pwdHash($submittedData['opass'])]
            );

            if ((count($passCheck) < 0) or ! isset($passCheck[0]['uid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Your old password is wrong';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }

            $sql = 'update users_data set pass = ?, uname = ?, name = ?, email = ? where uid = ?';
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('users_data', ['uname', 'name', 'email'], ['uid']);
            $success = DbOperations::getObject()->runQuery(
                [
                    DataFilter::getObject()->pwdHash($submittedData['npass']),
                    $submittedData['uname'],
                    $submittedData['name'],
                    $submittedData['email'],
                    $passCheck[0]['uid']
                ]
            );
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = 'You have successfully changed your profile';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Error occured while changing profile';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;

        
        default:
            echo 'This is an invalid option dear...';
            break;
    }
} else {
    echo 'This is an invalid option dear...';
}
exit(0);
