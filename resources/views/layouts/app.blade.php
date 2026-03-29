@extends('layouts.base')

@section('title', $title ?? 'Loading...')

@section('content')
    <div class="page">
        <x-backend.sidebar />
        <x-backend.header />
        <x-backend.content :page-title="$pageTitle ?? null" :page-pretitle="$pagePretitle ?? null">

            <x-slot name="button">
                {{ $button ?? '' }}
            </x-slot>

            {{ $slot }}
        </x-backend.content>
    </div>
@endsection
