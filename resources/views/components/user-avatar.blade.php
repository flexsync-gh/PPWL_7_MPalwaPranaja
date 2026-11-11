@props([
    'user',
    'size' => 48,
    'fontSize' => null,
    'class' => '',
])

@php
    $computedFontSize = $fontSize ?? max(12, (int) round($size * 0.38));
    $textColorClass = $user->avatar_color === 'warning' ? 'text-dark' : 'text-white';
@endphp

@if($user->profile_photo_url)
    <img
        {{ $attributes->merge(['class' => "rounded-circle object-fit-cover border shadow-sm {$class}"]) }}
        src="{{ $user->profile_photo_url }}"
        alt="Foto profil {{ $user->name }}"
        style="width: {{ $size }}px; height: {{ $size }}px;"
    >
@else
    <div
        {{ $attributes->merge(['class' => "bg-{$user->avatar_color} {$textColorClass} rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0 {$class}"]) }}
        style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ $computedFontSize }}px;"
        aria-label="Avatar {{ $user->name }}"
    >
        {{ $user->initials }}
    </div>
@endif
