<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Config\Paths;

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
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'orientation' => 'P',
            'tempDir' => WRITEPATH . 'cache/mpdf',
        ];

        // 一時ディレクトリが存在しない場合は作成
        if (!is_dir($this->defaultConfig['tempDir'])) {
            mkdir($this->defaultConfig['tempDir'], 0775, true);
        }
        
        $this->mpdf = new Mpdf($this->defaultConfig);
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
     * @param string $filename 出力するPDFのファイル名
     * @param string $outputMode 出力モード: 'I'(インライン), 'D'(ダウンロード), 'F'(ファイル保存), 'S'(文字列)
     * @param array $configOverride このPDF生成時のみ適用するmPDF設定
     * @return string|void
     * @throws MpdfException
     */
    public function generateFromHtml(string $htmlContent, string $filename = 'document.pdf', string $outputMode = 'I', array $configOverride = [])
    {
        $mpdfInstance = $this->mpdf;

        if (!empty($configOverride)) {
            $currentConfig = array_merge($this->defaultConfig, $configOverride);
            $mpdfInstance = new Mpdf($currentConfig);
            $mpdfInstance->autoScriptToLang = true;
            $mpdfInstance->autoLangToFont = true;
        }

        $mpdfInstance->WriteHTML($htmlContent);
        return $mpdfInstance->Output($filename, $outputMode);
    }

    /**
     * CodeIgniterのビューファイルからHTMLをレンダリングし、PDFを生成します。
     *
     * @param string $viewPath ビューファイルのパス
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

    /**
     * クリア入庫予定表PDFを生成します。
     * @param array $reservations 予約データの配列
     * @param string $date 対象日 (Y-m-d形式)
     * @param string $outputMode 出力モード
     * @return string|void
     * @throws MpdfException
     */
    public function generateArrivalSchedulePdf(array $reservations, string $date, string $outputMode = 'I'): mixed
    {
        $viewData = [
            'reservations' => $reservations,
            'target_date' => $date,
            'formatted_date' => $this->formatJapaneseDate($date)
        ];
        
        $filename = 'arrival_schedule_' . str_replace('-', '', $date) . '.pdf';
        
        // A4縦向き設定（シンプル）
        $configOverride = [
            'mode' => 'utf-8',
            'orientation' => 'P',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 5
        ];
        
        return $this->generateFromView('PdfTemplates/arrival_schedule', $viewData, $filename, $outputMode, $configOverride);
    }

    /**
     * 予約タグPDFを生成します。
     * @param array $reservations 予約データの配列
     * @param string $date 対象日 (Y-m-d形式)
     * @param string $outputMode 出力モード
     * @return string|void
     * @throws MpdfException
     */
    public function generateWorkInstructionCardPdf(array $reservations, string $date, string $outputMode = 'I'): mixed
    {
        $viewData = [
            'reservations' => $reservations,
            'target_date' => $date,
            'formatted_date' => $this->formatJapaneseDate($date)
        ];
        
        $filename = 'work_instruction_card_' . str_replace('-', '', $date) . '.pdf';
        
        // 作業指示カード用の設定（小さめの用紙サイズ）
        $configOverride = [
            'mode' => 'utf-8',
            'format' => [100, 60], // 100mm x 60mm
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5
        ];
        
        return $this->generateFromView('PdfTemplates/work_instruction_card', $viewData, $filename, $outputMode, $configOverride);
    }

    /**
     * 日付を日本語形式にフォーマットします。
     * @param string $date Y-m-d形式の日付
     * @return string 日本語形式の日付
     */
    private function formatJapaneseDate(string $date): string
    {
        $timestamp = strtotime($date);
        $dayOfWeek = ['日', '月', '火', '水', '木', '金', '土'][date('w', $timestamp)];
        
        return date('Y年n月j日', $timestamp) . '(' . $dayOfWeek . ')';
    }
}