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
                            <form action="{{ route('dirDelete', $folder['directory'] ) }}" method="POST" >
                                @csrf
                                @method('delete')
                                <button type="submit" onclick="return confirm('Are you sure?')" class=" btn btn-sm btn-outline-danger text-gray-900 m-1 ">{{ $folder['directory'] }} </button>
                            </form>  
                        @endforeach
                    </div>
                @endif     
        @endforeach
                               
@endforeach