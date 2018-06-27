<?php
// common include file required
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
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
                        <div class="panel-heading">Add New Test Details</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="testform" name="testform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveNewTest">
                                <div class="form-group">
                                    <label for="cat" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Select Category : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="cat" name="cat" required="required">
                                            <option value="">Select</option>
                                            <?php
                                            $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                                            $cats = DbOperations::getObject()->fetchData($sql);
                                            foreach ($cats as $value) {
                                            ?>
                                            <option value="<?php echo $value['cat_id'] ?>"><?php echo $value['cat_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tname" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> Test Name : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="tname" name="tname" required="required" autocomplete="off" placeholder="Name of the test">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tprice" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> IP Test Price : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" class="form-control" id="tprice" name="tprice" required="required" autocomplete="off" placeholder="Indoor Patient Price of the test in numbers or decimal" min="0">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tprice" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label"><strong class="text-danger">*</strong> OP Test Price : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-inr"></i></div>
                                            <input type="number" class="form-control" id="otprice" name="otprice" required="required" autocomplete="off" placeholder="Out Patient Price of the test in numbers or decimal" min="0">
                                            <div class="input-group-addon">.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <button type="submit" name="saveDoct" id="saveTest" class="btn btn-default">Save Test</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Add a new test - JK Imaging Admin Panel';
            break;

        // save the employee data posted from the new employee form
        case 'saveNewTest':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['cat']) or empty($submittedData['cat'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must select a test category';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['tname']) or empty($submittedData['tname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the test name';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['tprice']) or is_nan($submittedData['tprice'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the indoor patient test price in numbers';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['otprice']) or is_nan($submittedData['otprice'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the out patient test price in numbers';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // make a data array to be saves as in database table
            $saveData = [
                null,
                ucwords($submittedData['tname']),
                floatval($submittedData['tprice']),
                floatval($submittedData['otprice']),
                $submittedData['cat'],
                $_SESSION['UID'],
                DBTIMESTAMP
            ];
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // insert the data
            DbOperations::getObject()->buildInsertQuery('test_list');
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully added a new test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while adding a test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        
        // show the form to edit the selected employee
        case 'edit':
            if (!isset($opt['tid']) or empty($opt['tid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No test data found';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            $sql = 'select test_id, test_name, test_price, test_oprice, under_cat from test_list where test_id = ?';
            $tData = DbOperations::getObject()->fetchData($sql, [$opt['tid']]);
            ob_start();
            ?>
            <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12 col-lg-offset-2 col-md-offset-2 col-sm-offset-1">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit Test Details</div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" id="testform" name="testform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>?opt=saveEditedTest">
                                <div class="form-group">
                                    <label for="cat" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Under Category : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <select class="form-control" id="cat" name="cat" onchange="loadSubCats(this);">
                                            <option value="">Select</option>
                                            <?php
                                            $sql = 'select cat_id, cat_name from test_cats order by cat_name';
                                            $cats = DbOperations::getObject()->fetchData($sql);
                                            foreach ($cats as $value) {
                                            ?>
                                            <option<?php echo ($tData[0]['under_cat'] === $value['cat_id'] ? ' selected="selected"' : ''); ?> value="<?php echo $value['cat_id'] ?>"><?php echo $value['cat_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tname" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">Test Name : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="text" class="form-control" id="tname" name="tname" required="required" autocomplete="off" value="<?php echo $tData[0]['test_name']; ?>" placeholder="Name of the test">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tprice" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">IP Test Price : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="number" class="form-control" id="tprice" name="tprice" required="required" autocomplete="off" placeholder="Indoor Patient Price of the test in numbers or decimal" min="0" step="0.01" value="<?php echo $tData[0]['test_price']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tprice" class="col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label">OP Test Price : </label>
                                    <div class="col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="number" class="form-control" id="otprice" name="otprice" required="required" autocomplete="off" placeholder="Out Patient Price of the test in numbers or decimal" min="0" step="0.01" value="<?php echo $tData[0]['test_oprice']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-md-offset-4 col-sm-offset-5 col-lg-8 col-md-8 col-sm-7 col-xs-12">
                                        <input type="hidden" name="testid" value="<?php echo $tData[0]['test_id']; ?>">
                                        <button type="submit" name="saveTest" id="saveTest" class="btn btn-default">Save Test</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $pageTitle = 'Edit a test - JK Imaging Admin Panel';
            break;
            
        case 'saveEditedTest':
            // clean the data recieved
            $submittedData = DataFilter::getObject()->cleanData($_POST);
            
            // validate the data
            if (!isset($submittedData['testid']) or empty($submittedData['testid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Whoa, No test found like that';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['cat']) or empty($submittedData['cat'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must select a test category';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['tname']) or empty($submittedData['tname'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the test name';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['tprice']) or is_nan($submittedData['tprice'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the indoor test price in numbers';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            if (!isset($submittedData['otprice']) or is_nan($submittedData['otprice'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'You must enter the outdoor test price in numbers';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // make a data array to be saves as in database table
            $saveData = array(
                ucwords($submittedData['tname']),
                floatval($submittedData['tprice']),
                floatval($submittedData['otprice']),
                $submittedData['cat'],
                $submittedData['testid']
            );
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // update the data
            DbOperations::getObject()->buildUpdateQuery('test_list', ['test_name', 'test_price', 'test_oprice', 'under_cat'], ['test_id']);
            $success = DbOperations::getObject()->runQuery($saveData);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully edited the test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while editing the test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            break;
        // delete test
        case 'del':
            if (!isset($opt['tid']) or empty($opt['tid'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'No test data found to be deleted';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
            // start a transaction with database
            DbOperations::getObject()->transaction('start');
            // update the data
            $sql = 'delete from test_list where test_id = ?';
            DbOperations::getObject()->buildDeleteQuery('test_list', ['test_id']);
            $success = DbOperations::getObject()->runQuery([$opt['tid']]);
            if ($success !== false) {
                // if success commit the transaction and set a message in session
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG']    = 'You have successfully deleted the test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            } else {
                // else rollback the data inserted and set an error message in session
                DbOperations::getObject()->transaction('rollback');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG']    = 'Error occured while deleting the test';
                session_write_close();
                header("Location:" . $_SERVER['HTTP_REFERER']);
                exit(0);
            }
        default:
            ob_start();
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-info">
                    <div class="panel-heading">View all Tests</div>
                    <div class="panel-body">
                        <table cellpadding="0" data-get-ajax="respond.php?opt=allTests" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>IP Price (<i class="fa fa-inr"></i>)</th>
                                    <th>OP Price (<i class="fa fa-inr"></i>)</th>
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
                                    <th>Category</th>
                                    <th>IP Price (<i class="fa fa-inr"></i>)</th>
                                    <th>OP Price (<i class="fa fa-inr"></i>)</th>
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
            $pageTitle = 'View all tests - JK Imaging Admin Panel';
            break;
    }
} else {
    ob_start();
    ?>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-info">
            <div class="panel-heading">View all Tests</div>
            <div class="panel-body">
                <table cellpadding="0" data-get-ajax="respond.php?opt=allTests" cellspacing="0" border="0" class="table table-striped table-bordered table-hover center" id="reportTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>IP Price (<i class="fa fa-inr"></i>)</th>
                            <th>OP Price (<i class="fa fa-inr"></i>)</th>
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
                            <th>Category</th>
                            <th>IP Price (<i class="fa fa-inr"></i>)</th>
                            <th>OP Price (<i class="fa fa-inr"></i>)</th>
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
    $pageTitle = 'View all tests - JK Imaging Admin Panel';
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
