@extends('errors.minimal')

@section('code', '429')
@section('title', 'Zu viele Anfragen')
@section('message', 'Du hast in kurzer Zeit sehr viele Anfragen gesendet. Bitte warte eine Minute und versuche es dann nochmals.')
@section('back', true)
