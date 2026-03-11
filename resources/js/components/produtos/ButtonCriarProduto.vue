<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, toRef } from 'vue';
import { toast } from 'vue-sonner';

import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import { Money3Directive } from 'v-money3'

// registra diretiva
const vMoney3 = Money3Directive

// configuração da máscara
const money = {
    decimal: '.',
    thousands: '',
    precision: 2,
    masked: false,
    max: 99999999.99
}

export interface Produto {
    id?: number;
    nome: string;
    preco: string;
}

const props = defineProps<{
    produto?: Produto | null;
}>();

// Mantém reatividade do prop `produto`
const produto = toRef(props, 'produto');

// 🔑 controla abertura do modal
const open = ref(false);

// inicializa o form com o nome do produto (se existir)
const form = useForm({
    nome: produto.value?.nome ?? '',
    preco: produto.value?.preco ?? '',
});


import produtos from '@/routes/produtos';

function submit() {
    loading.value = true

    if (!produto.value) {
        form.post(produtos.create().url, {
            onSuccess: () => {
                form.reset()
                open.value = false
                toast.success('Produto criado com sucesso!');
            },
            onError: () => {
                console.log('Erro ao criar o produto!');
                toast.error('Erro ao criar o produto!');
            },
            onFinish: () => {
                loading.value = false
            }
        })
    } else {
        form.put(produtos.update(produto.value.id!).url, {
            onSuccess: () => {
                open.value = false
                toast.success('Produto alterado com sucesso!');
            },
            onError: () => {
                console.log('Erro ao alterar o produto!');
                toast.error('Erro ao alterar o produto!');
            },
            onFinish: () => {
                loading.value = false
            }
        })
    }
}

import { Pencil, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';
const isEdit = computed(() => !!produto.value);

import { watch } from 'vue';

watch(produto, (novoProduto) => {
    form.nome = novoProduto?.nome ?? '';
    form.preco = novoProduto?.preco ?? '';
});

// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';

// import { ref } from 'vue'; já tem um ref

const loading = ref(false)

// Início Load visual-------------

</script>

<template>
    <Dialog v-model:open="open">
        <LoadingOverlay :show="loading" />
        <DialogTrigger>
            <Button :class="isEdit
                ? 'bg-yellow-400 text-black hover:bg-yellow-500'
                : 'bg-yellow-400 text-black hover:bg-yellow-500'" class="flex items-center gap-2">
                <template v-if="isEdit">
                    <Pencil class="w-4 h-4" />
                    <span>Editar</span>
                </template>

                <template v-else>
                    <span>+ Criar Novo Produto</span>
                </template>
            </Button>
        </DialogTrigger>
        <DialogContent>

            <form @submit.prevent="submit">
                <div class="grid-cols-1 md:grid-cols-2">
                    <Heading :title="!produto ? 'Novo produto' : 'Editar Produto'" />
                    <div class="grid-cols-1 md:grid-cols-2">
                        <div class="mb-4 grid gap-2">
                            <Label for="nome">Nome</Label>
                            <Input v-model="form.nome" id="nome" type="text" :tabindex="1" autocomplete="nome"
                                name="nome" placeholder="Nome produto" maxlength="255" />
                            <InputError :message="form.errors.nome" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="preco">Preço</Label>
                            <Input v-model="form.preco" id="preco" type="text" step="0.01" min="0" max="99999999.99"
                                maxlength="11" placeholder="0.00" />

                            <InputError :message="form.errors.preco" />
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <Button type="submit" class="mt-2 bg-green-400 hover:bg-green-400/90" tabindex="5"
                                data-test="register-user-button">
                                <span v-if="isEdit">Salvar</span>
                                <span v-else>Criar</span>
                            </Button>
                            <DialogClose as-child>
                                <Button type="button" class="mt-2" tabindex="5" data-test="register-user-button">
                                    Cancelar
                                </Button>
                            </DialogClose>
                        </div>
                    </div>
                </div>
            </form>
        </DialogContent>
    </Dialog>
</template>

<style scoped></style>