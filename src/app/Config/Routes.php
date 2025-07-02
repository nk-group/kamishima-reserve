<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// デフォルトホームページ
$routes->get('/', 'Home::index');

// Shield認証ルート
service('auth')->routes($routes);

// 管理者/スタッフ向けルート
$routes->group('admin', ['filter' => 'sessionauth', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    
    // ダッシュボード
    $routes->group('dashboard', static function ($routes) {
        $routes->get('/', 'DashboardController::index', ['as' => 'admin.dashboard']);
        $routes->get('calendar-data', 'DashboardController::calendarData', ['as' => 'admin.dashboard.calendar-data']);
        $routes->get('today-reservations-more', 'DashboardController::todayReservationsMore', ['as' => 'admin.dashboard.today-reservations-more']);
        $routes->get('calendar-table', 'DashboardController::calendarTable', ['as' => 'admin.dashboard.calendar-table']);
    });

    // 個人設定（全ログインユーザー共通）
    $routes->group('user-preferences', static function ($routes) {
        $routes->get('/', 'UserPreferencesController::index', ['as' => 'admin.user-preferences.index']);
        $routes->post('save', 'UserPreferencesController::save', ['as' => 'admin.user-preferences.save']);
    });

    // 予約管理（staff権限）
    $routes->group('reservations', ['filter' => 'permission:staff.access'], static function ($routes) {
        $routes->get('/', 'ReservationController::index', ['as' => 'admin.reservations.index']);
        $routes->get('export-csv', 'ReservationController::exportCsv', ['as' => 'admin.reservations.export-csv']);
        $routes->get('new', 'ReservationController::new', ['as' => 'admin.reservations.new']);
        $routes->post('create', 'ReservationController::create', ['as' => 'admin.reservations.create']);
        $routes->get('edit/(:num)', 'ReservationController::edit/$1', ['as' => 'admin.reservations.edit']);
        $routes->post('update/(:num)', 'ReservationController::update/$1', ['as' => 'admin.reservations.update']);
        $routes->post('delete/(:num)', 'ReservationController::delete/$1', ['as' => 'admin.reservations.delete']);
    });

    // 帳票機能（staff権限）
    $routes->group('reports', ['filter' => 'permission:staff.access'], static function ($routes) {
        $routes->get('arrival-schedule', 'ReportsController::arrivalSchedule', ['as' => 'admin.reports.arrival-schedule']);
        $routes->get('work-instruction-card', 'ReportsController::workInstructionCard', ['as' => 'admin.reports.work-instruction-card']);
    });

    // 定休日管理（staff権限）
    $routes->group('shop-closing-days', ['filter' => 'permission:staff.access'], static function ($routes) {
        $routes->get('/', 'ShopClosingDayController::index', ['as' => 'admin.shop-closing-days.index']);
        $routes->get('new', 'ShopClosingDayController::new', ['as' => 'admin.shop-closing-days.new']);
        $routes->post('create', 'ShopClosingDayController::create', ['as' => 'admin.shop-closing-days.create']);
        $routes->get('edit/(:num)', 'ShopClosingDayController::edit/$1', ['as' => 'admin.shop-closing-days.edit']);
        $routes->post('update/(:num)', 'ShopClosingDayController::update/$1', ['as' => 'admin.shop-closing-days.update']);
        $routes->post('delete/(:num)', 'ShopClosingDayController::delete/$1', ['as' => 'admin.shop-closing-days.delete']);
        $routes->get('batch', 'ShopClosingDayController::batch', ['as' => 'admin.shop-closing-days.batch']);
        $routes->post('batch-create', 'ShopClosingDayController::batchCreate', ['as' => 'admin.shop-closing-days.batch-create']);
    });

    // リマインドメール管理（staff権限）
    $routes->group('reminders', ['filter' => 'permission:staff.access'], static function ($routes) {
        $routes->get('/', 'ReminderController::index', ['as' => 'admin.reminders.index']);
    });

    // ユーザー管理（admin権限のみ）
    $routes->group('users', ['filter' => 'group:admin'], static function ($routes) {
        $routes->get('/', 'UserController::index', ['as' => 'admin.users.index']);
        $routes->get('new', 'UserController::new', ['as' => 'admin.users.new']);
        $routes->post('create', 'UserController::create', ['as' => 'admin.users.create']);
        $routes->get('edit/(:num)', 'UserController::edit/$1', ['as' => 'admin.users.edit']);
        $routes->post('update/(:num)', 'UserController::update/$1', ['as' => 'admin.users.update']);
        $routes->post('delete/(:num)', 'UserController::delete/$1', ['as' => 'admin.users.delete']);
    });

    // 車両種別マスタ管理（admin権限のみ）
    $routes->group('vehicle-types', ['filter' => 'group:admin'], static function ($routes) {
        $routes->get('/', 'VehicleTypeController::index', ['as' => 'admin.vehicle-types.index']);
        $routes->get('new', 'VehicleTypeController::new', ['as' => 'admin.vehicle-types.new']);
        $routes->post('create', 'VehicleTypeController::create', ['as' => 'admin.vehicle-types.create']);
        $routes->get('edit/(:num)', 'VehicleTypeController::edit/$1', ['as' => 'admin.vehicle-types.edit']);
        $routes->post('update/(:num)', 'VehicleTypeController::update/$1', ['as' => 'admin.vehicle-types.update']);
        $routes->post('delete/(:num)', 'VehicleTypeController::delete/$1', ['as' => 'admin.vehicle-types.delete']);
    });
});


// 顧客向けページ（認証不要）
$routes->group('customer', ['namespace' => 'App\Controllers\Customer'], static function ($routes) {
    
    // カレンダー機能
    $routes->group('calendar', static function ($routes) {
        $routes->get('month', 'CalendarController::month', ['as' => 'customer.calendar.month']);
        $routes->get('week', 'CalendarController::week', ['as' => 'customer.calendar.week']);
    });
    
    // 予約機能
    $routes->group('reservation', static function ($routes) {
        $routes->get('form', 'ReservationController::form', ['as' => 'customer.reservation.form']);
        $routes->post('submit', 'ReservationController::submit', ['as' => 'customer.reservation.submit']);
        $routes->get('status/(:segment)', 'ReservationController::status/$1', ['as' => 'customer.reservation.status']);
        $routes->post('cancel', 'ReservationController::cancel', ['as' => 'customer.reservation.cancel']);
    });
});


// // 利用者向けページ
// $routes->group('user', ['namespace' => 'App\Controllers\User'], static function ($routes) {
//     $routes->get('calendar', 'CalendarController::index', ['as' => 'user.calendar']);
// });

// 開発テスト用ページ（開発環境のみ）
if (ENVIRONMENT === 'development') {
    $routes->group('dev-test', ['namespace' => 'App\Controllers\Dev'], static function ($routes) {
        $routes->get('/', 'TestController::index');
        $routes->get('auth', 'TestController::testAuthHelper');
        $routes->get('pdf-sample', 'TestController::testPdfService');
        $routes->get('flatpickr-form', 'TestController::testFlatpickrForm');
        $routes->get('lab', 'TestController::lab');
    });
}