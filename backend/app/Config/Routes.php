<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->group('ascots/api/v1', function($routes){
	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login');
		$routes->get('verified/(:any)', 'Auth::verifyAccount/$1');
	}); 

	// User Management
	$routes->group('users', function($routes){
		$routes->post('register', 'Users::registerUser');
		$routes->get('getAllUserList', 'Users::getAllUserList');
		$routes->post('changePassword', 'Users::ChangePassword');
		$routes->post('update/status', 'Users::updateUserStatus');
		$routes->post('getUserById', 'Users::getUserDetails');
	});

	// User Document
	$routes->group('document', function($routes){
		$routes->post('get/attachment', 'UploadDocument::getAttachment');
		$routes->post('get/attachments', 'UploadDocument::getAttachments');
		$routes->post('upload', 'UploadDocument::insertAttachment');
		$routes->post('update/attachment', 'UploadDocument::updateAttachmentStatus');
		$routes->post('delete/attachment', 'UploadDocument::deleteAttachmentStatus');
	});

	// User Scholarship
	$routes->group('announcement', function($routes){
		$routes->post('create/new', 'Announcement::createAnnouncement');
		$routes->post('delete/content', 'Announcement::deleteAnnouncement');
		$routes->get('list', 'Announcement::getList');
	});

	// Announcements
	$routes->group('scholarship', function($routes){
		$routes->post('create/new', 'ScholarShip::createNewScholarship');
		$routes->post('update/details', 'ScholarShip::updateScholarshipDetails');
		$routes->post('submit/application', 'ScholarShip::submitApplication');
		$routes->post('get/details', 'ScholarShip::getScholarshipDetails');
		$routes->get('list', 'ScholarShip::getList');
		$routes->get('list/admin', 'ScholarShip::getListAdmin');
		$routes->post('apply/validate', 'ScholarShip::validateAppliedScholarship');
		$routes->post('apply/getFilled', 'ScholarShip::getFielForm');
		$routes->post('applied/status', 'ScholarShip::getListUserApplied');
		$routes->post('applied/list', 'ScholarShip::getListUserApplications');
		$routes->post('approved/list', 'ScholarShip::getListApproveApplications');
		$routes->post('declined/list', 'ScholarShip::getListDeclinedApplications');
		$routes->post('update/application', 'ScholarShip::updateScholarshipStatus');
		$routes->post('update/scholarship', 'ScholarShip::updateScholarship');
		$routes->post('delete', 'ScholarShip::deleteScholarshipProgram');
	});

	// Miscelenious
	$routes->group('misc', function($routes){
		$routes->get('userTypes', 'Misc::getUserTypes');
		$routes->get('courseList', 'Misc::getCourses');
		$routes->get('providerList', 'Misc::getProviders');
		$routes->post('get/notifications', 'Misc::getUserNotification');
		$routes->post('get/notifications/unseen', 'Misc::getUserNotificationUnseen');
		$routes->post('update/notification', 'Misc::updateNotificationStatus');
		$routes->get('dashboard', 'Misc::getDashboard');
		$routes->post('database/backup', 'BackupController::backupDatabase');
		$routes->post('database/restore', 'BackupController::restoreDatabase');
	});

});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

