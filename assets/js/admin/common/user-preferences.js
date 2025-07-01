// assets/js/admin/common/user-preferences.js
import * as bootstrap from 'bootstrap';

/**
 * 個人設定機能の初期化
 */
export function initUserPreferences() {
    const userSettingsBtn = document.getElementById('userSettingsBtn');
    const modal = document.getElementById('userPreferencesModal');
    const saveBtn = document.getElementById('saveUserPreferencesBtn');
    const form = document.getElementById('userPreferencesForm');

    if (!userSettingsBtn || !modal) {
        return; // 必要な要素がない場合は処理を終了
    }

    // 歯車アイコンクリック時の処理
    userSettingsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openUserPreferencesModal();
    });

    // 保存ボタンクリック時の処理
    if (saveBtn) {
        saveBtn.addEventListener('click', (e) => {
            e.preventDefault();
            saveUserPreferences();
        });
    }

    // モーダル表示時の処理
    modal.addEventListener('show.bs.modal', () => {
        loadUserPreferences();
    });
}

/**
 * 個人設定モーダルを開く
 */
function openUserPreferencesModal() {
    const modal = new bootstrap.Modal(document.getElementById('userPreferencesModal'));
    modal.show();
}

/**
 * 現在の個人設定を読み込む
 */
async function loadUserPreferences() {
    try {
        showUserPreferencesLoading(true);

        const response = await fetch('/admin/user-preferences', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (data.success) {
            populatePreferencesForm(data.preferences, data.shops);
        } else {
            throw new Error(data.message || '設定の読み込みに失敗しました。');
        }
    } catch (error) {
        console.error('Failed to load user preferences:', error);
        showUserPreferencesMessage('設定の読み込みに失敗しました。', 'danger');
    } finally {
        showUserPreferencesLoading(false);
    }
}

/**
 * フォームに設定値を反映
 */
function populatePreferencesForm(preferences, shops) {
    // デフォルト店舗の選択肢を設定
    const shopSelect = document.getElementById('defaultShopId');
    if (shopSelect && shops) {
        shopSelect.innerHTML = '<option value="">-- 店舗を選択してください --</option>';
        for (const [id, name] of Object.entries(shops)) {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            if (preferences.default_shop_id && preferences.default_shop_id == id) {
                option.selected = true;
            }
            shopSelect.appendChild(option);
        }
    }

    // ページネーション件数を設定
    const paginationSelect = document.getElementById('paginationPerPage');
    if (paginationSelect && preferences.pagination_per_page) {
        paginationSelect.value = preferences.pagination_per_page;
    }
}

/**
 * 個人設定を保存
 */
async function saveUserPreferences() {
    const form = document.getElementById('userPreferencesForm');
    if (!form) return;

    try {
        showUserPreferencesLoading(true);

        const formData = new FormData(form);
        
        const response = await fetch('/admin/user-preferences/save', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (data.success) {
            showUserPreferencesMessage('設定を保存しました。', 'success');
            
            // 2秒後にモーダルを閉じる
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('userPreferencesModal'));
                if (modal) {
                    modal.hide();
                }
            }, 2000);
        } else {
            throw new Error(data.message || '設定の保存に失敗しました。');
        }
    } catch (error) {
        console.error('Failed to save user preferences:', error);
        showUserPreferencesMessage('設定の保存に失敗しました。', 'danger');
    } finally {
        showUserPreferencesLoading(false);
    }
}

/**
 * ローディング状態の表示制御
 */
function showUserPreferencesLoading(isLoading) {
    const saveBtn = document.getElementById('saveUserPreferencesBtn');
    const form = document.getElementById('userPreferencesForm');

    if (saveBtn) {
        if (isLoading) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>保存中...';
        } else {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>保存';
        }
    }

    if (form) {
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.disabled = isLoading;
        });
    }
}

/**
 * メッセージの表示
 */
function showUserPreferencesMessage(message, type = 'info') {
    const messageContainer = document.getElementById('userPreferencesMessage');
    const messageText = document.getElementById('userPreferencesMessageText');

    if (!messageContainer || !messageText) return;

    // メッセージの設定
    messageText.textContent = message;

    // アラートクラスの設定
    messageContainer.className = `alert alert-${type} alert-dismissible fade show`;
    
    // 表示
    messageContainer.style.display = 'block';

    // 成功メッセージは自動で非表示
    if (type === 'success') {
        setTimeout(() => {
            hideUserPreferencesMessage();
        }, 5000);
    }
}

/**
 * メッセージを非表示
 */
function hideUserPreferencesMessage() {
    const messageContainer = document.getElementById('userPreferencesMessage');
    if (messageContainer) {
        messageContainer.style.display = 'none';
        messageContainer.className = 'alert alert-dismissible fade';
    }
}

// グローバルスコープに関数を追加（HTMLから呼び出し可能にする）
window.hideUserPreferencesMessage = hideUserPreferencesMessage;