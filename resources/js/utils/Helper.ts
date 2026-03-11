/* Helper.ts - equivalente ao Helper PHP */

export type EnderecoViaCep = {
    cep?: string;
    logradouro?: string;
    complemento?: string;
    bairro?: string;
    localidade?: string;
    uf?: string;
    ibge?: string;
    gia?: string;
    ddd?: string;
    siafi?: string;
};

export class Helper {
    /* =======================
     * MONETÁRIO
     * ======================= */

    static formatarValorMonetarioPtBr(
        val?: number | string | null,
    ): string | null {
        if (val === null || val === undefined || val === '') return null;

        const num = Number(val);
        if (isNaN(num)) return null;

        return num.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    static formatarDecimalDb(val: any): number | null {
        if (val === null || val === undefined || val === '') return null;

        const normalizado = String(val).replace(/\./g, '').replace(',', '.');

        const num = Number(normalizado);
        return isNaN(num) ? null : Number(num.toFixed(2));
    }

    static formatarValorMonetarioDB(val: string): number {
        return Number(Number(val).toFixed(2));
    }

    /* =======================
     * DATAS
     * ======================= */

    static formatarDataDatePicker(date?: string | null): string | null {
        if (!date) return null;

        if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
            return date;
        }

        if (/^\d{2}\/\d{2}\/\d{4}$/.test(date)) {
            const [d, m, y] = date.split('/');
            return `${y}-${m}-${d}`;
        }

        const parsed = new Date(date);
        if (isNaN(parsed.getTime())) return null;

        return parsed.toISOString().slice(0, 10);
    }

    static formatarDataDB(data?: string | null): string | null {
        if (!data) return null;
        const [d, m, y] = data.split('/');
        return `${y}-${m}-${d}`;
    }

    static formatarDataPtBr(data?: string | null): string | null {
        if (!data) return null;
        return new Date(data).toLocaleDateString('pt-BR');
    }

    static formatarDateTimePtBr(data?: string | null): string | null {
        if (!data) return null;
        return new Date(data).toLocaleString('pt-BR');
    }

    static dataPorExtenso(data: string): string {
        return new Date(data).toLocaleDateString('pt-BR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }

    static extrairDia(data: string): string {
        return String(new Date(data).getDate());
    }

    static eUltimoDiaDoMes(data?: string | Date | null): boolean {
        if (!data) return false;

        const d = new Date(data);
        const ultimoDia = new Date(
            d.getFullYear(),
            d.getMonth() + 1,
            0,
        ).getDate();

        return d.getDate() === ultimoDia;
    }

    static nomeMes(numMes: number, short = false): string | null {
        if (numMes < 1 || numMes > 12) return null;

        const date = new Date(2024, numMes - 1, 1);

        return date.toLocaleDateString('pt-BR', {
            month: short ? 'short' : 'long',
        });
    }

    /* =======================
     * TELEFONE / DOCUMENTOS
     * ======================= */

    static formatarPhoneBR(phone?: string | null): string | null {
        if (!phone) return null;

        const clean = phone.replace(/\D/g, '');

        if (clean.length === 10) {
            return `(${clean.slice(0, 2)}) ${clean.slice(2, 6)}-${clean.slice(6)}`;
        }

        if (clean.length === 11) {
            return `(${clean.slice(0, 2)}) ${clean.slice(2, 7)}-${clean.slice(7)}`;
        }

        return phone;
    }

    static formatarCPF(cpf: string): string {
        if (cpf.length === 11) {
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }
        return cpf;
    }

    static formatarCNPJ(cnpj?: string | null): string | null {
        if (!cnpj) return null;

        if (cnpj.length === 14) {
            return cnpj.replace(
                /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,
                '$1.$2.$3/$4-$5',
            );
        }

        return cnpj;
    }

    static formatarCpfCnpj(cpfCnpj?: string | null): string | null {
        if (!cpfCnpj) return null;

        const clean = cpfCnpj.replace(/\D/g, '');

        if (clean.length === 11) return this.formatarCPF(clean);
        if (clean.length === 14) return this.formatarCNPJ(clean);

        return clean;
    }

    static formatarCep(cep?: string | null): string | null {
        if (!cep) return null;

        const clean = cep.replace(/\D/g, '');

        if (clean.length === 8) {
            return clean.replace(/(\d{5})(\d{3})/, '$1-$2');
        }

        return cep;
    }

    static obterDDDNumeroSeparados(phone: string): {
        ddd: string;
        numero: string;
    } {
        return {
            ddd: phone.slice(0, 2),
            numero: phone.slice(2),
        };
    }

    static formatarTelefoneRemoverDDI(telefone?: string | null): string | null {
        if (!telefone) return null;

        let clean = telefone.replace(/\D/g, '');

        if (clean.length === 12 || clean.length === 13) {
            clean = clean.slice(2);
        }

        return this.formatarPhoneBR(clean);
    }

    /* =======================
     * OUTROS
     * ======================= */

    static formatarHorasMinutos(value: number): string {
        const horas = value > 0 ? Math.floor(value) : 0;
        const minutos = Math.round((value - horas) * 60);
        return `${String(horas).padStart(2, '0')}:${String(minutos).padStart(2, '0')}`;
    }

    static replaceEspecialChars(str: string): string {
        return str
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-zA-Z0-9\s]/g, '');
    }

    static removerCaracteresLatinos(str?: string | null): string | null {
        if (!str) return null;
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    static obterValorEmCentavos(valor: number): number {
        return Math.round(valor * 100);
    }

    static obterTempoAteFimDoDiaEmMinutos(): number {
        const agora = new Date();
        const fim = new Date();
        fim.setHours(23, 59, 59, 999);

        return Math.floor((fim.getTime() - agora.getTime()) / 60000);
    }

    /* =======================
     * VIA CEP
     * ======================= */

    static async obterEnderecoPorCep(
        cep?: string | null,
    ): Promise<EnderecoViaCep | null> {
        if (!cep) return null;

        const clean = cep.replace(/\D/g, '');
        if (clean.length !== 8) return null;

        try {
            const response = await fetch(
                `https://viacep.com.br/ws/${clean}/json/`,
            );
            return await response.json();
        } catch {
            return null;
        }
    }
}
