@props(['otherUser' => null])

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if (auth()->check())
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="user-type" content="{{ get_class(auth()->user()) }}">
    <meta name="user-name" content="{{ auth()->user()->name }}">
    @if ($otherUser)
        <meta name="other-user-id" content="{{ $otherUser->id }}">
    @endif
@endif
