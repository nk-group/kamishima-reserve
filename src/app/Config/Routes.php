<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index'); // CodeIgniterのデフォルトWelcomeページはそのまま残す場合


// Shieldが提供する認証ルート (ログイン、ログアウト、登録など)
// これを呼び出すことで、ShieldのAuthControllerが処理を担当します。
// Auth.phpの$viewsで指定したビューが使われます。
service('auth')->routes($routes);


// 管理者/スタッフ向けルート
// 'sessionauth' フィルタで認証を要求
// 'group:admin,staff' フィルタで admin または staff グループに所属しているかチェック
$routes->group('admin', ['filter' => 'sessionauth', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    /** @var RouteCollection $routes */
    $routes->get('', 'DashboardController::index', ['as' => 'admin.home']);
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);


    // --- ユーザー管理ルート ---
    // 'group:admin' フィルターを追加して、adminグループのユーザーのみアクセスを許可
    $routes->group('users', ['filter' => 'group:admin'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'UserController::index', ['as' => 'admin.users.index']); // ユーザー一覧表示 (GET /admin/users)
        $routes->get('new', 'UserController::new', ['as' => 'admin.users.new']);         // 新規作成フォーム表示 (GET /admin/users/new) (後で追加)
        $routes->post('create', 'UserController::create', ['as' => 'admin.users.create']); // 新規作成処理 (POST /admin/users/create) (後で追加)
        $routes->get('edit/(:num)', 'UserController::edit/$1', ['as' => 'admin.users.edit']); // 編集フォーム表示 (GET /admin/users/edit/1) (後で追加)
        $routes->post('update/(:num)', 'UserController::update/$1', ['as' => 'admin.users.update']); // 更新処理 (POST /admin/users/update/1) (後で追加)
        $routes->get('delete/(:num)', 'UserController::delete/$1', ['as' => 'admin.users.delete']); // 削除処理 (GET /admin/users/delete/1) (後で追加、POST推奨)
    });


    // --- 車両種別マスタ管理ルート ---
    // 'group:admin' フィルターを追加して、adminグループのユーザーのみアクセスを許可
    $routes->group('vehicle-types', ['filter' => 'group:admin'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'VehicleTypeController::index', ['as' => 'admin.vehicle-types.index']);    // 一覧表示
        $routes->get('new', 'VehicleTypeController::new', ['as' => 'admin.vehicle-types.new']);     // 新規作成フォーム
        $routes->post('create', 'VehicleTypeController::create', ['as' => 'admin.vehicle-types.create']); // 新規作成処理
        $routes->get('edit/(:num)', 'VehicleTypeController::edit/$1', ['as' => 'admin.vehicle-types.edit']); // 編集フォーム
        $routes->post('update/(:num)', 'VehicleTypeController::update/$1', ['as' => 'admin.vehicle-types.update']); // 更新処理
        $routes->post('delete/(:num)', 'VehicleTypeController::delete/$1', ['as' => 'admin.vehicle-types.delete']); // 削除処理
    });

    // --- 予約管理ルート ---
    // URL: /admin/reservations/...
    // Filter: 'staff.access' パーミッションを持つユーザーがアクセス可能
    // Controller: App\Controllers\Admin\ReservationController
    $routes->group('reservations', ['filter' => 'permission:staff.access'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'ReservationController::index', ['as' => 'admin.reservations.index']); // 予約一覧
        $routes->get('export-csv', 'ReservationController::exportCsv', ['as' => 'admin.reservations.export-csv']); // CSVエクスポート
        $routes->get('new', 'ReservationController::new', ['as' => 'admin.reservations.new']); // 新規作成フォーム
        $routes->post('create', 'ReservationController::create', ['as' => 'admin.reservations.create']); // 新規予約作成処理
        $routes->get('(:num)', 'ReservationController::edit/$1', ['as' => 'admin.reservations.edit']); // 予約詳細/編集フォーム
        $routes->post('update/(:num)', 'ReservationController::update/$1', ['as' => 'admin.reservations.update']); // 予約更新処理
        $routes->post('delete/(:num)', 'ReservationController::delete/$1', ['as' => 'admin.reservations.delete']); // 予約削除処理
    });

    // 帳票系機能
    $routes->group('reports', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
        $routes->get('arrival-schedule', 'ReportsController::arrivalSchedule', ['as' => 'admin.reports.arrival-schedule']);
        $routes->get('work-instruction-card', 'ReportsController::workInstructionCard', ['as' => 'admin.reports.work-instruction-card']);
    });

    $routes->group('shop-closing-days', ['filter' => 'permission:staff.access'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'ShopClosingDayController::index', ['as' => 'admin.shop-closing-days.index']); // 一覧表示
        $routes->get('new', 'ShopClosingDayController::new', ['as' => 'admin.shop-closing-days.new']); // 新規作成フォーム
        $routes->post('create', 'ShopClosingDayController::create', ['as' => 'admin.shop-closing-days.create']); // 新規作成処理
        $routes->get('edit/(:num)', 'ShopClosingDayController::edit/$1', ['as' => 'admin.shop-closing-days.edit']); // 編集フォーム
        $routes->post('update/(:num)', 'ShopClosingDayController::update/$1', ['as' => 'admin.shop-closing-days.update']); // 更新処理
        $routes->post('delete/(:num)', 'ShopClosingDayController::delete/$1', ['as' => 'admin.shop-closing-days.delete']); // 削除処理
        $routes->get('batch', 'ShopClosingDayController::batch', ['as' => 'admin.shop-closing-days.batch']); // 一括作成フォーム
        $routes->post('batch-create', 'ShopClosingDayController::batchCreate', ['as' => 'admin.shop-closing-days.batch-create']); // 一括作成処理
    });    
    

    // --- リマインドメール管理ルート ---
    $routes->group('reminders', ['filter' => 'permission:staff.access'], static function ($routes) {
        /** @var RouteCollection $routes */
        $routes->get('', 'ReminderController::index', ['as' => 'admin.reminders.index']); // 送信予定一覧
    });    
});



// --- 利用者向けページ ---
$routes->get('/calendar', 'User\CalendarController::index', ['as' => 'user.calendar']);

// 他の利用者向けルートもここに追加していく
// $routes->get('/', 'User\HomeController::index', ['as' => 'user.home']); // もしトップページがあれば



// --- 開発テスト用のページ ---
if (ENVIRONMENT === 'development') {
    $routes->group('dev-test', ['namespace' => 'App\Controllers\Dev'], static function ($routes) {
        $routes->get('/', 'TestController::index');
        $routes->get('auth', 'TestController::testAuthHelper');
        $routes->get('pdf-sample', 'TestController::testPdfService');
        $routes->get('flatpickr-form', 'TestController::testFlatpickrForm');
        $routes->get('lab', 'TestController::lab');
        // $routes->post('lab', 'TestController::lab'); // もしフォームで何か送信してテストする場合はPOSTも許可
    });
}