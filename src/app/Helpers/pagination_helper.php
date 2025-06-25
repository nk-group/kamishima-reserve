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
    function buildSortUrl(string $column): string
    {
        $request = service('request');
        $params = $request->getGet();
        
        // 現在のソート状態を確認
        $currentSort = $params['sort'] ?? '';
        $currentDirection = $params['direction'] ?? 'ASC';
        
        // 同じカラムの場合は方向を切り替え、違うカラムの場合はASCで開始
        if ($currentSort === $column) {
            $params['direction'] = $currentDirection === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $params['sort'] = $column;
            $params['direction'] = 'ASC';
        }
        
        // ページ番号をリセット（ソート時は1ページ目に戻る）
        $params['page'] = 1;
        
        return route_to('admin.reservations.index') . '?' . http_build_query($params);
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
        $currentSort = $request->getGet('sort');
        $currentDirection = $request->getGet('direction');
        
        if ($currentSort === $column) {
            return $currentDirection === 'ASC' 
                ? '<i class="bi bi-arrow-up"></i>' 
                : '<i class="bi bi-arrow-down"></i>';
        }
        
        return '<i class="bi bi-arrow-up-down text-muted"></i>';
    }
}

if (!function_exists('buildPageUrl')) {
    /**
     * ページネーション用URLを生成します。
     * 
     * @param int $page ページ番号
     * @return string ページネーション用URL
     */
    function buildPageUrl(int $page): string
    {
        $request = service('request');
        $params = $request->getGet();
        $params['page'] = $page;
        
        return route_to('admin.reservations.index') . '?' . http_build_query($params);
    }
}

if (!function_exists('getCurrentSearchParams')) {
    /**
     * 現在の検索パラメータを取得します。
     * 
     * @return array 検索パラメータ配列
     */
    function getCurrentSearchParams(): array
    {
        $request = service('request');
        return $request->getGet();
    }
}

if (!function_exists('buildSearchUrl')) {
    /**
     * 検索用URLを生成します。
     * 
     * @param array $additionalParams 追加パラメータ
     * @return string 検索用URL
     */
    function buildSearchUrl(array $additionalParams = []): string
    {
        $request = service('request');
        $params = array_merge($request->getGet(), $additionalParams);
        
        // 空の値を除去
        $params = array_filter($params, function($value) {
            return $value !== '' && $value !== null;
        });
        
        return route_to('admin.reservations.index') . '?' . http_build_query($params);
    }
}