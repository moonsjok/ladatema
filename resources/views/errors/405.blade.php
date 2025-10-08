@extends('errors.error_layout')

@section('title', __('Method Not Allowed'))
@section('code', '405')
@section('message',
    __("Something is broken.\n Please let us know what you were doing when this error occurred. \n We
    will fix it as soon as possible. \n Sorry for any inconvenience caused."))
