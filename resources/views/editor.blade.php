
@push('styles')
    <!--<link rel="stylesheet" href="{!! url('assets/richtexteditor/rte_theme_default.css') !!}" /> 
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">-->
@endpush
<x-app-layout>
  
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload a newsletter') }}
        </h2>
    </x-slot>

    <br>
    <!--<div class="shadow-xl mx-auto mt-5 text-center max-w-6xl bg-white"></div>-->
    <form action="{{url('update')}}" method="POST" enctype="multipart/form-data" class="text-center" style="width: 1500px; margin: auto;">
        @csrf
        <textarea id="html" class="description shadow-xl mt-3" rows="40" style="width:100%;" name="html">{{ $html }}</textarea>
        <button type="submit" class="btn btn-primary m-4">Submit</button>
    </form> 
    
        
@push('scripts')
    <!--<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
    <script src="{!! url('assets/tinymce/js/tinymce/tinymce.min.js') !!}"></script>
    <script>
    tinymce.init({
        selector:'textarea.description',
        plugins: ['code', 'preview'],
        toolbar:"undo redo | fontselect styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | codesample action section button | source code | preview",
        //menubar: 'tools',
        width: 1000,
        height: 1300
    });
    </script>  

    <script type="text/javascript" src="{!! url('assets/richtexteditor/rte.js') !!}"></script>
    <script type="text/javascript" src='{!! url('assets/richtexteditor/plugins/all_plugins.js') !!}'></script>
    <script>
        var editor1 = new RichTextEditor("#html");
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#html').summernote();
        });
    </script>
-->
@endpush            
</x-app-layout>

