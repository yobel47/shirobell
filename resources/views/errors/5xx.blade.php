@extends('errors.layout')

@section('code', $exception->getStatusCode())
@section('title', 'Terjadi kesalahan')
@section('message', 'Ada masalah di sistem kami. Silakan coba lagi beberapa saat lagi.')
