@extends('errors::minimal')

@section('title', __('Muitas requisições'))
@section('code', '429')
@section('message', __('Atenção muitas requisições, click fora do aviso para tentar novamente!'))