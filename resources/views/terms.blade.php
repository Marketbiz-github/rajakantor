@extends('layouts.app-landingpage')

@section('content')

{{-- CENTER CONTENT --}}
<main class="order-0 md:order-none">
  <div class="bg-white p-4 text-[13px] text-[#444]">

    <div class="pt-4 pb-12">
      <div class="font-bold text-lg text-[#333] mb-4">Terms and conditions of use</div>

      <div class="prose text-xs text-gray-600 [&_*]:text-xs">
        {!! $siteSettings->terms !!}
      </div>
      
    </div>


  </div>
</main>

@endsection
