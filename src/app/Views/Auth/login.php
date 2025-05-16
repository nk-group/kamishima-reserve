<?= $this->extend(config('Auth')->views['layout']) // Shieldのデフォルトレイアウトを継承 (通常はBootstrapベース) ?>

<?= $this->section('title') ?>
    <?= lang('Auth.login') // 'ログイン'などの言語ファイルの設定に依存 ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="container d-flex justify-content-center p-5">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-5 text-center"><?= lang('Auth.login') ?> (管理画面)</h5>

            <?php if (session('error') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= session('error') ?>
                </div>
            <?php elseif (session('errors') !== null) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php if (is_array(session('errors'))) : ?>
                        <?php foreach (session('errors') as $error) : ?>
                            <?= esc($error) ?>
                            <br>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?= esc(session('errors')) ?>
                    <?php endif ?>
                </div>
            <?php endif ?>

            <?php if (session('message') !== null) : ?>
                <div class="alert alert-success" role="alert">
                    <?= esc(session('message')) ?>
                </div>
            <?php endif ?>

            <form action="<?= url_to('login') // Shieldが提供するログイン処理ルート ?>" method="post">
                <?= csrf_field() // CSRF対策トークン ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingEmailInput" name="email"
                           inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>"
                           value="<?= old('email') ?>" required>
                    <label for="floatingEmailInput"><?= lang('Auth.email') ?></label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPasswordInput" name="password"
                           inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>"
                           required>
                    <label for="floatingPasswordInput"><?= lang('Auth.password') ?></label>
                </div>

                <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberMeCheckbox"
                               <?php if (old('remember')): ?> checked<?php endif ?>>
                        <label class="form-check-label" for="rememberMeCheckbox">
                            <?= lang('Auth.rememberMe') ?>
                        </label>
                    </div>
                <?php endif; ?>

                <div class="d-grid col-12 col-md-8 mx-auto m-3">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.login') ?></button>
                </div>

                <?php
                    // 新規登録やマジックリンクは Config/Auth.php で無効化しているため、
                    // 通常は以下のリンクは表示されません。
                    // (setting('Auth.allowRegistration') や setting('Auth.allowMagicLinkLogins') が true の場合のみ表示)
                ?>
                <?php if (setting('Auth.allowRegistration')) : ?>
                    <p class="text-center mt-3">
                        <?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a>
                    </p>
                <?php endif ?>

                <?php if (setting('Auth.allowMagicLinkLogins')): ?>
                    <p class="text-center">
                         <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.forgotPassword') ?> <?= lang('Auth.useMagicLink') ?></a>
                    </p>
                <?php endif ?>

            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>