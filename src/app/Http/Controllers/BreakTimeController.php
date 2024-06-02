<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BreakTimeController extends Controller
{
    public function index()
    {
        return view('attendance', compact('attendances'));
    }
    
    public function store(Request $request)
    {
        $type = $request->input('type');
        if (!in_array($type, ['start', 'end'])) {
            abort(400, '不正なタイプです。');
        }

        //attendance_id をフォームから取得する
        $latestAttendance = Attendance::where('user_id', auth()->user()->id)
        ->latest()
        ->first();

        if (!$latestAttendance) {
            abort(400, '出勤記録が見つかりませんでした。');
        }

        if ($type === 'start') { // タイプが 'start' の場合
            BreakTime::create([ // 新しい BreakTime レコードを作成
                'attendance_id' => $latestAttendance->id, // 最新の attendance_id をセット
                'started_at' => now(), // 現在の時刻を開始時刻としてセット
                'ended_at' => null, // 終了時刻を null にセット
            ]);
        } elseif ($type === 'end') { // タイプが 'end' の場合
            $breakTime = BreakTime::where('attendance_id', $latestAttendance->id)
            ->whereNull('ended_at') // 終了時間が null のものを探す
            ->latest()
            ->first();

            if ($breakTime) { // 該当のレコードがある場合
                $breakTime->update(['ended_at' => now()]); // 終了時刻を更新
            } else { // 該当のレコードがない場合
                abort(400, '終了する休憩時間が見つかりませんでした。');
            }
        }

        return redirect()->route('attendance');
    }
    
    public function breakStart(Request $request)
    {
        $this->validate($request, [
            'attendance_id' => 'required|exists:attendances,id',
        ]);
    
        $breakTime = new BreakTime();
        $breakTime->attendance_id = $request->input('attendance_id');
        $breakTime->started_at = now();
        $breakTime->save(); // ここでデータベースに保存する
    
        return Redirect::route('attendance');
    }
    
    public function breakEnd(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:breaks,id',
            'ended_at' => 'required|date|after:started_at',
        ]);
    
        $breakTime = BreakTime::find($request->id);
    
        if ($breakTime) {
            $breakTime->ended_at = now();
            $breakTime->save(); // ここでデータベースに保存する
        }
    
        return Redirect::route('attendance');
    }
}