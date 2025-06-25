<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PdfService;
use CodeIgniter\HTTP\ResponseInterface;

class ReportsController extends BaseController
{
    protected PdfService $pdfService;

    public function __construct()
    {
        $this->pdfService = new PdfService();
    }

    /**
     * クリア入庫予定表PDF生成
     */
    public function arrivalSchedule(): ResponseInterface
    {
        try {
            // リクエストから日付を取得（デフォルトは今日）
            $targetDate = $this->request->getGet('date') ?? date('Y-m-d');
            
            // 予約データを取得（サンプルデータを使用）
            $reservations = $this->getSampleReservations($targetDate);
            
            // HTTPヘッダーを設定
            $filename = 'arrival_schedule_' . str_replace('-', '', $targetDate) . '.pdf';
            
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');
            
            // PDFを生成してブラウザに出力
            $pdfOutput = $this->pdfService->generateArrivalSchedulePdf($reservations, $targetDate, 'S');
            
            return $this->response->setBody($pdfOutput);
            
        } catch (\Exception $e) {
            log_message('error', 'PDF生成エラー: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'PDF生成に失敗しました。']);
        }
    }

    /**
     * 作業指示カードPDF生成
     */
    public function workInstructionCard(): ResponseInterface
    {
        try {
            // リクエストから日付を取得（デフォルトは今日）
            $targetDate = $this->request->getGet('date') ?? date('Y-m-d');
            
            // 予約データを取得（サンプルデータを使用）
            $reservations = $this->getSampleReservations($targetDate);
            
            // HTTPヘッダーを設定
            $filename = 'work_instruction_card_' . str_replace('-', '', $targetDate) . '.pdf';
            
            $this->response->setHeader('Content-Type', 'application/pdf');
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
            $this->response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            $this->response->setHeader('Pragma', 'public');
            
            // PDFを生成してブラウザに出力
            $pdfOutput = $this->pdfService->generateWorkInstructionCardPdf($reservations, $targetDate, 'S');
            
            return $this->response->setBody($pdfOutput);
            
        } catch (\Exception $e) {
            log_message('error', 'PDF生成エラー: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'PDF生成に失敗しました。']);
        }
    }

    /**
     * サンプル予約データを生成
     * 実際の実装では ReservationModel から取得します
     */
    private function getSampleReservations(string $date): array
    {
        return [
            // 午前の予約
            [
                'reservation_time' => '8:45',
                'customer_name' => '鈴木　一郎',
                'vehicle_number' => '帯広330あ1234',
                'year_month' => date('Y年n月', strtotime($date)),
                'vehicle_info' => 'スカイライン',
                'remarks' => '初回車検・オイル交換希望',
                'substitute_car' => 'フィット'
            ],
            [
                'reservation_time' => '9:30',
                'customer_name' => '田中　花子',
                'vehicle_number' => '帯広501さ5678',
                'year_month' => date('Y年n月', strtotime($date)),
                'vehicle_info' => 'プリウス',
                'remarks' => 'エアコン不調・要点検',
                'substitute_car' => 'ヴィッツ'
            ],
            [
                'reservation_time' => '10:15',
                'customer_name' => '佐藤　太郎',
                'vehicle_number' => '帯広330き9012',
                'year_month' => date('Y年n月', strtotime($date)),
                'vehicle_info' => 'ワゴンR',
                'remarks' => 'タイヤ交換・洗車込み',
                'substitute_car' => ''
            ],
            // 午後の予約
            [
                'reservation_time' => '13:45',
                'customer_name' => '山田　次郎',
                'vehicle_number' => '帯広301く3456',
                'year_month' => date('Y年n月', strtotime($date)),
                'vehicle_info' => 'アルファード',
                'remarks' => 'ブレーキパッド交換予定',
                'substitute_car' => 'カローラ'
            ],
            [
                'reservation_time' => '15:15',
                'customer_name' => '高橋　美咲',
                'vehicle_number' => '帯広330て7890',
                'year_month' => date('Y年n月', strtotime($date)),
                'vehicle_info' => 'フィット',
                'remarks' => '継続車検・バッテリー要確認',
                'substitute_car' => 'アクア'
            ]
        ];
    }
}