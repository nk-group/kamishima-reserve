<?= $this->extend('Layouts/admin-layout') ?>

<?php // ページタイトルはコントローラーから $page_title 変数として渡されます。
      // このビューファイル内での title セクションは不要です。
?>

<?= $this->section('content') ?>  <?php // ★★★ セクション名を 'content' に変更 ★★★ ?>

    <h2>Flatpickr Helper (app_form_helper.php) テスト</h2>

    <?= form_open(site_url('dev-test/flatpickr-form')) ?>

    <div class="mb-3">
        <h3>日付選択 (flatpickr-date)</h3>
        <p><code>flatpickr_input('date_field', old('date_field', ''), ['placeholder' => '日付を選択', 'class' => 'form-control'], 'date')</code></p>
        <?= flatpickr_input('date_field', old('date_field', ''), ['placeholder' => '日付を選択', 'class' => 'form-control'], 'date') ?>
    </div>

    <div class="mb-3">
        <h3>日時選択 (flatpickr-datetime)</h3>
        <p><code>flatpickr_input('datetime_field', old('datetime_field', ''), ['placeholder' => '日時を選択', 'class' => 'form-control'], 'datetime')</code></p>
        <?= flatpickr_input('datetime_field', old('datetime_field', ''), ['placeholder' => '日時を選択', 'class' => 'form-control'], 'datetime') ?>
    </div>

    <div class="mb-3">
        <h3>時刻選択 (flatpickr-time)</h3>
        <p><code>flatpickr_input('time_field', old('time_field', ''), ['placeholder' => '時刻を選択', 'class' => 'form-control'], 'time')</code></p>
        <?= flatpickr_input('time_field', old('time_field', ''), ['placeholder' => '時刻を選択', 'class' => 'form-control'], 'time') ?>
    </div>

    <div class="mb-3">
        <h3>動的オプション付き日付選択 (flatpickr-date-dynamic)</h3>
        <p><code>$disabledDates = ['<?= date('Y-m-d', strtotime('+1 day')) ?>', '<?= date('Y-m-d', strtotime('+5 day')) ?>'];</code></p>
        <p><code>$attributes = ['placeholder' => '特定の日付が無効', 'data-disabled-dates' => $disabledDates, 'data-min-date' => 'today', 'class' => 'form-control'];</code></p>
        <p><code>flatpickr_input('dynamic_date_field', '', $attributes, 'dynamic')</code></p>
        <?php
        $disabledDates = [date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+5 day'))];
        $attributes = [
            'placeholder' => '特定の日付が無効',
            'data-disabled-dates' => $disabledDates,
            'data-min-date' => 'today',
            'class' => 'form-control',
        ];
        ?>
        <?= flatpickr_input('dynamic_date_field', old('dynamic_date_field', ''), $attributes, 'dynamic') ?>
    </div>

    <?= form_close() ?>

    <hr>
    <p><strong>注意:</strong> Flatpickrの動作は、JavaScript (`flatpickr-custom.js`) が正しく読み込まれ、実行されているブラウザでご確認ください。</p>

    <hr class="my-4">
    <p><a href='<?= site_url('dev-test') ?>' class="btn btn-secondary btn-sm">テストコントローラー トップに戻る</a></p>

<?= $this->endSection() ?>