@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="attendance__alert">
  
</div>

<div class="attendance__content">
  <div class="attendance__panel">
    <form id="startWorkForm" class="attendance__button" action="{{ route('attendance.store') }}" method="post">
      @csrf
      <input type="hidden" name="type" value="start">
      <input type="hidden" name="time" value="{{ now() }}">
      <button id="startWorkButton" class="attendance__button-submit" type="submit" onclick="window.location.href='/attendance'">勤務開始</button>
    </form>
    
    @if($attendance)
      <form id="endWorkForm" class="attendance__button" action="{{ route('attendance.store') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="end">
        <input type="hidden" name="time" value="{{ now() }}">
        <button id="endWorkButton" class="attendance__button-submit" type="submit" onclick="window.location.href='/attendance'">勤務終了</button>
      </form>

      <form id="startBreakForm" class="attendance__button" action="{{ route('breaktime.store') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="start">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <input type="hidden" name="time" value="{{ now() }}">
        <button id="startBreakButton" class="attendance__button-submit" type="submit" onclick="window.location.href='/attendance'">休憩開始</button>
      </form>

      <form id="endBreakForm" class="attendance__button" action="{{ route('breaktime.store') }}" method="post">
        @csrf
        <input type="hidden" name="type" value="end">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <input type="hidden" name="time" value="{{ now() }}">
        <button id="endBreakButton" class="attendance__button-submit" type="submit" onclick="window.location.href='/attendance'">休憩終了</button>
      </form>
    @endif
  </div>
</div>
</script>
@endsection