
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css"/>  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet"> 
@endpush
<x-app-layout>
  
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload a newsletter') }}
        </h2>
    </x-slot>

    
            <div class="mt-5 bg-white overflow-hidden max-w-xl mx-auto shadow-xl sm:rounded-lg p-5">
                
                <form action="{{url('upload')}}" method="POST" enctype="multipart/form-data" class="px-4" style="width: 400px; margin: auto;">
                    @csrf
                    <center><h1>Source {{ $source }} </h1></center>
                    <div class="form-group text-center py-4">
                    <a href="{{ route('upload', ['source' => 'url']) }}" class="btn btn-outline-primary m-1 w-25">URL</a>
                    <a href="{{ route('upload', ['source' => 'file']) }}" class="btn btn-outline-primary m-1 w-25">FILE</a>
                    </div>
                    <div class="form-group">
                              <label for="name">Name:</label>
                              <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" >
                    </div>
                    <br>
                    <div class="form-group date">
                        <label for="date">Date:</label><br>
                        <input class="datepicker form-control" type="text" name="date" value="{{ date('Y-m-d') }}" >
                        <span class="input-group-addon"><img style="height: 30px !important; position: absolute; margin-top: -35px; margin-left: 315px;" src="https://cdn-icons-png.flaticon.com/512/55/55281.png"></span>
                    </div>
                    @switch($source)
                        @case('url')  
                            <div class="form-group py-4">
                                <label for="exampleInputEmail1">URL</label>
                                <input type="text" id="url" name="url" class="form-control" >
                            </div>  
                            <button type="submit" class="btn btn-primary">Submit</button>
                            
                            @break                    
                        @case('file')
                            <br>
                            {{ Form::file('file') }}
                            <br>
                            <br>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            
                            @break                    
                        @default
                            
                    @endswitch    
                </form>  
            </div>
@push('scripts')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript">
        $('.datepicker').datepicker({  
            format: 'yyyy-mm-dd',
            autoclose: true
        });  
    </script> 
@endpush            
</x-app-layout>

