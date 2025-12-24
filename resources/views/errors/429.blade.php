@extends('errors.layout')

@section('title', 'Too many requests')
@section('code', '429')
@section('message', 'You are sending requests too quickly.')
@section('detail', 'Please wait a moment and try again.')
