<script setup lang="ts">
import { Form, Head, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';

import DatePicker from '@/components/DatePicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
// import AcoesTabelaItens from '@/components/Venda/AcoesTabelaItens.vue';
// import ButtonAdicionarItem from '@/components/Venda/ButtonAdicionarItem.vue';
import AcoesTabelaItens from '@/components/vendas/AcoesTabelaItens.vue';
import ButtonAdicionarItem from '@/components/vendas/ButtonAdicionarItem.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import vendas from '@/routes/vendas';
import { Helper } from '@/utils/Helper';
import Icon from '@/components/Icon.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
const dialogConfirmar = ref(false);

function confirmarEnvio() {
    dialogConfirmar.value = true
}


const page = usePage();
const props = page.props as unknown as {
    idVenda: number | null;
    venda?: Record<string, any>;
    clientes?: Record<string, any>[];
    produtos?: Record<string, any>[];
};

const tituloPagina = props.idVenda ? 'Editar Venda' : 'Realizar nova Venda';

const venda = props.venda;

// quando monta a tela verifica se o id do vendedor é válido
onMounted(() => {
    if (props.idVenda != null && !venda) {
        router.visit(vendas.listar().url);
        // toast.error('Vendedor não encontrado.');
    }
});

export interface Venda {
    data_venda: string;
    id_cliente: number;
    itens?: Record<string, any>[];
}

const today = new Date().toLocaleDateString('sv-SE');

const form = useForm<Venda>({
    id: venda?.id ?? undefined,
    data_venda: venda?.data_formatada ?? today,
    id_cliente: venda?.id_cliente ?? 0,
    itens: venda?.itens ?? [],
});

function getItemNome(item: any): string {
    return item.produto?.nome ?? item.nome ?? '—';
}

function onAdicionarItem(localItem: any) {

    if (itens.value.length >= 100) {
        toast.error('Limite máximo de 100 itens por venda.');
        return;
    }

    const produtoId = localItem.id_produto;

    const itemExistente = itens.value.find(
        (item) => item.id_produto === produtoId
    );

    if (itemExistente) {

        const novaQuantidade =
            Number(itemExistente.quantidade) + Number(localItem.quantidade);

        // valida limite 99
        if (novaQuantidade > 99) {
            toast.error('Quantidade máxima por produto é 99!');
            return;
        }

        itemExistente.quantidade = novaQuantidade;

        toast.info('Produto já existente. Quantidade atualizada.');

    } else {

        if (Number(localItem.quantidade) > 99) {
            toast.error('Quantidade máxima por produto é 99.');
            return;
        }

        localItem.nome = localItem.produto?.nome ?? localItem.nome ?? '';

        itens.value.push(localItem);
    }

    syncItens();
}

function onRemoverItemLocal(id_item: string | number) {
    itens.value = itens.value.filter(
        (i) => String(i.id_item) !== String(id_item),
    );

    syncItens();
}
const clientesOptions = props?.clientes?.map((cliente) => ({
    id: cliente.id,
    label: cliente.nome,
    value: cliente.id,
}));

function onItemRemovido(id_item: any) {
    const idStr = String(id_item ?? '');
    const newItens = (form.itens ?? []).filter((i: any) => {
        return String(i.id ?? i.id_item ?? '') !== idStr;
    });

    // atualiza o ref usado pela tabela (substitui a referência para forçar reatividade)
    itens.value = [...newItens];

    // mantém o useForm do Inertia sincronizado
    if (typeof (form as any).setData === 'function') {
        (form as any).setData('itens', newItens);
    } else {
        form.itens = newItens;
    }
}

const itens = ref<any[]>(venda?.itens ?? []);

function syncItens() {
    form.itens = [...itens.value];
}

const produtosList = props.produtos;

function submit() {

    // if (!confirm('Verifique se todos os itens e quantidades estão corretos antes de salvar.')) {
    //     return;
    // }

    // Para iniciar o Load ao clicar em Salvar
    loading.value = true

    if (props?.venda?.id) {
        form.put(vendas.update(venda?.id).url, {
            onSuccess: () => {
                toast.success('Venda atualizada com sucesso!');
            },
            onError: () => {
                toast.error('Erro ao atualizar a venda.');
                console.log(form.errors);
            },
            // Finalizo aqui para impedir que o Load fique mesmo se der erro as validações
            onFinish: () => {
                loading.value = false
            },
        });
        return;
    }
    form.post(vendas.create().url, {
        onSuccess: () => {
            console.log('aqui');
            toast.success('Venda criada com sucesso!');
        },
        onError: () => {
            toast.error('Erro ao criar a venda.');
            console.log('erro');
        },
        onFinish: () => {
            loading.value = false
        },
    });
}

// console.log('VENDA:', venda);
// console.log('DATA:', venda?.data);
// console.log('TIPO:', typeof venda?.data);
function validarData(data: string) {
    if (data > today) {
        form.data_venda = today;

        toast.error('Não é permitido selecionar datas futuras.');
    }
}

import { computed } from 'vue';

const paginaAtual = ref(1);
const itensPorPagina = 10;

const itensPaginados = computed(() => {
    const inicio = (paginaAtual.value - 1) * itensPorPagina;
    const fim = inicio + itensPorPagina;
    return itens.value.slice(inicio, fim);
});

const totalPaginas = computed(() => {
    return Math.ceil(itens.value.length / itensPorPagina);
});

// function adicionarItem() {

//     if (itens.value.length >= 15) {
//         toast.error('Limite máximo de 15 itens por venda.');
//         return;
//     }

//     itens.value.push({
//         id_item: Date.now(),
//         produto_id: produtoSelecionado.value.id,
//         nome: produtoSelecionado.value.nome,
//         quantidade: quantidade.value,
//         preco: preco.value,
//     });
// }

// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
const loading = ref(false);
// Fim Load visual-------------

</script>

<template>

    <Head title="Venda" />

    <AppLayout>
        <!-- Parte 2 Início Load visual------------- -->
        <LoadingOverlay :show="loading" />
        <!-- Parte 2 Fim Load visual------------- -->
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Heading title="Vendas" :description="tituloPagina" />
            <div class="md:grid-cols 4 grid-cols-1">
                <div
                    class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border">
                    <form @submit.prevent="confirmarEnvio">
                        <Heading title="Informações pessoais" />

                        <div class="mb-4 grid grid-cols-1 gap-6 md:grid-cols-4">
                            <div>
                                <DatePicker v-model="form.data_venda" label="Data da venda"
                                    placeholder="Selecione a data" :max="today" @update:model-value="validarData" />
                                <InputError :message="form.errors.data_venda" />
                            </div>

                            <div class="md:col-span-3">
                                <Select v-model="form.id_cliente">
                                    <SelectTrigger class="w-full">
                                        <SelectValue placeholder="Selecione um cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectLabel>Clientes</SelectLabel>
                                        <SelectGroup>
                                            <SelectItem v-for="item in clientesOptions" :key="item.id" :value="item.id">
                                                {{ item.label }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.id_cliente" />
                            </div>
                        </div>
                        <Separator />
                        <Heading title="Itens da Venda" class="mt-4" />
                        <!-- Aqui você pode adicionar campos para os itens da venda -->
                        <div class="mb-4 grid grid-cols-1 gap-6 md:grid-cols-4">
                            <ButtonAdicionarItem :produto="produtosList" @adicionar-item="onAdicionarItem" />
                        </div>

                        <Table v-if="itens?.length > 0">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nome</TableHead>
                                    <TableHead>Quantidade</TableHead>
                                    <TableHead>Valor</TableHead>

                                    <TableHead>Ações</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in itensPaginados" :key="item.id_item">
                                    <TableCell>
                                        {{ getItemNome(item) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ item.quantidade ?? '—' }}
                                    </TableCell>
                                    <TableCell>
                                        R$
                                        {{
                                            Helper.formatarValorMonetarioPtBr(
                                                item.valor,
                                            ) ?? '—'
                                        }}
                                    </TableCell>

                                    <TableCell>
                                        <AcoesTabelaItens :item="item" @remover-item-local="
                                            onRemoverItemLocal
                                        " @item-removido="onItemRemovido" />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                        <div v-if="itens.length > 0" class="text-sm text-muted-foreground mt-2">
                            Mostrando {{ itensPaginados.length }} de {{ itens.length }} itens
                        </div>

                        <div v-if="itens.length > 0 && totalPaginas > 1" class="flex gap-2 mt-4">
                            <Button variant="outline" :disabled="paginaAtual === 1" @click="paginaAtual--">
                                Anterior
                            </Button>

                            <span class="flex items-center px-2">
                                Página {{ paginaAtual }} de {{ totalPaginas }}
                            </span>

                            <Button variant="outline" :disabled="paginaAtual === totalPaginas" @click="paginaAtual++">
                                Próxima
                            </Button>
                        </div>

                        <div v-if="itens.length === 0"
                            class="flex h-full w-full flex-col items-center justify-center gap-4">
                            <p class="text-center text-lg font-medium text-muted-foreground">
                                Nenhum item adicionado.
                            </p>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-5">
                            <Button type="submit" class="bg-yellow-400 text-black hover:bg-yellow-500" tabindex="5"
                                data-test="register-user-button">
                                <Icon name="plus" />
                                <span v-if="!props.idVenda">
                                    Salvar Venda
                                </span>
                                <span v-else> Editar Venda </span>
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <Dialog v-model:open="dialogConfirmar">
            <DialogContent>

                <DialogHeader>
                    <DialogTitle>⚠ Confirmar venda</DialogTitle>
                    <DialogDescription>
                        Verifique se todos os itens e quantidades estão corretos antes de salvar.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="flex gap-2 justify-end">
                    <Button variant="outline" @click="dialogConfirmar = false">
                        Cancelar
                    </Button>

                    <Button class="bg-yellow-400 text-black hover:bg-yellow-500" @click="submit">
                        Confirmar
                    </Button>
                </DialogFooter>

            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<style scoped></style>
