@extends('errors::minimal')

@section('title', 'Error')
@section('code', '422')
@section('message', $exception->getMessage())
