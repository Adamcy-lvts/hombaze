@extends('errors.layout')

@section('title', 'Server error')
@section('code', '500')
@section('message', 'Something went wrong on our side.')
@section('detail', 'Please try again shortly. If the issue persists, let us know.')
