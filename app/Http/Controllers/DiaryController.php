<?php

namespace App\Http\Controllers;

use App\Contracts\AppointmentRepositoryInterface;
use App\Enums\AppointmentStatus;
use App\Enums\AppointmentType;
use App\Models\Diary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function __construct(private readonly AppointmentRepositoryInterface $appointments)
    {
    }

    /**
     * Diary calendar — week view by default, day view when requested.
     * Query parameters: diary_id, date (Y-m-d), view (week|day), show_cancelled.
     */
    public function index(Request $request): View
    {
        $diaries = Diary::orderBy('name')->get();

        $diary = $request->filled('diary_id')
            ? Diary::findOrFail($request->integer('diary_id'))
            : $diaries->first();

        $anchorDate   = $request->filled('date')
            ? Carbon::parse($request->string('date'))
            : Carbon::today();

        $viewMode      = $request->input('view', 'week');
        $showCancelled = $request->boolean('show_cancelled', false);

        [$from, $to] = match ($viewMode) {
            'day'   => [$anchorDate->copy()->startOfDay(), $anchorDate->copy()->endOfDay()],
            default => [$anchorDate->copy()->startOfWeek(), $anchorDate->copy()->endOfWeek()],
        };

        $appointments = $diary
            ? $this->appointments->forDiaryAndDateRange($diary, $from, $to, $showCancelled)
            : collect();

        return view('diary.index', compact(
            'diaries', 'diary', 'anchorDate', 'viewMode', 'showCancelled', 'appointments', 'from', 'to',
        ) + [
            'appointmentTypes'    => AppointmentType::options(),
            'appointmentStatuses' => AppointmentStatus::options(),
        ]);
    }
}
