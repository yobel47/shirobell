@extends('errors.layout')

@section('code', $exception->getStatusCode())
@section('title', 'Permintaan tidak dapat diproses')
@section('message', 'Permintaan Anda tidak dapat kami proses. Periksa kembali tautannya atau kembali ke beranda.')
