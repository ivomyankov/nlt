@for ($i = 1; $i < 8; $i++) 
    <div class="border {{ $convertion[$i] }} {{ $this->colByWeekDay($convertion[$i]) }}">{{ $convertion[$i]}}</div>
@endfor
@foreach ($folders as $week_number => $week) {{-- dd($week) --}}
    
    <div class="col-sm-12 text-center p-2">Week{{ $week_number }}</div>
    
        @foreach ($week as $week_day => $folders)
            
                @if (empty($week[$week_day]))
                    <div class="border {{ $week_day }} {{ $this->colByWeekDay($week_day) }}"></div>
                @else
                    <div class="border {{ $week_day }} {{ $this->colByWeekDay($week_day) }}">
                        @foreach ($folders as $folder)
                            <a class="btn btn-sm btn-outline-primary text-gray-900 m-1 " href="{{ url('storage/newsletters/' . $folder['directory'] . '/index.html')}}" target="_blank" >{{ $folder['directory'] }}</a>  
                        @endforeach
                    </div>
                @endif     
        @endforeach
                               
@endforeach