<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTOLOADER CONFIGURATION
 * -------------------------------------------------------------------
 *
 * This file defines the namespaces and class maps so the Autoloader
 * can find the files as needed.
 *
 * NOTE: If you use an identical key in $psr4 or $classmap, then
 *       the values in this file will overwrite the framework's values.
 *
 * NOTE: This class is required prior to Autoloader instantiation,
 *       and does not extend BaseConfig.
 */
class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
     * This maps the locations of any namespaces in your application to
     * their location on the file system. These are used by the autoloader
     * to locate files the first time they have been instantiated.
     *
     * The 'Config' (APPPATH . 'Config') and 'CodeIgniter' (SYSTEMPATH) are
     * already mapped for you.
     *
     * You may change the name of the 'App' namespace if you wish,
     * but this should be done prior to creating any namespaced classes,
     * else you will need to modify all of those classes for this to work.
     *
     * @var array<string, list<string>|string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
     * The class map provides a map of class names and their exact
     * location on the drive. Classes loaded in this manner will have
     * slightly faster performance because they will not have to be
     * searched for within one or more directories as they would if they
     * were being autoloaded through a namespace.
     *
     * Prototype:
     *   $classmap = [
     *       'MyClass'   => '/path/to/class/file.php'
     *   ];
     *
     * @var array<string, string>
     */
    public $classmap = [];

    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     * The files array provides a list of paths to __non-class__ files
     * that will be autoloaded. This can be useful for bootstrap operations
     * or for loading functions.
     *
     * Prototype:
     *   $files = [
     *       '/path/to/my/file.php',
     *   ];
     *
     * @var list<string>
     */
    public $files = [];

    /**
     * -------------------------------------------------------------------
     * Helpers
     * -------------------------------------------------------------------
     * アプリケーション起動時に自動的にロードされるヘルパーファイルの配列。
     *
     * @var list<string>
     */
    public $helpers = [
        'auth',             // Shield認証ライブラリ用 (必須)
        'setting',          // Shield設定値取得用 (必須)
        'url',              // URL生成関数 (例: site_url(), base_url()) のため (強く推奨)
        'html',             // HTML要素生成関数 (例: link_tag(), script_tag()) のため (vite_helperで必須)
        'vite',             // カスタムViteヘルパー (vite_helper.php) のため (必須)
        'form',             // フォーム生成関数 (例: csrf_field(), form_open()) のため (強く推奨)
        'auth_utility',     // Shieldの認証機能を補完するカスタムヘルパー
        'app_form',         // このアプリのフォームに関するカスタムヘルパー
        'shop_closing_day', // 定休日マスタ用ヘルパー（新規追加）
        // 'filesystem',       // ファイルシステム操作が必要になった場合に追加
        // 'text',             // 高度なテキスト処理が必要になった場合に追加
    ];    

}
