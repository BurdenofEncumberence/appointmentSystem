<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /**
     * Handle streaming export of various management reports.
     */
    public function export(Request $request, ReportExportService $exportService): StreamedResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:overall,financial,utilization'],
            'period' => ['required', 'string', 'in:this_day,today,this_month,last_month,this_year'],
        ]);

        $user = Auth::user();
        $type = $validated['type'];
        $period = $validated['period'];

        return match ($type) {
            'financial' => $exportService->exportFinancial($period, $user),
            'utilization' => $exportService->exportUtilization($period, $user),
            default => $exportService->exportOverall($period, $user),
        };
    }
}
