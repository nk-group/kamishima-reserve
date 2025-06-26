/**
 * 定休日マスタ一覧ページ JavaScript
 * ファイル名: assets/js/admin/pages/shop-closing-days/index.js
 */

import { initCommonFeatures } from './common.js';

/**
 * 定休日マスタ一覧ページの初期化
 */
export function initShopClosingDaysIndex() {
    console.log('Shop Closing Days Index page initialized.');
    
    // 削除確認モーダル
    window.confirmDelete = function(id, name) {
        const targetElement = document.getElementById('deleteTargetName');
        const formElement = document.getElementById('deleteForm');
        
        if (targetElement && formElement) {
            targetElement.textContent = name;
            formElement.action = `/admin/shop-closing-days/delete/${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    };
    
    // 共通機能の初期化
    initCommonFeatures();
}