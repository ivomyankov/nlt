<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md" style="background: rgb(173, 230, 187); width:30%; margin:auto; margin-top:20px; margin-bottom:20px;" role="alert">
        <div class="flex p-12 ">
          <div class="py-1"><svg class="fill-current h-6 w-6 text-teal-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
          <div style="margin-left:20px;">
            <p class="font-bold">Html uploaded successfully</p>
            <p class="text-sm">Open html in new tab: <x-jet-nav-link href="{{ url('storage/newsletters/' . $cache['folder'] . '/index.html') }}" target="_blank" :active="request()->routeIs('upload')">{{ $cache['folder'] }}</x-jet-nav-link></p>
            <p class="text-sm">UTM: <b>{{ $cache['utm'] == 1 ? "Yes" : "No" }}</b>
            <p class="text-sm">Links: <b style="text-decoration: underline" onclick="$( '#links').toggle();">Toggle</b>
            {{-- dd($cache['links']) --}}
                
            </p>
            <p class="text-sm">Edit html: <x-jet-nav-link href="{{ route('edit') }}" target="_blank" >{{ $cache['folder'] }}</x-jet-nav-link></p>
            <p class="text-sm">Download html: <x-jet-nav-link href="{{ route('zip', ['id' => $cache['nl_id'] ]) }}" target="_blank" >Download</x-jet-nav-link></p> 
          </div>
        </div>
    </div>

    <div id="links" class="w-70 rounded-b text-teal-900 px-4 py-3 shadow-md bg-slate-50" style="background: blanchedalmond; width:90%; margin:auto; display:none;">
        @foreach ($cache['links'] as $key => $value)  
            {{ $value }} <br>
        @endforeach
    </div>


    
    <div class="text-center">
        <x-uploaded :var="555" />
    </div>

</x-app-layout>
