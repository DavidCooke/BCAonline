<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));

/**
 * ImportedUsers Controller
 *
 * @property ImportedUser $ImportedUser
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ImportedUsersController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Session', 'Filter.Filter');

    public $paginate = array(
        'limit' => 100,
        'order' => array('ImportedUser.id' => 'asc')
    );

    public function beforeFilter() {
        parent::beforeFilter();

        //$this->Auth->allow(); // Allow all to public.
    }

    public function isAuthorized($user) {

        //User Admin role can also do the following.
        if ($this->UserUtilities->hasRole(array('UserAdmin'))) {
            if (in_array($this->action, array('admin_index', 'admin_view', 'admin_add', 'admin_edit', 'admin_delete',
                    'admin_upload_file', 'admin_process_file', 'admin_update_users', 'admin_delete_all'))) {

                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /*
     * Filter Plugin stuff.
     */
    var $filters = array (
        'admin_index' => array (
            'ImportedUser' => array (
                'ImportedUser.bca_no' => array('label' => 'BCA No', 'condition' => '=', 'type' => 'text'),
                'ImportedUser.surname',
                'ImportedUser.organisation',
            )
        )
    );

public function admin_testxls() {

    $folderToSaveXls = '/';

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getProperties()->setCreator("David Cooke")
                         ->setLastModifiedBy("David Cooke")
                         ->setTitle("PHPExcel Test Document")
                         ->setSubject("PHPExcel Test Document")
                         ->setDescription("Test document for PHPExcel, generated using PHP classes.")
                         ->setKeywords("office PHPExcel php")
                         ->setCategory("Test result file");

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Hello')
        ->setCellValue('B2', 'world!')
        ->setCellValue('C1', 'Hello')
        ->setCellValue('D2', 'world!');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //$objWriter->save( $folderToSaveXls . '/test.xls' );
    $objWriter->save( 'test.xls' );



    }


    /**
     * index method
     *
     * @return void
     *
    public function index() {
        $this->ImportedUser->recursive = 0;
        $this->set('importedUsers', $this->Paginator->paginate());
    }


    /**
     * admin_index method
     *
     * @return void
     */
    public function admin_index() {

        //Empty is allowed for the search filter.
        //Can't set in the filter configuration, so override the model's validation here.
        $this->ImportedUser->validate['bca_no']['allowEmpty'] = true;
        $this->ImportedUser->validate['organisation']['allowEmpty'] = true;

        $this->Paginator->settings = $this->paginate;

        $this->ImportedUser->recursive = 0;
        $this->set('importedUsers', $this->Paginator->paginate());
    }

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_view($id = null) {
        if (!$this->ImportedUser->exists($id)) {
            throw new NotFoundException(__('Invalid user import'));
        }
        $options = array('conditions' => array('ImportedUser.' . $this->ImportedUser->primaryKey => $id));
        $this->set('importedUser', $this->ImportedUser->find('first', $options));
    }

    /**
     * admin_add method
     *
     * @return void
     */
    public function admin_add() {
        if ($this->request->is('post')) {
            $this->ImportedUser->create();
            if ($this->ImportedUser->save($this->request->data)) {
                $this->Session->setFlash(__('The user import has been saved.'), 'default', array('class' => 'success'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user import could not be saved. Please, try again.'));
            }
        }
    }

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_edit($id = null) {
        if (!$this->ImportedUser->exists($id)) {
            throw new NotFoundException(__('Invalid user import'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->ImportedUser->save($this->request->data)) {
                $this->Session->setFlash(__('The user import has been saved.'), 'default', array('class' => 'success'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The user import could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('ImportedUser.' . $this->ImportedUser->primaryKey => $id));
            $this->request->data = $this->ImportedUser->find('first', $options);
        }
    }

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_delete($id = null) {
        $this->ImportedUser->id = $id;
        if (!$this->ImportedUser->exists()) {
            throw new NotFoundException(__('Invalid user import'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->ImportedUser->delete()) {
            $this->Session->setFlash(__('The user import has been deleted.'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('The user import could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function admin_delete_all() {

        if ($this->ImportedUser->deleteAll('1=1', false)) {
            $this->Session->setFlash(__('All user import records have been deleted.'), 'default', array('class' => 'success'));
        } else {
            $this->Session->setFlash(__('Not all user import records were deleted.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * admin_import_errors
     *
     *
     */

    function admin_import_errors() {}

    /**
     * admin_upload method
     *
     * @return void
     */
    public function admin_upload_file() {

        if(!empty($this->request->data)) {

            //Check if file has been uploaded.
            if(!empty($this->request->data['ImportedUser']['upload']['name'])) {

                $this->ImportedUser->set($this->request->data);
                //debug($this->ImportedUser); die;

                $upload = $this->request->data['ImportedUser']['upload'];
                //debug($file); die;

                $ext = substr(strtolower(strrchr($upload['name'], '.')),1); //Get the file extension.
                $allowed_ext = array('csv', 'txt');

                //DEV!!! should have more checks.
                if(!in_array($ext, $allowed_ext)) {
                    $this->Session->setFlash(__('Sorry, the file must be a .csv or .txt file.'));
                    return;
                }

                //Upload file. DEV!!! don't use same name for all.
                if(move_uploaded_file($upload['tmp_name'], WWW_ROOT . 'files/upload.csv')) {
                    $this->Session->setFlash(__('The upload succeeded.'), 'default', array('class' => 'success'));
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('The upload failed.'));
                }
            } else {
                $this->Session->setFlash(__('No file was uploaded.'));
            }
        }
    }

    function admin_process_file() {

        //Give ourselves plenty of time and memory.
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '64M');

        if (!($this->data = $this->ImportedUser->importCsv(WWW_ROOT .'files/upload.csv'))) {
            $this->Session->setFlash(__('Importing of the uploaded file failed.'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!$this->ImportedUser->saveMany($this->data)) {
            //Display errors.

            //Find Name & BCA_no to go with the errors.
            foreach ($this->ImportedUser->validationErrors as $line => $errors) {

                $importedUser[$line] = array(
                    'class' => $this->data[$line]['ImportedUser']['class'],
                    'bca_no' => $this->data[$line]['ImportedUser']['bca_no'],
                    'forename' => $this->data[$line]['ImportedUser']['forename'],
                    'surname' => $this->data[$line]['ImportedUser']['surname'],
                    'organisation' => $this->data[$line]['ImportedUser']['organisation']);
            }

            $this->set('importedUser', $importedUser);
            $this->set('importedUserErrors', $this->ImportedUser->validationErrors);

            //Email any errors to the Administrator.
            $this->loadmodel('SentEmail');

            $viewVars = array(
                'full_name' => $this->Auth->user('full_name'),
                'importedUser' => $importedUser,
                'importedUserErrors' => $this->ImportedUser->validationErrors,
            );

            $email = array(
                'user_id' => $this->Auth->user('id'),
                //'bca_no' => $this->Auth->user('bca_no'),
                //'to' => $configEmailAddresses['bca_online_admin'],
                'subject' => 'BCA Online file import errors.',
                'template' => 'imported_users-admin_process_file',
                'forceSend' => true,
                'save' => false,
                'viewVars' => $viewVars,
            );
            $this->SentEmail->send($email);

            $this->Session->setFlash(__('Saving of the imported data failed.'));
            return;
        }

        $this->Session->setFlash(__('Processing of the imported file succeeded.'), 'default', array('class' => 'success'));
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * admin_update_users
     *
     *
     */

    function admin_update_users() {

        $this->loadModel('User');
        $this->loadModel('SentEmail');

        //Get email config.
        if(!$configEmailAddresses = Configure::read('EmailAddresses')) {
            throw new NotFoundException(__('Invalid email configuration'));
        }

        $process_count = $add_count = $update_count = $c1 = $c2 = 0;
        $batch_size = 100;

        while ($importedUsers = $this->ImportedUser->find('all',
            array('order' => 'id', 'offset' => $c1 * $batch_size, 'limit' => $batch_size, 'recursive' => -1))) {

            set_time_limit(120); //Reset PHPs timeout counter.

            $batchCount = count($importedUsers);

            for ($c2 = 0; $c2 < $batchCount; $c2++) {

                $importedUser = $importedUsers[$c2];

                $process_count++;

                //Don't add, update or compare these fields.
                unset($importedUser['ImportedUser']['id']);
                unset($importedUser['ImportedUser']['created']);
                unset($importedUser['ImportedUser']['modified']);

                //Find corresponding User.
                //NB These 3 criteria should give a unique match.
                $conditions = array(
                    'User.class' => $importedUser['ImportedUser']['class'],
                    'User.bca_no' => $importedUser['ImportedUser']['bca_no'],
                    'User.organisation' => $importedUser['ImportedUser']['organisation']
                );

                //If User exists.
                if ($user = $this->User->find('first', array('conditions' => $conditions, 'contain' => false))) {

                    //Don't update any records already marked as Deceased.
                    if ($user['User']['bca_status'] == 'Deceased'){
                        continue;
                    }

                    //Don't update or compare these fields because they can overwrite changes made online.
                    unset($importedUser['ImportedUser']['bca_email_ok']);
                    unset($importedUser['ImportedUser']['bcra_email_ok']);

                    //Find the values in ImportedUser that aren't in User. Case insensitive. DEV!!! Might have trouble with integers.
                    if($changes = array_udiff_assoc($importedUser['ImportedUser'], $user['User'], 'strcasecmp')) {

                        //The import can not clear the email address of an active user but can update it.
                        if ($user['User']['active'] && isset($changes['email']) && empty($changes['email'])) {
                            unset($changes['email'], $importedUser['ImportedUser']['email']);
                        }

                        // If email address has changed send a notification to the old address.
                        // NB Before save() so isEmailable() isn't affected by the update.
                        if (isset($changes['email'])) {

                            $viewVars = array(
                                'full_name' => $user['User']['full_name'],
                                'new_email' => $changes['email'],
                                'bca_online_admin_email' => $configEmailAddresses['bca_online_admin'],
                            );

                            $email = array(
                                'user_id' => $user['User']['id'],
                                //'to' => $user['User']['email'],
                                'subject' => 'Your BCA Online email address has been changed. (Ref: '. $user['User']['bca_no'] . ')',
                                'template' => 'imported_users-admin_update_users-email_updated',
                                'viewVars' => $viewVars,
                            );

                            $this->SentEmail->send($email);
                        }

                        //Update User if any changes.
                        if (!empty($changes)) {
                            $importedUser['ImportedUser']['id'] = $user['User']['id'];
                            $this->User->clear();

                            if(!$this->User->save($importedUser['ImportedUser'])) {

                                //Save errors to display at end of batch.
                                $updateErrors[] = array(
                                    'imported_user_id' => $importedUsers[$c2]['ImportedUser']['id'],
                                    'bca_no' =>$importedUsers[$c2]['ImportedUser']['bca_no'],
                                    'full_name' =>$importedUsers[$c2]['ImportedUser']['full_name'],
                                    'validation_error' => $this->User->validationErrors);

                                continue;

                            } else {
                                //Keep total of records updated.
                                $update_count++;
                            }

                            //// Send email to notify user of import changes.

                            //Check for notifyable changes not including changes of case.
                            $changes2 = array_udiff_assoc($importedUser['ImportedUser'], $user['User'], 'strcasecmp');

                            //Don't want these to show up in the notification emails.
                            unset($changes2['full_name']); //Virtual field.
                            unset($changes2['bca_status']); //Change of status is normally notified by another route.
                            unset($changes2['date_of_expiry'], $changes2['address_ok']);
                            unset($changes2['forename2'], $changes2['surname2'], $changes2['bca_no2']);
                            unset($changes2['insurance_status2'], $changes2['class_code2']); //!!!DEV These should be removed when gone from table.

                            //Add in whole address if any part has changed.
                            if(array_key_exists('address1', $changes2) || array_key_exists('address2', $changes2) ||
                                array_key_exists('address3', $changes2) || array_key_exists('town', $changes2) ||
                                array_key_exists('county', $changes2) || array_key_exists('postcode', $changes2) ||
                                array_key_exists('country', $changes2)) {

                                //Add in keys to be used below.
                                $changes2['address1'] = $changes2['address2'] = $changes2['address3'] = $changes2['town'] = "";
                                $changes2['county'] = $changes2['postcode'] = $changes2['country'] = "";
                            }

                            //Find the corresponding current values in the right order (as per database).
                            $email_changes = array_intersect_key($importedUser['ImportedUser'], $changes2);

                            //Find the corresponding previous values.
                            $email_previous = array_intersect_key($user['User'], $changes2);

                            if (!empty($email_changes)) {
                                $viewVars = array(
                                    'full_name' => $importedUser['ImportedUser']['full_name'],
                                    'membership_admin_email' => $configEmailAddresses['membership_admin'],
                                    'changes' => $email_changes,
                                    'previous' => $email_previous,
                                );

                                $email = array(
                                    'user_id' => $user['User']['id'],
                                    'subject' => 'Your BCA Online profile has been updated. (Ref: '. $user['User']['bca_no'] . ')',
                                    'template' => 'imported_users-admin_update_users-user_updated',
                                    'viewVars' => $viewVars,
                                );

                                $this->SentEmail->send($email);
                            }
                        }
                    }

                } else {
                    //Add new user.

                    // Set username (bca_no padded with leading zeros).
                    $importedUser['ImportedUser']['username'] = User::MakeUserName($importedUser['ImportedUser']['bca_no']);

                    if (empty($importedUser['ImportedUser']['username'])) {

                        //Save errors to display at end of batch.
                        $updateErrors[] = array(
                            'imported_user_id' => $importedUsers[$c2]['ImportedUser']['id'],
                            'bca_no' =>$importedUsers[$c2]['ImportedUser']['bca_no'],
                            'full_name' =>$importedUsers[$c2]['ImportedUser']['full_name'],
                            'validation_error' => 'Empty username');

                        continue;
                    }

                    $this->User->create();
                    if(!$this->User->save($importedUser['ImportedUser'])) {

                        //Save errors to display at end of batch.
                        $updateErrors[] = array(
                            'imported_user_id' => $importedUsers[$c2]['ImportedUser']['id'],
                            'bca_no' =>$importedUsers[$c2]['ImportedUser']['bca_no'],
                            'full_name' =>$importedUsers[$c2]['ImportedUser']['full_name'],
                            'validation_error' => $this->User->validationErrors);

                        continue;

                    } else {
                        //Keep total of records added.
                        $add_count++;
                    }

                    // NB no notifying email for new users. User needs to activate account before receiving emails.
                }
            }

            //Display any errors and stop processing.
            if (!empty($updateErrors)) {
                $this->set('updateErrors', $updateErrors);
                $this->Session->setFlash(__('Saving of the data failed.'));
                return $this->render();
            }

            $c1++; //Do next batch.
        }

        $this->Session->setFlash("Processed {$process_count}, added {$add_count} and updated {$update_count} records.", 'default', array('class' => 'success'));

        return $this->redirect(array('action' => 'index'));

    }

    /**
     * admin_report_repeated_lines
     *
     * Lists repeated records in the import file.
     */

    function admin_report_repeated_lines() {

        $fields = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class', 'COUNT(*) as row_count',
            'GROUP_CONCAT(ImportedUser.forename) as forenames', 'GROUP_CONCAT(ImportedUser.surname) as surnames');

        //A condition on an aggregate function must use HAVING.
        $group = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class HAVING COUNT(*) > 1');

        $order = array('ImportedUser.class', 'ImportedUser.organisation', 'ImportedUser.bca_no');

        $repeatedLines = $this->ImportedUser->find('all', array(
            'fields' => $fields,
            'group' => $group,
            'order' => $order,
            //'limit' => 10,
        ));

        $this->set('repeatedLines', $repeatedLines);
    }

    /**
     * admin_tidy_repeats
     *
     * Deletes the records records from the Import.
     */

    function admin_tidy_repeats() {

        $this->request->onlyAllow('post');

        $fields = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class', 'COUNT(*) as row_count');

        //A condition on an aggregate function must use HAVING.
        $group = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class HAVING COUNT(*) > 1');

        if ($repeatedLines = $this->ImportedUser->find('all', array(
            'fields' => $fields,
            'group' => $group,
            )))
        {
            $line_count = count($repeatedLines);

            for ($c1 = 0; $c1 < $line_count; $c1++) {

                $conditions = array('ImportedUser.bca_no' => $repeatedLines[$c1]['ImportedUser']['bca_no'],
                    'ImportedUser.organisation' => $repeatedLines[$c1]['ImportedUser']['organisation'],
                    'ImportedUser.class' => $repeatedLines[$c1]['ImportedUser']['class'],);

                $this->ImportedUser->deleteAll($conditions);
            }

            $this->Session->setFlash(__($line_count .' repeated records have has been deleted.'), 'default', array('class' => 'success'));

        } else {
            $this->Session->setFlash(__('There was no data to delete.'));
        }

        return $this->redirect(array('action' => 'report_repeats'));
    }

    /**
     * admin_report_new_mismatched_names
     *
     * Lists records in the import file that have different names from the master database
     * and are not already in the master database.
     */

     /* SELECT DISTINCT u1.bca_no, u2.bca_no, u2.forename, u2.surname, u2.organisation, u2.class, u1.forename, u1.surname, u1.organisation, u1.class
      * FROM imported_users AS u1, users AS u2
      * WHERE (((u1.bca_no)=[u2].[bca_no]) AND
      * ((Exists (SELECT *
      * FROM users  AS u3
      * WHERE u1.bca_no=u3.bca_no AND u1.forename=u3.forename AND u1.surname=u3.surname))=False) AND
      * ((Exists (SELECT *
      * FROM users  AS u4
      * WHERE u1.bca_no=u4.bca_no AND (u1.forename<>u4.forename or u1.surname<>u4.surname)))<>False))
      * ORDER BY u1.bca_no;
    */
    function admin_report_new_mismatched_names() {

        $mySQL = 'SELECT DISTINCT ImportedUser.bca_no, User.bca_no, User.forename, User.surname, User.organisation,
            User.class, ImportedUser.forename, ImportedUser.surname, ImportedUser.organisation, ImportedUser.class
            FROM imported_users AS ImportedUser, users AS User
            WHERE
            (((ImportedUser.bca_no)=[User].[bca_no])
            AND ((Exists (SELECT *
            FROM users  AS u3
            WHERE ImportedUser.bca_no=u3.bca_no AND ImportedUser.forename=u3.forename AND ImportedUser.surname=u3.surname))=False)
            AND ((Exists (SELECT *
            FROM users  AS u4
            WHERE ImportedUser.bca_no=u4.bca_no AND (ImportedUser.forename<>u4.forename or ImportedUser.surname<>u4.surname)))<>False))
            LIMIT 10
            ORDER BY ImportedUser.bca_no;';

        $mySQL2 = 'SELECT ImportedUser.bca_no FROM imported_users AS ImportedUser,
        users AS User WHERE ImportedUser.bca_no = User.bca_no LIMIT 10';

        $mySQL3 = 'SELECT DISTINCT ImportedUser.bca_no, User.bca_no, User.forename, User.surname, User.organisation,
            User.class, ImportedUser.forename, ImportedUser.surname, ImportedUser.organisation, ImportedUser.class
            FROM imported_users AS ImportedUser, users AS User
            WHERE
            ImportedUser.bca_no=User.bca_no
            AND Exists (SELECT *
                FROM users AS u3
                WHERE ImportedUser.bca_no=u3.bca_no AND
                    ImportedUser.forename=u3.forename AND
                    ImportedUser.surname=u3.surname)
            AND Exists (SELECT *
                FROM users AS u4
                WHERE ImportedUser.bca_no=u4.bca_no AND
                    (ImportedUser.forename<>u4.forename or ImportedUser.surname<>u4.surname))
            LIMIT 10
            ORDER BY ImportedUser.bca_no;';

        $mySQL4 = 'SELECT DISTINCT ImportedUser.bca_no, User.bca_no, User.forename, User.surname,
                User.organisation, User.class, User.address1, User.address2, User.email,
                ImportedUser.forename, ImportedUser.surname,
                ImportedUser.organisation, ImportedUser.class, ImportedUser.address1,
                ImportedUser.address2, ImportedUser.email
            FROM imported_users AS ImportedUser, users AS User
            WHERE
                ImportedUser.bca_no=User.bca_no
            AND Not Exists (SELECT u3.bca_no
                FROM users AS u3
                WHERE ImportedUser.bca_no=u3.bca_no AND
                    ImportedUser.forename=u3.forename AND
                    ImportedUser.surname=u3.surname)
            AND Exists (SELECT u4.bca_no
                FROM users AS u4
                WHERE ImportedUser.bca_no=u4.bca_no AND
                    (ImportedUser.forename<>u4.forename or ImportedUser.surname<>u4.surname))
            ORDER BY ImportedUser.bca_no';
            //LIMIT 10';

        $db = $this->ImportedUser->getDataSource();

        $mismatchedLines = $db->fetchALL($mySQL4);

    /*
        $fields = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class',
            'ImportedUser.forename', 'ImportedUser.surname', 'ImportedUser.address1', 'ImportedUser.address2',
            'User.bca_no', 'User.organisation', 'User.class', 'User.forename', 'User.surname',
            'User.address1', 'User.address2');

        $joins = array(array('table' => 'users', 'alias' => 'User',
            'type' => 'inner', 'conditions' => array('ImportedUser.bca_no = User.bca_no')));

        //$order = array('ImportedUser.class', 'ImportedUser.organisation', 'ImportedUser.bca_no');
        $order = array('ImportedUser.bca_no');

        $conditions = array('or' => array('ImportedUser.forename <> User.forename', 'ImportedUser.surname <> User.surname'));

        $mismatchedLines = $this->ImportedUser->find('all', array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions,
            'order' => $order,
            //'limit' => 10,
        ));
*/
        $this->set('mismatchedLines', $mismatchedLines);

    }

    /**
     * admin_report_mismatched_names
     *
     * Lists records in the import file that have different names from the master database.
     */

    function admin_report_mismatched_names() {

        $fields = array('ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class',
            'ImportedUser.forename', 'ImportedUser.surname', 'ImportedUser.address1', 'ImportedUser.address2',
            'User.bca_no', 'User.organisation', 'User.class', 'User.forename', 'User.surname',
            'User.address1', 'User.address2');

        $joins = array(array('table' => 'users', 'alias' => 'User',
            'type' => 'inner', 'conditions' => array('ImportedUser.bca_no = User.bca_no')));

        //$order = array('ImportedUser.class', 'ImportedUser.organisation', 'ImportedUser.bca_no');
        $order = array('ImportedUser.bca_no');

        $conditions = array('or' => array('ImportedUser.forename <> User.forename', 'ImportedUser.surname <> User.surname'));

        $mismatchedLines = $this->ImportedUser->find('all', array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions,
            'order' => $order,
            //'limit' => 10,
        ));

        $this->set('mismatchedLines', $mismatchedLines);

    }

    /**
     * admin_tidy_mismatched_names
     *
     * Deletes the mismatching name records from the Import.
     */

    function admin_tidy_mismatched_names() {

        $this->request->onlyAllow('post');

        $fields = array('ImportedUser.id');

        $joins = array(array('table' => 'users', 'alias' => 'User',
            'type' => 'inner', 'conditions' => array('ImportedUser.bca_no = User.bca_no')));

        $conditions = array('ImportedUser.forename <> User.forename', 'ImportedUser.surname <> User.surname');

        if ($mismatchedLines = $this->ImportedUser->find('all', array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions,
            )))
        {
            $line_count = count($mismatchedLines);

            for ($c1 = 0; $c1 < $line_count; $c1++) {
                $this->ImportedUser->delete($mismatchedLines[$c1]['ImportedUser']['id']);
            }

            $this->Session->setFlash(__($line_count .' mismatched records have has been deleted.'), 'default', array('class' => 'success'));

        } else {
            $this->Session->setFlash(__('There was no data to delete.'));
        }

        return $this->redirect(array('action' => 'report_mismatched_names'));
    }

    /*
     * admin_report_users_to_be_updated
     *
     * Shows the users that will be updated by the import.
     */

    function admin_report_users_to_be_updated() {

        $fields = array(
            'ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class',
            'ImportedUser.class_code', 'ImportedUser.bca_status', 'ImportedUser.insurance_status',
            'ImportedUser.date_of_expiry',
            'ImportedUser.email', 'ImportedUser.forename', 'ImportedUser.surname',
            'ImportedUser.address1', 'ImportedUser.address2', 'ImportedUser.address3', 'ImportedUser.town',
            'ImportedUser.county', 'ImportedUser.postcode', 'ImportedUser.country',
            'User.bca_no', 'User.organisation', 'User.class', 'User.class_code', 'User.bca_status',
            'User.insurance_status',
            'User.date_of_expiry', 'User.email', 'User.forename', 'User.surname', 'User.address1',
            'User.address2', 'User.address3', 'User.town', 'User.county', 'User.postcode', 'User.country',
        );

        $joins = array(array('table' => 'users', 'alias' => 'User',
            'type' => 'inner', 'conditions' => array(
                'ImportedUser.class = User.class',
                'ImportedUser.bca_no = User.bca_no',
                'ImportedUser.organisation = User.organisation'))
        );

        $order = array('ImportedUser.class', 'ImportedUser.organisation', 'ImportedUser.bca_no');

        $conditions = array('or' => array(
            'ImportedUser.class_code <> User.class_code',
            'ImportedUser.bca_status <> User.bca_status',
            'ImportedUser.insurance_status <> User.insurance_status',
            'ImportedUser.date_of_expiry <> User.date_of_expiry',
            'ImportedUser.email <> User.email',
            'ImportedUser.forename <> User.forename',
            'ImportedUser.surname <> User.surname',
            'ImportedUser.address1 <> User.address1',
            'ImportedUser.address2 <> User.address2',
            'ImportedUser.address3 <> User.address3',
            'ImportedUser.town <> User.town',
            'ImportedUser.county <> User.county',
            'ImportedUser.postcode <> User.postcode',
            'ImportedUser.country <> User.country',)
        );

        $updatedLines = $this->ImportedUser->find('all', array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions,
            'order' => $order,
            'limit' => 1000,
        ));

        $this->set('updatedLines', $updatedLines);
    }

    /*
     * admin_report_users_to_be_added
     *
     * Shows the new users that will be added by the import.
     */

    function admin_report_users_to_be_added() {

        $fields = array(
            'ImportedUser.bca_no', 'ImportedUser.organisation', 'ImportedUser.class',
            'ImportedUser.class_code', 'ImportedUser.insurance_status', 'ImportedUser.date_of_expiry',
            'ImportedUser.email', 'ImportedUser.forename', 'ImportedUser.surname',
            'ImportedUser.address1', 'ImportedUser.address2', 'ImportedUser.address3', 'ImportedUser.town',
            'ImportedUser.county', 'ImportedUser.postcode', 'ImportedUser.country',
            'User.bca_no',
        );

        $joins = array(array('table' => 'users', 'alias' => 'User',
            'type' => 'left', 'conditions' => array(
                'ImportedUser.class = User.class',
                'ImportedUser.bca_no = User.bca_no',
                'ImportedUser.organisation = User.organisation'))
        );

        $order = array('ImportedUser.class', 'ImportedUser.organisation', 'ImportedUser.bca_no');

        $conditions = array('or' => array(
            'User.bca_no is null')
        );

        $addedLines = $this->ImportedUser->find('all', array(
            'joins' => $joins,
            'fields' => $fields,
            'conditions' => $conditions,
            'order' => $order,
            'limit' => 250,
        ));

        $this->set('addedLines', $addedLines);
    }
}
