<script setup lang="ts">
import { Form, Head, router, useForm, usePage } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import vendedores from '@/routes/vendedores';
import { onMounted } from 'vue';
import { toast } from 'vue-sonner';
import { vMaska } from "maska/vue";

const page = usePage();

const props = page.props as unknown as {
    idVendedor?: number | null;
    vendedor?: Record<string, any>;
};

const vendedor = props.vendedor;
const idVendedor = props.idVendedor;

// Checa explicitamente null/undefined e mostra o erro, expulsa para listar
onMounted(() => {
    if (props.idVendedor != null && !vendedor) {
        router.visit(vendedores.listar().url);
        // toast.error('Cliente não encontrado.');
    }
});

// Reatividade dentro do view, alteração em tempo real
const form = useForm({
    id_vendedor: idVendedor ?? null,
    nome: vendedor?.user?.name ?? '',
    email: vendedor?.user?.email ?? '',
    cpf: vendedor?.cpf ?? '',
    comissao: vendedor?.comissao ?? '',
    cep: vendedor?.endereco?.cep ?? '',
    rua: vendedor?.endereco?.rua ?? '',
    numero: vendedor?.endereco?.numero ?? '',
    complemento: vendedor?.endereco?.complemento ?? '',
    bairro: vendedor?.endereco?.bairro ?? '',
    cidade: vendedor?.endereco?.cidade ?? '',
    estado: vendedor?.endereco?.estado ?? '',
});

// Impede passar de 100%
function onComissaoInput(e: Event) {
    const el = e.target as HTMLInputElement;
    let v = el.value;

    // aceita vírgula como separador decimal
    v = v.replace(',', '.');

    // apenas números e ponto
    v = v.replace(/[^\d.]/g, '');

    // apenas UM ponto
    const dotIndex = v.indexOf('.');
    if (dotIndex !== -1) {
        v = v.slice(0, dotIndex + 1) + v.slice(dotIndex + 1).replace(/\./g, '');
    }

    // se começa com ponto, prefixa 0
    if (v.startsWith('.')) {
        v = '0' + v;
    }

    // limita a 1 casa decimal
    if (v.includes('.')) {
        const [intPart, decPart] = v.split('.');
        v = intPart + '.' + decPart.slice(0, 1);
    }

    // bloqueia valores acima de 100
    const n = Number(v);
    if (!isNaN(n) && n > 100) {
        v = '100';
    }

    // se for exatamente 100, não permite ponto
    if (v === '100.' || v.startsWith('100.')) {
        v = '100';
    }

    form.comissao = v;
}

// Se passar de 100% vai reconfigurar para voltar a 100%
function onComissaoBlur() {
    if (!form.comissao) return;

    let n = Number(form.comissao.replace(',', '.'));

    if (isNaN(n)) {
        form.comissao = '';
        return;
    }

    // clamp final de segurança
    n = Math.max(0, Math.min(100, n));

    // remove .0 desnecessário
    form.comissao = Number.isInteger(n) ? String(n) : n.toFixed(1);
}

function viaCepLookup(cep: string) {
    cep = cep.replace(/\D/g, ''); // Remove non-digit characters
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then((response) => response.json())
        .then((data) => {
            form.rua = data.logradouro || '';
            form.bairro = data.bairro || '';
            form.cidade = data.localidade || '';
            form.estado = data.uf || '';
        })
        .catch((error) => {
            console.error('Erro ao buscar CEP:', error);
        });
}

function enviar() {
    // console.log(form);
    // return;

    // Para iniciar o Load ao clicar em Salvar
    loading.value = true
    // --------------------------------------
    
    if (idVendedor !== null) {
        form.put(vendedores.update(idVendedor).url, {
            onSuccess: () => {
                console.log('success');
                toast.success('Vendedor alterado com sucesso!');
            },
            onError: () => {
                console.log('erro');
                toast.error('Erro ao alterar o vendedor!');
            },
            // Finalizo aqui para impedir que o Load fique mesmo se der erro as validações
            onFinish: () => {
                loading.value = false
            },
            // ---------------------------------------------------------------------------
        });
        return;
    }


    form.post(vendedores.create().url, {
        onSuccess: () => {
            console.log('success');
            toast.success('Vendedor criado com sucesso!');
        },
        onError: () => {
            console.log('erro');
            toast.error('Erro ao criar o vendedor!');
        },
        onFinish: () => {
            loading.value = false
        },
    });

}

// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import { ref } from 'vue';
const loading = ref(false);
// Fim Load visual-------------


</script>

<template>

    <Head title="Vendedor" />

    <AppLayout>
        <!-- Parte 2 Início Load visual------------- -->
        <LoadingOverlay :show="loading" />
        <!-- Parte 2 Fim Load visual------------- -->
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Heading title="Vendedores" :description="idVendedor ? 'Alterar vendedor' : 'Criar vendedor'" />
            <div class="md:grid-cols 4 grid-cols-1">
                <div
                    class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border">
                    <Form @submit.prevent="enviar">
                        
                        <Heading title="Informações pessoais" />

                        <div class="mb-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="nome">Nome</Label>
                                <Input v-model="form.nome" id="nome" type="text" autofocus :tabindex="1"
                                    autocomplete="nome" name="nome" maxlength="255" placeholder="Nome Completo" />
                                <InputError :message="form.errors.nome" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input id="email" v-model="form.email" type="text" :tabindex="2" autocomplete="email"
                                    name="email" maxlength="255" placeholder="email@exemplo.com" />
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="cpf">CPF</Label>
                                <Input v-model="form.cpf" id="cpf" type="text" :tabindex="2" autocomplete="cpf"
                                    name="cpf" maxlength="14" placeholder="000.000.000-00" v-maska="'###.###.###-##'" />
                                <InputError :message="form.errors.cpf" />
                            </div>
                        </div>
                        <Separator />
                        <Heading title="Informações adicionais" class="mt-4" />
                        <div class="mt-4 mb-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="comissao">Comissão</Label>
                                <div class="relative">
                                    <Input v-model="form.comissao" type="text" class="pr-8" @blur="onComissaoBlur"
                                        @input="onComissaoInput" id="comissao" placeholder="0.00%" />
                                    <span
                                        class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground">
                                        %
                                    </span>
                                </div>
                                <InputError :message="form.errors.comissao" />
                            </div>
                        </div>
                        <Separator />
                        <Heading title="Endereço" class="mt-4" />
                        <div class="mt-4 mb-4 grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="cep">CEP</Label>
                                <Input v-model="form.cep" v-maska="'#####-###'" id="cep" type="text" :tabindex="2" autocomplete="cep"
                                    name="cep" maxlength="9" placeholder="insira seu CEP" @blur="viaCepLookup(form.cep)" />
                                <InputError :message="form.errors.cep" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="rua">Rua</Label>
                                <Input v-model="form.rua" id="rua" type="text" :tabindex="2" autocomplete="rua"
                                    name="rua" maxlength="255" placeholder="Insira sua rua" />
                                <InputError :message="form.errors.rua" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="numero">Número</Label>
                                <Input v-model="form.numero" id="numero" type="text" :tabindex="2" autocomplete="numero"
                                    name="numero" maxlength="20" placeholder="Insira sua número" />
                                <InputError :message="form.errors.numero" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="complemento">Complemento</Label>
                                <Input v-model="form.complemento" id="complemento" type="text" :tabindex="2"
                                    autocomplete="complemento" name="complemento" maxlength="255"
                                    placeholder="Insira sua complemento" />
                                <InputError :message="form.errors.complemento" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="bairro">Bairro</Label>
                                <Input v-model="form.bairro" id="bairro" type="text" :tabindex="2" autocomplete="bairro"
                                    name="bairro" maxlength="255" placeholder="Insira sua bairro" />
                                <InputError :message="form.errors.bairro" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="cidade">Cidade</Label>
                                <Input v-model="form.cidade" id="cidade" type="text" :tabindex="2" autocomplete="cidade"
                                    name="cidade" maxlength="255" placeholder="Insira sua cidade" />
                                <InputError :message="form.errors.cidade" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="estado">Estado</Label>
                                <Input v-model="form.estado" id="estado" type="text" :tabindex="2" autocomplete="estado"
                                    name="estado" maxlength="2" placeholder="Insira sua estado" />
                                <InputError :message="form.errors.estado" />
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-5">
                            <!-- Parte 3 Início Load visual em específico @click="abrirCriacaoLoop" // Foi retirado para colocar no enviar o LOAD -->
                            <Button type="submit"
                                class="mt-2 w-full bg-yellow-500 hover:bg-yellow-600 text-black font-semibold"
                                tabindex="5" data-test="register-user-button">
                                {{ idVendedor ? 'Alterar Vendedor' : 'Criar Vendedor' }}
                            </Button>
                            <!-- Parte 3 Início Load visual em específico @click="abrirCriacaoLoop" -->
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>