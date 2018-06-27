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
        case 'viewProfile':
            $sql = 'select staff_name, staff_address, staff_contact, staff_uname, staff_privilage, staff_created from staff_users where staff_id = ?';
            $sData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Your Profile Details</div>
                        <div class="panel-body">
                                
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Name: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo $sData[0]['staff_name']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Contact No.: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo $sData[0]['staff_contact']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Address: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo $sData[0]['staff_address']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Username: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo $sData[0]['staff_uname']; ?>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Type: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo ($sData[0]['staff_privilage'] === '1' ? 'Administrator' : 'Staff'); ?>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Created At: </div>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <?php echo date('d/m/Y h:i:s A', strtotime($sData[0]['staff_created'])); ?>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View own profile - JK Imaging Admin Panel';
            break;

        // save the employee data posted from the new employee form
        case 'saveEditedProfile':
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
            if (count($present) > 1) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'This username already in use. Please choose another';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // check if old password is correct
            $oldPass = DbOperations::getObject()->fetchData('select staff_pwd from staff_users where staff_id = ?', [$_SESSION['UID']]);
            if ($oldPass[0]['staff_pwd'] !== DataFilter::getObject()->pwdHash($submittedData['opass'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'The old password is incorrect, please try again';
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
                $_SESSION['UID']
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // insert the data
            DbOperations::getObject()->buildUpdateQuery(
                'staff_users',
                ['staff_name', 'staff_address', 'staff_contact', 'staff_uname', 'staff_pwd'],
                ['staff_id']
            );
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully edited your profile data';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while editing profile';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        
        // show the form to edit the selected employee
        case 'editProfile':
            $sql = 'select staff_name, staff_address, staff_contact, staff_uname from staff_users where staff_id = ?';
            $sData = DbOperations::getObject()->fetchData($sql, [$_SESSION['UID']]);
            
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="row">
                    <div class="panel panel-danger">
                        <div class="panel-heading">Edit Profile</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="empform" name="empform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedProfile">
                                <div class="form-group">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Name: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="name" name="name" required="required" placeholder="Full Name" value="<?php echo $sData[0]['staff_name']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="contact" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Contact No.: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="contact" required="required" name="contact" placeholder="Contact Number" value="<?php echo $sData[0]['staff_contact']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="addr" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Address: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <textarea class="form-control" id="addr" name="addr" required="required" placeholder="Address"><?php echo $sData[0]['staff_address']; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="uname" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Username: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="uname" required="required" name="uname" placeholder="Desired Username" value="<?php echo $sData[0]['staff_uname']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="opass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Old Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="opass" name="opass" required="required" placeholder="Old Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> New Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="pass" name="pass" required="required" placeholder="Desired New Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="rpass" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Re-Password: </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="password" class="form-control" id="rpass" name="rpass" required="required" placeholder="Re-enter above New Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <button type="submit" name="saveProfile" id="saveProfile" class="btn btn-default">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Edit profile - JK Imaging Admin Panel';
            break;
        
        case 'deleted':
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all deleted patients</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=delPats" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
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
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all deleted patients - JK Imaging Admin Panel';
            break;
        
        case 'anotherDate':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">Select a date to view patient details</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="anotherpatform" name="anotherpatform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=showAnotherDatePatients">
                            <div class="form-group">
                                <label for="pdate" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="pdate" name="pdate" required="required" autocomplete="off" placeholder="Date to show">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="savePat" id="savePat" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Select a date to view the patients - JK Imaging Admin Panel';
            break;
        
        case 'showAnotherDatePatients':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if(!isset($submittedData['pdate']) or ($submittedData['pdate'] === '')){
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You did not select a date';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $timestamp = strtotime($submittedData['pdate']);
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all patients entered on Date: <?php echo date('d-m-Y', $timestamp); ?></div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=showAnotherDatePatients&ts=<?php print($timestamp); ?>" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <!--<th>ID</th>-->
                                    <th>Name</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Price (<span class="fa fa-rupee"></span>)</th>
                                    <th>Patient ID / Bill No</th>
									<th>Created By</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Price (<span class="fa fa-rupee"></span>)</th>
                                    <th>Patient ID / Bill No</th>
									<th>Created By</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all patients entered on a specified date - JK Imaging Admin Panel';
            break;
        
            case 'rangeDate':
            ob_start();
            ?>
            <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 col-lg-offset-3 col-md-offset-3 col-sm-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">Select a date to view patient details</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" id="anotherpatform" name="anotherpatform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=showRangeDatePatients">
                            <div class="form-group">
                                <label for="sdate" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Start Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="sdate" name="sdate" required="required" autocomplete="off" placeholder="Start Date to show">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edate" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">End Date: </label>
                                <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <input type="text" class="form-control datepicker" data-date-format="DD-MM-YYYY" id="edate" name="edate" required="required" autocomplete="off" placeholder="End Date to show">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                    <button type="submit" name="savePat" id="savePat" class="btn btn-default">Show Details</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Select a date range to view the patients - JK Imaging Admin Panel';
            break;
        
        case 'showRangeDatePatients':
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            if(!isset($submittedData['sdate']) or ($submittedData['sdate'] === '')){
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You did not select a start date';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if(!isset($submittedData['edate']) or ($submittedData['edate'] === '')){
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You did not select a end date';
                session_write_close();
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $stimestamp = strtotime($submittedData['sdate']);
            $etimestamp = strtotime($submittedData['edate']);
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all patients entered on Date Range: <?php echo date('d-m-Y', $stimestamp); ?> to <?php echo date('d-m-Y', $etimestamp); ?></div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=showRangeDatePatients&sts=<?php print($stimestamp); ?>&ets=<?php print($etimestamp); ?>" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Price (<span class="fa fa-rupee"></span>)</th>
                                    <th>Status</th>
                                    <th>Date - Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Price (<span class="fa fa-rupee"></span>)</th>
                                    <th>Status</th>
                                    <th>Date - Time</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all patients entered in between a date range - JK Imaging Admin Panel';
            break;
            
        case 'personal':
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-success">
                    <div class="panel-heading">View all patients entered by you</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=mySelf" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
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
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all patients entered by you - JK Imaging Admin Panel';
            break;
        default:
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all patients</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=allPats" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
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
                                    <th>Age &amp; Sex</th>
                                    <th>Contact</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'View all patients - JK Imaging Admin Panel';
            break;
    }
} else {
    ob_start();
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading">View all patients added today</div>
            <div class="panel-body">
                <table cellpadding="0" data-get-ajax="respond.php?opt=allPatsToday" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age &amp; Sex</th>
                            <th>Contact</th>
                            <th>Price (<span class="fa fa-rupee"></span>)</th>
                            <th>Patient ID / Bill No</th>
                                    <th>Created By</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Age &amp; Sex</th>
                            <th>Contact</th>
                            <th>Price (<span class="fa fa-rupee"></span>)</th>
                            <th>Patient ID / Bill No</th>
                                    <th>Created By</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php
    $pageTitle = 'View all patients today - JK Imaging Admin Panel';
}
// get contents from buffer
$contents = ob_get_contents();
// clean and end the buffer
ob_end_clean();

$replacementArray = [
    'PageTitle' => $pageTitle,
    'ErrorMessages' => getAlertMsg(),
    'CenterContents' => $contents,
    'CSSHelpers' => ['bootstrap.min.css', 'bootstrap-theme.min.css', 'font-awesome.min.css', 'bootstrap-datetimepicker.min.css', 'dataTables.bootstrap.min.css', 'custom.min.css'],
    'JSHelpers' => ['jquery.min.js', 'bootstrap.min.js', 'bootstrap-typeahead.min.js', 'moment.min.js', 'bootstrap-datetimepicker.min.js', 'jquery.dataTables.min.js', 'dataTables.bootstrap.min.js', 'custom.min.js']
];

assignTemplate($replacementArray);
// the ending php tag has been intentionally not used to avoid unwanted whitespaces before document starts
