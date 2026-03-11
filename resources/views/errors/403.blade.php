@extends('errors::minimal')

@section('title', 'Acesso negado')
@section('code', '403')
@section('message')
    Acesso não autorizado. Redirecionando em <span id="count">3</span> segundos...
@endsection

<script>
let tempo = 3;

const contador = setInterval(() => {
    tempo--;
    document.getElementById("count").innerText = tempo;

    if (tempo <= 0) {
        clearInterval(contador);

        // volta para página anterior
        window.history.back();
    }
}, 1000);
</script>