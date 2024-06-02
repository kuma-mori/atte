@extends('layouts.app')

@section('content')

  <h1>勤怠管理</h1>

  <table>

    <thead>
      <tr>
        <th>名前</th>
        <th>勤務開始時間</th>
        <th>勤務終了時間</th>
        <th>休憩時間</th>
        <th>勤務時間</th> 
      </tr>
    </thead>

    <tbody>
      @foreach ($attendances as $attendance)
        <tr>
          <td>{{ $attendance->user->name }}</td>
          <td>{{ \Carbon\Carbon::parse($attendance->started_at)->format('H:i:s') }}</td>
          <td>{{ $attendance->ended_at ? \Carbon\Carbon::parse($attendance->ended_at)->format('H:i:s') : '-' }}</td>
          <td>{{ gmdate('H:i:s', $attendance->total_break) }}</td>
          <td>{{ gmdate('H:i:s', $attendance->work_time) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

@endsection