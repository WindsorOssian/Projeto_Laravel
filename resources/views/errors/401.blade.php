@extends('errors::minimal')

@section('title', 'Sessão inválida')
@section('code', '401')
@section('message')

Você tentou acessar uma área do sistema diretamente pela URL.<br><br>

Redirecionando para o login...

<meta http-equiv="refresh" content="4;url=/login">

@endsection