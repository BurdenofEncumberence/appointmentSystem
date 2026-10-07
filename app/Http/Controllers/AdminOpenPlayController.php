<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\OpenPlayRegistration;
use App\Models\OpenPlaySession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminOpenPlayController extends Controller
{
    /**
     * Display a listing of Open Play & Tournament sessions.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $typeFilter = $request->query('type', 'all');

        $query = OpenPlaySession::with(['courts', 'registrations'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        if ($statusFilter !== 'all') {
            $query->where('session_status', $statusFilter);
        }

        if ($typeFilter !== 'all') {
            $query->where('session_type', $typeFilter);
        }

        $sessions = $query->paginate(12)->withQueryString();

        $upcomingCount = OpenPlaySession::where('date', '>=', today())
            ->where('session_status', '!=', 'cancelled')
            ->count();

        $totalRegistrations = OpenPlayRegistration::whereIn('payment_status', ['paid', 'pending'])->sum('slots_count');
        $totalOpenPlayRevenue = OpenPlayRegistration::where('payment_status', 'paid')->sum('total_fee');

        return view('admin.open-play.index', [
            'sessions' => $sessions,
            'upcomingCount' => $upcomingCount,
            'totalRegistrations' => $totalRegistrations,
            'totalOpenPlayRevenue' => $totalOpenPlayRevenue,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
        ]);
    }

    /**
     * Show the form for creating a new Open Play session.
     */
    public function create(): View
    {
        abort_unless(Auth::user()?->isManager(), 403, 'Only managers are authorized to create Open Play and Tournament sessions.');

        $courts = Court::where('court_status', 'available')
            ->orderBy('court_name')
            ->get();

        $session = new OpenPlaySession([
            'date' => today()->addDay()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '21:00',
            'max_capacity' => 12,
            'price_per_slot' => 150.00,
            'skill_level' => 'All Levels',
            'session_type' => 'open_play',
        ]);

        return view('admin.open-play.form', [
            'session' => $session,
            'courts' => $courts,
            'allocatedCourtIds' => [],
        ]);
    }

    /**
     * Store a newly created Open Play session in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->isManager(), 403, 'Only managers are authorized to create Open Play and Tournament sessions.');

        $data = $this->validatedData($request);

        $session = OpenPlaySession::create([
            'title' => $data['title'],
            'session_type' => $data['session_type'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_capacity' => $data['max_capacity'],
            'skill_level' => $data['skill_level'],
            'price_per_slot' => $data['price_per_slot'],
            'details' => $data['details'] ?? null,
            'session_status' => 'scheduled',
            'created_by' => Auth::id(),
        ]);

        $session->courts()->sync($data['allocated_courts']);

        return redirect()->route('admin.open-play.index')
            ->with('status', "Open Play session '{$session->title}' created successfully with {$session->courts->count()} allocated court(s).");
    }

    /**
     * Display the specified Open Play session and its player roster.
     */
    public function show(OpenPlaySession $open_play): View
    {
        $session = $open_play->load(['courts', 'registrations.user']);

        return view('admin.open-play.show', [
            'session' => $session,
        ]);
    }

    /**
     * Show the form for editing the specified Open Play session.
     */
    public function edit(OpenPlaySession $open_play): View
    {
        abort_unless(Auth::user()?->isManager(), 403, 'Only managers are authorized to manage Open Play and Tournament sessions.');

        $courts = Court::orderBy('court_name')->get();
        $allocatedCourtIds = $open_play->courts->pluck('id')->all();

        return view('admin.open-play.form', [
            'session' => $open_play,
            'courts' => $courts,
            'allocatedCourtIds' => $allocatedCourtIds,
        ]);
    }

    /**
     * Update the specified Open Play session in storage.
     */
    public function update(Request $request, OpenPlaySession $open_play): RedirectResponse
    {
        abort_unless(Auth::user()?->isManager(), 403, 'Only managers are authorized to manage Open Play and Tournament sessions.');

        $data = $this->validatedData($request);

        $open_play->update([
            'title' => $data['title'],
            'session_type' => $data['session_type'],
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'max_capacity' => $data['max_capacity'],
            'skill_level' => $data['skill_level'],
            'price_per_slot' => $data['price_per_slot'],
            'details' => $data['details'] ?? null,
            'session_status' => $request->input('session_status', $open_play->session_status),
        ]);

        $open_play->courts()->sync($data['allocated_courts']);

        return redirect()->route('admin.open-play.show', $open_play)
             ->with('status', "Session '{$open_play->title}' updated successfully.");
    }

    /**
     * Remove or cancel the specified Open Play session.
     */
    public function destroy(OpenPlaySession $open_play): RedirectResponse
    {
        abort_unless(Auth::user()?->isManager(), 403, 'Only managers are authorized to manage Open Play and Tournament sessions.');

        if ($open_play->activeRegistrations()->exists()) {
            // Cancel session instead of hard deleting to preserve audit history
            $open_play->update(['session_status' => 'cancelled']);
            return redirect()->route('admin.open-play.index')
                ->with('status', "Session '{$open_play->title}' has registered players and was marked as Cancelled.");
        }

        $open_play->courts()->detach();
        $open_play->delete();

        return redirect()->route('admin.open-play.index')
            ->with('status', 'Session deleted successfully.');
    }

    /**
     * Update attendance status for a registered player.
     */
    public function updateAttendance(Request $request, OpenPlayRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_status' => ['required', 'in:registered,show,no_show'],
        ]);

        $registration->update($validated);

        return back()->with('status', "Attendance for {$registration->player_name} updated to " . ucfirst(str_replace('_', ' ', $validated['attendance_status'])) . '.');
    }

    /**
     * Validate session request data.
     */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'session_type' => ['required', 'in:open_play,tournament'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'allocated_courts' => ['required', 'array', 'min:1'],
            'allocated_courts.*' => ['required', 'integer', 'exists:courts,id'],
            'max_capacity' => ['required', 'integer', 'min:2', 'max:100'],
            'skill_level' => ['required', 'string', 'max:100'],
            'price_per_slot' => ['required', 'numeric', 'min:0'],
            'details' => ['nullable', 'string'],
        ], [
            'allocated_courts.required' => 'Please select at least one court to allocate for this session.',
            'allocated_courts.min' => 'Please select at least one court to allocate for this session.',
            'max_capacity.required' => 'Please set the maximum player capacity.',
            'price_per_slot.required' => 'Please specify the participation fee per player ticket.',
        ]);
    }
}
