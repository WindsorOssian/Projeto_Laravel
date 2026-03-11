<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import vendas from '@/routes/vendas';
import { Venda } from '@/pages/vendas/Persistir.vue';
import Icon from '@/components/Icon.vue';
import { Helper } from '@/utils/Helper';
import AcoesTabelaVenda from '@/components/vendas/AcoesTabelaVenda.vue';
import { router } from '@inertiajs/vue3'

type Vendas = Record<string, any>;

const page = usePage();

const vendasList = computed<Vendas[]>(() => {
    return page.props.vendas?.data ?? [];
});

const vendedoresList = computed<Record<string, any>[]>(() => {
    return page.props.vendedores ?? [];
});

const clietnesList = computed<Record<string, any>[]>(() => {
    return page.props.clientes ?? [];
});

const filtroVendedor = ref<number | null>(null);
const filtroCliente = ref<number | null>(null);

// const vendasFiltradas = computed(() => {
//     return vendasList.value.filter((v) => {
//         console.log('VENDA:', v);
//         const matchVendedor =
//             !filtroVendedor.value || v.id_vendedor === filtroVendedor.value;

//         const matchCliente =
//             !filtroCliente.value || v.cliente?.id === filtroCliente.value;

//         return matchVendedor && matchCliente;
//     });
// });

const labelVendedorSelecionado = computed(() => {
    if (!filtroVendedor.value) {
        return 'Todos os vendedores';
    }

    const vendedor = vendedoresList.value.find(
        (v) => v.id_vendedor === filtroVendedor.value,
    );

    return vendedor?.user?.name ?? 'Todos os vendedores';
});

//O Select do shadcn só consegue mostrar automaticamente o valor
// quando o value do SelectItem é string
const labelClienteSelecionado = computed(() => {
    if (!filtroCliente.value) {
        return 'Todos os clientes';
    }

    const cliente = clietnesList.value.find(
        (c) => c.id === filtroCliente.value,
    );

    return cliente?.nome ?? 'Todos os clientes';
});

// function sumValor(itens?: Venda['itens']): number {
//     return (itens ?? []).reduce((s, i) => s + (Number(i?.valor) || 0), 0);
// }

function sumValor(itens?: Venda['itens']): number {
    return (itens ?? []).reduce((s, i) => {
        const valor = Number(i?.valor) || 0;
        const quantidade = Number(i?.quantidade) || 1;
        return s + valor * quantidade;
    }, 0);
}

function obterValorComissao(valorVenda: number, comissao: number): number {
    return (valorVenda * comissao) / 100;
}

function aplicarFiltro() {
    router.get(
        vendas.listar(),
        {
            id_vendedor: filtroVendedor.value,
            id_cliente: filtroCliente.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}



// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
const loading = ref(false);
const abrirCriacaoLoop = () => {
    loading.value = true
};
// Fim Load visual-------------

</script>

<template>

    <Head title="Venda" />

    <AppLayout>
        <!-- Parte 2 Início Load visual------------- -->
        <LoadingOverlay :show="loading" />
        <!-- Parte 2 Fim Load visual------------- -->
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Heading title="Venda" description="Lista de vendas" />
            <div class="md:grid-cols 4 grid-cols-1">
                <Link :href="vendas.persistir()" @click="abrirCriacaoLoop">
                    <Button class="bg-yellow-400 text-black hover:bg-yellow-500" :disabled="loading">
                        <span v-if="loading">⏳</span>
                        {{ loading ? 'Carregando...' : '+ Realizar Venda' }}
                    </Button>
                </Link>
            </div>
            <!-- Filtro Vendedor -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="flex flex-col gap-1">
                    <span class="text-sm text-muted-foreground">Vendedor</span>
                    <Select class="w-full" v-model="filtroVendedor" @update:modelValue="aplicarFiltro">
                        <SelectTrigger class="h-10 w-full">
                            <SelectValue>
                                {{ labelVendedorSelecionado }}
                            </SelectValue>
                        </SelectTrigger>

                        <SelectContent>
                            <SelectItem :value="null">
                                Todos os vendedores
                            </SelectItem>

                            <SelectItem v-for="v in vendedoresList" :key="v.id_vendedor" :value="v.id_vendedor">
                                {{ v.user?.name ?? '—' }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Filtro Cliente -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm text-muted-foreground">Cliente</span>
                    <Select class="w-full" v-model="filtroCliente" @update:modelValue="aplicarFiltro">
                        <SelectTrigger class="h-10 w-full">
                            <SelectValue>
                                {{ labelClienteSelecionado }}
                            </SelectValue>
                        </SelectTrigger>

                        <SelectContent>
                            <SelectItem :value="null">
                                Todos os clientes
                            </SelectItem>

                            <SelectItem v-for="c in clietnesList" :key="c.id" :value="c.id">
                                {{ c.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border">
                <Table v-if="vendasList.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Cliente</TableHead>
                            <TableHead>Vendedor</TableHead>
                            <TableHead>Valor da Venda</TableHead>
                            <TableHead>Comissao Vendedor</TableHead>
                            <TableHead>Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="vendas in vendasList" :key="vendas.id">
                            <TableCell> {{ vendas.cliente.nome ?? '—' }} </TableCell>
                            <TableCell>
                                {{ vendas.vendedor_nome }}
                            </TableCell>
                            <TableCell>
                                R$
                                {{
                                    Helper.formatarValorMonetarioPtBr(
                                        sumValor(vendas.itens),
                                    )
                                }}
                            </TableCell>
                            <TableCell>
                                R$
                                {{
                                    Helper.formatarValorMonetarioPtBr(
                                        obterValorComissao(
                                            sumValor(vendas.itens),
                                            vendas.vendedor?.comissao ?? 0,
                                        ),
                                    )
                                }}
                            </TableCell>

                            <TableCell>
                                <AcoesTabelaVenda :venda="vendas" />
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <div v-if="vendasList.length > 0" class="flex gap-2 mt-4">
                    <Link v-for="link in page.props.vendas.links" :key="link.label" :href="link.url ?? ''"
                        v-html="link.label.replace('Previous', 'Anterior').replace('Next', 'Próxima')" class="px-3 py-1 text-sm border rounded transition
                        hover:bg-gray-100 dark:hover:bg-gray-700" :class="{
                        'bg-yellow-400 text-black border-yellow-400': link.active,
                        'opacity-40 pointer-events-none': !link.url
                    }" />
                </div>

                <div v-else class="flex h-full w-full flex-col items-center justify-center gap-4">
                    <Icon name="shoppingCart" class="h-16 w-16 text-muted-foreground" />
                    <p class="text-center text-muted-foreground">
                        Nenhuma venda encontrada.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
