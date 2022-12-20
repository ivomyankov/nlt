@for ($i = 1; $i < 8; $i++) 
    <div class="border {{ $convertion[$i] }} {{ $this->colByWeekDay($convertion[$i]) }}">{{ $convertion[$i]}}</div>
@endfor
@foreach ($weeks as $week_number => $week) {{-- dd($week) --}}
    
    <div class="col-sm-12 text-center p-2">Week{{ $week_number }}</div>{{-- dd($weeks,$nls) --}}
    
        @foreach ($week as $week_day => $weeks)
            
                @if (empty($week[$week_day]))
                    <div class="border {{ $week_day }} {{ $this->colByWeekDay($week_day) }}"></div>
                @else
                    <div class="border {{ $week_day }} {{ $this->colByWeekDay($week_day) }}">
                        @foreach ($weeks as $day) {{-- dd($day['nl']->date) --}}
                            @if ($day['nl']->archived == 1)
                                <a class="btn btn-sm btn-outline-info text-red-900 m-1 " href="{{ route('unzip', ['id' => $day['nl']->id] )}}" >{{ $day['nl']->date . '_' . $day['nl']->company->name }}</a>  
                            @else
                                <a class="btn btn-sm btn-outline-primary text-gray-900 m-1 " href="{{ url('storage/newsletters/' . $day['nl']->date . '_' . $day['nl']->company->name . '/index.html')}}" target="_blank" >{{ $day['nl']->date . '_' . $day['nl']->company->name }}</a>  
                            @endif
                            
                        @endforeach
                    </div>
                @endif     
        @endforeach
                               
@endforeach