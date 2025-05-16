<?= $this->extend('Layouts/admin-layout') ?> <?php // 共通レイアウトを継承 ?>

<?= $this->section('content') ?> <?php // レイアウトの 'content' セクションに表示 ?>

    <h2>Auth Utility Helper テスト</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">現在の認証状態</h5>
            <p><strong>ログイン状態:</strong> <?= $isLoggedIn ? '<span class="badge bg-success">ログイン中</span>' : '<span class="badge bg-danger">未ログイン</span>' ?></p>
        </div>
    </div>

    <?php if ($user): ?>
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">ユーザー情報 (<code>current_user_entity()</code>)</h5>
            <p><strong>ユーザーID:</strong> <?= esc($user->id) ?></p>
            <p><strong>ユーザー名 (username):</strong> <?= esc($user->username) ?></p>
            <p><strong>メールアドレス:</strong> <?= esc($user->email) ?></p>
            
            <h6 class="mt-3">カスタムヘルパー関数テスト:</h6>
            <p><strong><code>is_admin()</code>:</strong> <?= $isAdminHelper ? '<span class="badge bg-primary">はい (true)</span>' : '<span class="badge bg-secondary">いいえ (false)</span>' ?></p>
            <p><strong><code>is_staff()</code>:</strong> <?= $isStaffHelper ? '<span class="badge bg-info">はい (true)</span>' : '<span class="badge bg-secondary">いいえ (false)</span>' ?></p>
            <p><strong><code>user_can('admin.access')</code>:</strong> <?= $canAdminAccessHelper ? '<span class="badge bg-warning text-dark">はい (true)</span>' : '<span class="badge bg-secondary">いいえ (false)</span>' ?></p>
            
            <h6 class="mt-3">所属グループ (<code>auth()->user()->getGroups()</code>):</h6>
            <?php if (!empty($userGroups)): ?>
                <ul>
                    <?php foreach ($userGroups as $group): ?>
                        <li><?= esc($group) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>所属しているグループはありません。</p>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info mt-3" role="alert">
        ユーザー情報は取得できませんでした（未ログインの状態です）。ログインしてお試しください。
    </div>
    <?php endif; ?>

    <hr class="my-4">
    <p><a href='<?= site_url('dev-test') ?>' class="btn btn-secondary btn-sm">テストコントローラー トップに戻る</a></p>

<?= $this->endSection() ?>