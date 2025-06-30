<?php

/**
 * Pagination Helper
 * ページネーションとソート機能のヘルパー関数
 */

if (!function_exists('buildSortUrl')) {
    /**
     * ソート用URLを生成します。
     * 
     * @param string $column ソート対象カラム
     * @return string ソート用URL
     */
    function buildSortUrl(string $column, string $routeName): string
    {
        $request = service('request');
        $params = $request->getGet();
        
        // 現在のソート状態を確認
        $currentSort = $params['sort'] ?? 'desired_date'; // デフォルトソートキーを予約日に
        $currentDirection = $params['direction'] ?? 'DESC';
        
        // 同じカラムの場合は方向を切り替え、違うカラムの場合はASCで開始
        $params['direction'] = ($currentSort === $column && $currentDirection === 'asc') ? 'desc' : 'asc';
        $params['sort'] = $column;
        
        // ページ番号をリセット（ソート時は1ページ目に戻る）
        unset($params['page']);
        
        return route_to($routeName) . '?' . http_build_query($params);
    }
}

if (!function_exists('renderSortIcon')) {
    /**
     * ソートアイコンを表示します。
     * 
     * @param string $column ソート対象カラム
     * @return string HTMLアイコン文字列
     */
    function renderSortIcon(string $column): string
    {
        $request = service('request');
        $sort = $request->getGet('sort') ?? 'desired_date';
        $direction = $request->getGet('direction') ?? 'desc';
        
        if ($sort !== $column) {
            return '<i class="bi bi-arrow-down-up sort-icon-default"></i>';
        }
        
        return ($direction === 'asc')
            ? '<i class="bi bi-sort-up-alt sort-icon-active"></i>'
            : '<i class="bi bi-sort-down-alt sort-icon-active"></i>';
    }
}