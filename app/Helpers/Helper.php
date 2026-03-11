<?php

namespace App\Helpers;


use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use stdClass;
use Throwable;

class Helper
{


    public static function formatarValorMonetarioPtBr(null|float|string $val): ?string
    {
        if (str($val)->isEmpty()) {
            return null;
        }
        return number_format($val, 2, ",", ".");
    }

    public static function formatarDecimalDb(mixed $val): ?float
    {
        if (str($val)->isEmpty()) {
            return null;
        }

        return (float)number_format(Str::replace([".", ","], ["", "."], $val), 2, ".", "");
    }

    public static function formatarValorMonetarioDB(string $val): float
    {
        return number_format($val, 2, ".", "");
    }


    public static function formatarDataDatePicker(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            $parts = explode('/', $date);
            return "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function formatarDataDB(?string $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        $ex = explode("/", $data);

        return $ex[2] . "-" . $ex[1] . "-" . $ex[0];
    }

    public static function formatarDataPtBr(?string $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        return date("d/m/Y", strtotime($data));
    }

    public static function formatarDateTimePtBr(?string $data): ?string
    {
        if (empty($data)) {
            return null;
        }

        return date("d/m/Y H:i:s", strtotime($data));
    }

    public static function formatarPhoneBR(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        if (strlen($phone) === 10) {

            return "(" . substr($phone, 0, 2) . ") " . substr($phone, 2, 4) . "-" . substr($phone, 6, 4);

        } elseif (strlen($phone) === 11) {

            return "(" . substr($phone, 0, 2) . ") " . substr($phone, 2, 5) . "-" . substr($phone, 7, 4);

        } else {
            return $phone;
        }
    }

    public static function formatarCPF(string $cpf): string
    {
        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }

        return $cpf;
    }

    public static function formatarCNPJ(?string $cnpj): ?string
    {
        if (strlen($cnpj) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
        }

        return $cnpj;
    }

    public static function formatarCpfCnpj(?string $cpfCnpj): ?string
    {
        if (empty($cpfCnpj)) {
            return null;
        }

        $cpfCnpj = preg_replace('/[^0-9]/', '', $cpfCnpj);

        if (strlen($cpfCnpj) === 11) {

            return self::formatarCPF($cpfCnpj);

        } else if (strlen($cpfCnpj) === 14) {

            return self::formatarCNPJ($cpfCnpj);
        }

        return $cpfCnpj;
    }

    public static function formatarCep(?string $cep): ?string
    {
        if (empty($cep)) {
            return null;
        }

        if (strlen($cep) === 8) {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
        }

        return $cep;
    }


    public static function obterEnderecoPorCep(?string $cep): ?stdClass
    {

        try {

            if (empty($cep)) {
                return null;
            }

            $cep = preg_replace('/[^0-9]/', '', $cep);

            if (strlen($cep) !== 8) {
                return null;
            }

            $url = "https://viacep.com.br/ws/{$cep}/json/";

            return json_decode(file_get_contents($url));

        } catch (Throwable $th) {
            return null;
        }

    }

    public static function eUltimoDiaDoMes(string|Carbon|CarbonImmutable|null $data): bool
    {
        if (empty($data)) {
            return false;
        }

        if (is_string($data)) {
            $data = Carbon::parse($data);
        }

        return $data->day === $data->daysInMonth;
    }

    public static function formatarHorasMinutos($value): string
    {
        $horas   = $value > 0 ? floor($value) : 0;
        $minutos = ($value - $horas) * 60;
        return sprintf('%02d:%02d', $horas, $minutos);
    }


    public static function replaceEspecialChars(string $str): array|string
    {
        return preg_replace("/[^a-zA-Z0-9\s]/", "", str_replace(
            ["á", "à", "ã", "â", "é", "è", "ê", "í", "ì", "î", "ó", "ò", "õ", "ô", "ú", "ù", "û", "ç", "Ç", "Á", "À", "Ã", "Â", "É", "È", "Ê", "Í", "Ì", "Î", "Ó", "Ò", "Õ", "Ô", "Ú"],
            ["a", "a", "a", "a", "e", "e", "e", "i", "i", "i", "o", "o", "o", "o", "u", "u", "u", "c", "C", "A", "A", "A", "A", "E", "E", "E", "I", "I", "I", "O", "O", "O", "O", "U"],
            $str
        ));
    }


    public static function dataPorExtenso($data): string
    {
        return Carbon::parse($data)->locale('pt_BR')->translatedFormat('j \d\e F \d\e Y');
    }

    public static function extrairDia($data): string
    {
        return Carbon::parse($data)->day;
    }


    public static function obterDDDNumeroSeparados($phone): array
    {

        $ddd    = substr($phone, 0, 2);
        $numero = substr($phone, 2);

        return [
            'ddd'    => $ddd,
            'numero' => $numero,
        ];
    }

    public static function obterValorEmCentavos($valor): float
    {

        return (float)($valor * 100);
    }


    public static function verificarSeExisteForeign($relacoes, $model): bool
    {
        foreach ($relacoes as $relation) {
            if ($model->$relation()->exists()) {

                return true;
            }
        }

        return false;
    }


    public static function formatarTelefoneRemoverDDI(?string $telefone): ?string
    {
        if (empty($telefone)) {
            return null;
        }

        $telefone = preg_replace('/[^0-9]/', '', $telefone);


        if (empty($telefone)) {
            return null;
        }

        if (strlen($telefone) == 11) {

            $telefone = preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone);

        } else if (strlen($telefone) == 10) {

            $telefone = preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $telefone);

        } else if (strlen($telefone) == 12) {

            $telefone = preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', substr($telefone, 2, 10));

        } else if (strlen($telefone) == 13) {

            $telefone = preg_replace('/^(\d{2})(\d{5})(\d{4})(\d{4})$/', '($1) $2-$3', substr($telefone, 2, 11));

        }

        return $telefone;
    }


    public static function nomeMes(int $numMes, bool $short = false): ?string
    {
        if ($numMes < 1 || $numMes > 12) {
            return null;
        }

        if ($short) {

            return CarbonImmutable::createFromFormat('!m', $numMes)->locale('pt_BR')->translatedFormat('M');
        }

        return CarbonImmutable::createFromFormat('!m', $numMes)->locale('pt_BR')->translatedFormat('F');

    }

    public static function removerCaracteresLatinos(?string $str): ?string
    {
        if (empty($str)) {
            return null;
        }

        return Str::ascii($str);
    }


    public static function obterTempoAteFimDoDiaEmSegundos(): float|int
    {
        return Carbon::now('America/Sao_Paulo')->diffInHours(Carbon::now('America/Sao_Paulo')->endOfDay()) * 60;
    }


}
