@extends('layouts.master')
@section('content')

@if(isset($page->page_detail))
<br />
<h2>{{ $page->page_detail[0]->title }}</h2>

<br />
{!! $page->page_detail[0]->description !!}
@else
<h2>comming soon</h2>
@endif

@endsection
@section('script')
@endsection
