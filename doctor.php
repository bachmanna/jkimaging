<?php
// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
// throw out the user if it is not admin
if (isLogged() !== '1'){redirectUser();}
// extract get data to get option by user
$opt = DataFilter::getObject()->cleanData($_GET);

if (isset($opt['opt']) and ! empty($opt['opt'])) {
    switch ($opt['opt']) {
        // show add new doctor form
        case 'add':
            ob_start();
            ?>
            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Add New Doctor</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveNewDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" autocomplete="off" placeholder="Full Name of the doctor">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Organization: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" autocomplete="off" placeholder="Organization/Hospital name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Contact No: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" autocomplete="off" placeholder="Contact No of the doctor">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <button type="submit" name="saveDoct" id="saveDoct" class="btn btn-default">Save Doctor</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Add a new doctor - JK Imaging Admin Panel';
            break;

        // save the employee data posted from the new employee form
        case 'saveNewDoc':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['dname']) or empty($submittedData['dname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter doctor name';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            /*if (!isset($submittedData['org']) or empty($submittedData['org'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must Enter doctor organization';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }*/
            
            if (!isset($submittedData['cont']) or empty($submittedData['cont'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must Enter doctor contact number';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
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
            DbOperations::getObject()->buildInsertQuery('doctor_details');
            // insert the data
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully added a new doctor';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while adding a patient';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        
        // show the form to edit the selected employee
        case 'edit':
            if (!isset($opt['did']) or empty($opt['did'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No doctor data found';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $dData = DbOperations::getObject()->fetchData('select dr_id, dr_name, dr_org, dr_phone from doctor_details where dr_id = ?', [$opt['did']]);
            ob_start();
            ?>
            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit the Doctor</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="docform" name="docform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedDoc">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="dname" name="dname" required="required" autocomplete="off" placeholder="Full Name of the doctor" value="<?php echo $dData[0]['dr_name']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="org" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Organization: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="org" name="org" autocomplete="off" placeholder="Organization/Hospital name" value="<?php echo $dData[0]['dr_org']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cont" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Contact No: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="cont" name="cont" required="required" autocomplete="off" placeholder="Contact No of the doctor" value="<?php echo $dData[0]['dr_phone']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="hidden" name="doctid" id="doctid" value="<?php echo $dData[0]['dr_id'];?>">
                                        <button type="submit" name="saveDoct" id="saveDoct" class="btn btn-default">Save Doctor</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Edit a Doctor - JK Imaging Admin Panel';
            break;
            
        case 'saveEditedDoc':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['doctid']) or empty($submittedData['doctid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Whoa, No doctor found like that !!';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['dname']) or empty($submittedData['dname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter doctor name';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            /*if (!isset($submittedData['org']) or empty($submittedData['org'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must Enter doctor organization';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }*/
            
            if (!isset($submittedData['cont']) or empty($submittedData['cont'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must Enter doctor contact number';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            
            // make a data array to be saves as in database table
            $saveData = [
                ucwords($submittedData['dname']),
                ucwords($submittedData['org']),
                $submittedData['cont'],
                $submittedData['doctid']
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('doctor_details', ['dr_name', 'dr_org', 'dr_phone'], ['dr_id']);
            // insert the data
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully edited the doctor';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while editing the doctor';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        // delete doctor
        case 'del':
            if (!isset($opt['did']) or empty($opt['did'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No test data found to be deleted';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildDeleteQuery('doctor_details', ['dr_id']);
            // update the data
            $success = DbOperations::getObject()->runQuery([$opt['did']]);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully deleted the doctor';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while deleting the doctor';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
        default:
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all doctor</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=allDocs" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Organization/Hospital</th>
                                    <th>Created By</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Organization/Hospital</th>
                                    <th>Created By</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all doctors - JK Imaging Admin Panel';
            break;
    }
} else {
    ob_start();
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading">View all doctor</div>
            <div class="panel-body">
                <table cellpadding="0" data-get-ajax="respond.php?opt=allDocs" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Organization/Hospital</th>
                            <th>Created By</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Organization/Hospital</th>
                            <th>Created By</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    $pageTitle = 'View all doctors - JK Imaging Admin Panel';
}
// get contents from buffer
$contents = ob_get_contents();
// clean and end the buffer
ob_end_clean();

$replacementArray = [
    'PageTitle' => $pageTitle,
    'ErrorMessages' => getAlertMsg(),
    'CenterContents' => $contents,
    'CSSHelpers' => ['bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'dataTables.bootstrap.min.css', 'custom.min.css'],
    'JSHelpers' => ['jquery.min.js', 'bootstrap.min.js', 'bootstrap-typeahead.min.js', 'jquery.dataTables.min.js', 'dataTables.bootstrap.min.js', 'custom.min.js']
];

assignTemplate($replacementArray);
// the ending php tag has been intentionally not used to avoid unwanted whitespaces before document starts
