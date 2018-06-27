<?php
// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
// throw out the user if it is not admin
if (isLogged() !== '1'){redirectUser();}
// extract get data to get option by user
$opt = DataFilter::getObject()->cleanData($_GET);

if (isset($opt['opt']) and ! empty($opt['opt'])) {
    switch ($opt['opt']) {
        // show add new employee form
        case 'add':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">Add New Employee</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="empform" name="empform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveNewEmp">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="name" name="name" required="required" placeholder="Full Name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No.: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" name="contact" placeholder="Contact Number">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="addr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Address: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="uname" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Username: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="uname" required="required" name="uname" placeholder="Desired Username">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="pass" name="pass" required="required" placeholder="Desired Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rpass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Re-Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="rpass" name="rpass" required="required" placeholder="Re-enter above Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="stype" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Staff Type: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="stype" name="stype">
                                            <option value="3">Reception</option>
                                            <option value="2">User</option>
                                            <option value="1">Administrator</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <button type="submit" name="saveEmp" id="saveEmp" class="btn btn-info">Create Employee</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Add a new Employee - JK Imaging Admin Panel';
            break;

        // save the employee data posted from the new employee form
        case 'saveNewEmp':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['name']) or empty($submittedData['name'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee name';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['contact']) or is_nan($submittedData['contact'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee valid contact number';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee address';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['uname']) or empty($submittedData['uname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee Username';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // check if username given already exists
            $present = DbOperations::getObject()->fetchData('select staff_uname from staff_users where staff_uname = ?', [$submittedData['uname']]);
            if (count($present) > 0) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'This username already in use. Please choose another';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['pass']) or empty($submittedData['pass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee Password';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['rpass']) or ($submittedData['pass'] !== $submittedData['rpass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must repeat the password same as password field';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // make a data array to be saves as in database table
            $saveData = [
                null,
                $submittedData['name'],
                $submittedData['addr'],
                $submittedData['contact'],
                $submittedData['uname'],
                DataFilter::getObject()->pwdHash($submittedData['pass']),
                $submittedData['stype'],
                1,
                $_SESSION['UID'],
                DBTIMESTAMP
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildInsertQuery('staff_users');
            // insert the data
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully created an employee.';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while creating employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        
        // show the form to edit the selected employee
        case 'edit':
            if (!isset($opt['eid']) or empty($opt['eid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No employee data found';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sql = 'select staff_id, staff_name, staff_address, staff_uname, staff_contact, staff_privilage from staff_users where staff_id = ?';
            $eData = DbOperations::getObject()->fetchData($sql, [$opt['eid']]);
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit an Employee</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="empform" name="empform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedEmp">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="name" name="name" required="required" placeholder="Full Name" value="<?php echo $eData[0]['staff_name']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No.: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" name="contact" placeholder="Contact Number" value="<?php echo $eData[0]['staff_contact']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="addr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Address: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"><?php echo $eData[0]['staff_address']; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="uname" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Username: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="uname" required="required" name="uname" placeholder="Desired Username" value="<?php echo $eData[0]['staff_uname']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="pass" name="pass" required="required" placeholder="Desired Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rpass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Re-Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="rpass" name="rpass" required="required" placeholder="Re-enter above Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="stype" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Staff Type: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="stype" name="stype">
                                            <option<?php echo ($eData[0]['staff_privilage'] === '3' ? ' selected="selected"' : ''); ?> value="3">Reception</option>
                                            <option<?php echo ($eData[0]['staff_privilage'] === '2' ? ' selected="selected"' : ''); ?> value="2">User</option>
                                            <option<?php echo ($eData[0]['staff_privilage'] === '1' ? ' selected="selected"' : ''); ?> value="1">Administrator</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="hidden" name="empid" id="empid" value="<?php echo $eData[0]['staff_id']; ?>">
                                        <button type="submit" name="saveEmp" id="saveEmp" class="btn btn-default">Save Employee</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Edit an Employee - JK Imaging Admin Panel';
            break;
        // save edited employee
        case 'saveEditedEmp':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if (!isset($submittedData['empid']) or empty($submittedData['empid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Whoa, where is the employee id ??';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // validate the data
            if (!isset($submittedData['name']) or empty($submittedData['name'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee name';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['contact']) or is_nan($submittedData['contact'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee valid contact number';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['addr']) or empty($submittedData['addr'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee address';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['uname']) or empty($submittedData['uname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee Username';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // check if username given already exists
            $present = DbOperations::getObject()->fetchData('select staff_uname from staff_users where staff_uname = ?', [$submittedData['uname']]);
            if (count($present) > 1) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'This username already in use. Please choose another';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['pass']) or empty($submittedData['pass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter employee Password';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['rpass']) or ($submittedData['pass'] !== $submittedData['rpass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must repeat the password same as password field';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // make a data array to be saves as in database table
            $saveData = [
                $submittedData['name'],
                $submittedData['addr'],
                $submittedData['contact'],
                $submittedData['uname'],
                DataFilter::getObject()->pwdHash($submittedData['pass']),
                $submittedData['stype'],
                $submittedData['empid']
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('staff_users',
                ['staff_name', 'staff_address', 'staff_contact', 'staff_uname', 'staff_pwd', 'staff_privilage'],
                ['staff_id']
            );
            // insert the data
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully eidted an employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while editing employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        case 'approve':
            if (!isset($opt['eid']) or empty($opt['eid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No employee data found';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('staff_users', ['is_active'], ['staff_id']);
            // insert the data
            $success = DbOperations::getObject()->runQuery([1, $opt['eid']]);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully activated an employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while activating employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        case 'disapprove':
            if (!isset($opt['eid']) or empty($opt['eid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No employee data found';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('staff_users', ['is_active'], ['staff_id']);
            // insert the data
            $success = DbOperations::getObject()->runQuery([0, $opt['eid']]);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully deactivated an employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while deactivating employee';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        default:
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-warning">
                    <div class="panel-heading">View all employees</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=allEmps" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact No</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Contact No</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all employees - JK Imaging Admin Panel';
            break;
    }
} else {
    ob_start();
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-warning">
            <div class="panel-heading">View all employees</div>
            <div class="panel-body">
                <table cellpadding="0" data-get-ajax="respond.php?opt=allEmps" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact No</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact No</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    $pageTitle = 'View all employees - JK Imaging Admin Panel';
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
