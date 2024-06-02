<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', auth()->user()->id)->latest()->first();
        return view('index', compact('attendance'));
    }
    public function list()
    {
        $attendances = Attendance::with('user')->latest()->get();
        return view('attendance', compact('attendances'));
    }
    public function store(Request $request)
    {
        $type = $request->input('type');//リクエストからタイプを取得
        if (!in_array($type, ['start', 'end'])) {//タイプが'start'または'end'でない場合エラーを発生
            abort(400, '不正なタイプです。');
        }
    
        if ($type === 'start') {//タイプが'start'の場合
            $attendance = Attendance::create([//新しいAttendanceレコードを作成
                'user_id' => auth()->user()->id,//ユーザーIDをセット
                'type' => $type,//タイプをセット
                'started_at' => now(),//現在の時刻を開始時刻としてセット
                'ended_at' => null,//終了時刻をnullにセット
            ]);
        } elseif ($type === 'end') {//タイプが'end'の場合
            $attendance = Attendance::where('user_id', auth()->user()->id)
                ->whereNull('ended_at') //終了時間がnullのものを探す
                ->latest()
                ->first();
    
                if ($attendance) { // 該当のレコードがある場合
                    $attendance->update(['ended_at' => now()]); // 終了時刻を更新
    
                    // 休憩時間の合計を計算
                    $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
                    $breakTimeSeconds = 0;
    
                    foreach ($breaks as $break) {
                        // Carbonに変換してからdiffInSecondsを使用
                        $start = Carbon::parse($break->started_at);
                        $end = Carbon::parse($break->ended_at);
                        $breakTimeSeconds += $end->diffInSeconds($start);
                    }

                //休憩時間を勤務時間から引いて、実際の勤務時間を計算
                $workTimeSeconds = $attendance->ended_at->diffInSeconds($attendance->started_at);
                $actualWorkTimeSeconds = $workTimeSeconds - $breakTimeSeconds;

                //休憩時間と勤務時間を保存
                $attendance->total_break = $breakTimeSeconds;
                $attendance->work_time = $actualWorkTimeSeconds;
                $attendance->save();
            } else {//該当のレコードがない場合
                abort(400, '終了するレコードが見つかりませんでした。');
            }
        }
    
        return redirect()->route('attendance');//attendanceルートにリダイレクト

    }
    public function workStart(Request $request, Attendance $attendance)
    {
        $request->validate([
            'id' => 'required|exists:attendances,id',//リクエストからのIDが存在するかチェック
        ]);
        $attendance = Attendance::find($request->input('id'));//IDに対応するAttendanceレコードを取得
        $attendance->started_at = now();//現在の時刻を開始時刻としてセット
        $attendance->save();//変更を保存
        return Redirect::route('attendance');//attendanceルートにリダイレクト
    }
    public function workEnd(Request $request)
    {
        $attendance = Attendance::where('user_id', auth()->user()->id)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if ($attendance) {
            $attendance->ended_at = now();

            // 休憩時間の合計を計算
            $breaks = BreakTime::where('attendance_id', $attendance->id)->get();
            $breakTimeSeconds = 0;

            foreach ($breaks as $break) {
                $breakTimeSeconds += $break->ended_at->diffInSeconds($break->started_at);
            }

            // 休憩時間を勤務時間から引いて、実際の勤務時間を計算
            $workTimeSeconds = $attendance->ended_at->diffInSeconds($attendance->started_at);
            $actualWorkTimeSeconds = $workTimeSeconds - $breakTimeSeconds;

            // 保存する
            $attendance->total_break = $breakTimeSeconds;
            $attendance->work_time = $actualWorkTimeSeconds;
            $attendance->save();

            return redirect()->route('attendance');
        }
    }
}