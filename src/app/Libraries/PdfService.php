<?php

namespace App\Libraries; // または App\Services

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Config\Paths; // vendorディレクトリのパス取得用

class PdfService
{
    protected Mpdf $mpdf;
    protected array $defaultConfig = [];

    /**
     * PdfService constructor.
     * @throws MpdfException
     */
    public function __construct()
    {
        $this->defaultConfig = [
            'mode' => 'ja', // ★★★ 日本語モードを指定 ★★★
            // 'default_font_size' => 0, // デフォルトはauto
            // 'default_font' => '', // デフォルトはauto
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'orientation' => 'P', // Portrait
            'format' => 'A4',
            'tempDir' => WRITEPATH . 'cache/mpdf', // 書き込み可能な一時ディレクトリ
        ];

        // 一時ディレクトリが存在しない場合は作成
        if (!is_dir($this->defaultConfig['tempDir'])) {
            mkdir($this->defaultConfig['tempDir'], 0775, true);
        }
        
        $this->mpdf = new Mpdf($this->defaultConfig);

        // languageToFont() は削除またはコメントアウトします
        // $this->mpdf->languageToFont(); // ← この行を削除またはコメントアウト

        // これらの設定は引き続き有効です
        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true; 
    }
    
    
    /**
     * mPDFインスタンスを直接取得します（高度なカスタマイズが必要な場合）。
     * @return Mpdf
     */
    public function getInstance(): Mpdf
    {
        return $this->mpdf;
    }

    /**
     * HTMLコンテンツからPDFを生成し、指定された方法で出力します。
     *
     * @param string $htmlContent 生成するPDFのHTMLコンテンツ
     * @param string $filename 出力するPDFのファイル名 (例: 'document.pdf')
     * @param string $outputMode 出力モード:
     * 'I': ブラウザにインライン表示 (デフォルト)
     * 'D': ブラウザでダウンロードダイアログ表示
     * 'F': ローカルファイルに保存 (この場合、$filename はフルパス)
     * 'S': 文字列としてPDFコンテンツを返す
     * @param array $configOverride このPDF生成時のみ適用するmPDF設定 (オプション)
     * @return string|void 出力モード 'S' の場合はPDFコンテンツ文字列、それ以外はvoid
     * @throws MpdfException
     */
    public function generateFromHtml(string $htmlContent, string $filename = 'document.pdf', string $outputMode = 'I', array $configOverride = [])
    {
        $mpdfInstance = $this->mpdf;

        if (!empty($configOverride)) {
            // 一時的な設定上書きが必要な場合、新しいインスタンスを作るか、設定を変更して元に戻す
            // ここでは簡易的に新しいインスタンスで対応 (設定項目が多い場合)
            // または、既存インスタンスの設定を一部変更する $this->mpdf->WriteHTML($htmlContent, \Mpdf\HTMLParserMode::DEFAULT_MODE, true, false); なども可
            $currentConfig = array_merge($this->defaultConfig, $configOverride);
            $mpdfInstance = new Mpdf($currentConfig);
            $mpdfInstance->languageToFont();
            $mpdfInstance->autoScriptToLang = true;
            $mpdfInstance->autoLangToFont = true;
        }
        
        // ヘッダーやフッターの共通設定例
        // $mpdfInstance->SetHeader('{PAGENO}/{nbpg}'); // 右上にページ番号
        // $mpdfInstance->SetFooter('会社名'); // 中央にフッター

        $mpdfInstance->WriteHTML($htmlContent);
        return $mpdfInstance->Output($filename, $outputMode);
    }

    /**
     * CodeIgniterのビューファイルからHTMLをレンダリングし、PDFを生成します。
     *
     * @param string $viewPath ビューファイルのパス (例: 'pdf_templates/invoice')
     * @param array $viewData ビューに渡すデータ
     * @param string $filename 出力ファイル名
     * @param string $outputMode 出力モード
     * @param array $configOverride mPDF設定上書き
     * @return string|void
     * @throws MpdfException
     */
    public function generateFromView(string $viewPath, array $viewData = [], string $filename = 'document.pdf', string $outputMode = 'I', array $configOverride = []): mixed
    {
        $html = view($viewPath, $viewData);
        return $this->generateFromHtml($html, $filename, $outputMode, $configOverride);
    }

    // --- アプリケーション固有のPDF生成メソッドの例 ---

    /**
     * 予約タグPDFを生成します。
     * @param \App\Entities\ReservationEntity $reservation 予約エンティティ
     * @return string|void
     * @throws MpdfException
     */
    public function generateReservationTagPdf(\App\Entities\ReservationEntity $reservation, string $outputMode = 'I'): mixed
    {
        $viewData = ['reservation' => $reservation];
        $filename = 'reservation_tag_' . $reservation->reservation_no . '.pdf';
        // 予約タグ専用のmPDF設定があれば $configOverride で渡す (例: 用紙サイズを小さくするなど)
        $configOverride = ['format' => [80, 50]]; // 例: 80mm x 50mm の用紙サイズ
        return $this->generateFromView('pdf_templates/reservation_tag', $viewData, $filename, $outputMode, $configOverride);
    }

    /**
     * 入庫予定表PDFを生成します。
     * @param array $reservations 予約エンティティの配列
     * @param string $date 対象日 (Y-m-d形式)
     * @return string|void
     * @throws MpdfException
     */
    public function generateDailyReportPdf(array $reservations, string $date, string $outputMode = 'I'): mixed
    {
        $viewData = ['reservations' => $reservations, 'report_date' => $date];
        $filename = 'daily_report_' . str_replace('-', '', $date) . '.pdf';
        $configOverride = ['orientation' => 'L']; // 例: 横向き
        return $this->generateFromView('pdf_templates/daily_report', $viewData, $filename, $outputMode, $configOverride);
    }
}