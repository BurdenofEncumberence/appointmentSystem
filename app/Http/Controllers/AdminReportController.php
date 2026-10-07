<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminReportController extends Controller
{
    /**
     * Handle streaming export of various management reports (CSV or PDF).
     */
    public function export(Request $request, ReportExportService $exportService): Response
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:overall,financial,utilization'],
            'period' => ['required', 'string', 'in:this_day,today,this_month,last_month,this_year'],
            'format' => ['nullable', 'string', 'in:csv,pdf'],
        ]);

        $user = Auth::user();
        $type = $validated['type'];
        $period = $validated['period'];
        $format = $validated['format'] ?? 'csv';

        if ($format === 'pdf') {
            return match ($type) {
                'financial' => $exportService->exportFinancialPdf($period, $user),
                'utilization' => $exportService->exportUtilizationPdf($period, $user),
                default => $exportService->exportOverallPdf($period, $user),
            };
        }

        return match ($type) {
            'financial' => $exportService->exportFinancial($period, $user),
            'utilization' => $exportService->exportUtilization($period, $user),
            default => $exportService->exportOverall($period, $user),
        };
    }
}
