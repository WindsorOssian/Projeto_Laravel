<script setup lang="ts">
import { Head, usePage, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
// import { Button } from '@/components/ui/button'; não precisa mais
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

// Para criar o produto e editar
import ButtonCriarProduto from '@/components/produtos/ButtonCriarProduto.vue';
// Para criar o produto e editar

// Precisa do import { usePage } from '@inertiajs/vue3' e import { computed } from 'vue'; para fazer isso de atualizar a lista abaixo
import { computed } from 'vue';

interface Produto {
    id: number
    nome: string
    preco: number
}

const produtosList = computed<Produto[]>(() => {
    return page.props.produtos?.data ?? []
})

const page = usePage()


// Para adicionar a modal com o nome para remover
import AcoesProduto from '@/components/produtos/AcoesProduto.vue'
// Para adicionar a modal com o nome para remover

</script>

<template>

    <Head title="Produtos" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Heading title="Produtos" description="Lista de produtos" />

            <div class="md:grid-cols 4 grid-cols-1">
                <!-- Botão modal para criar produto -->
                <ButtonCriarProduto />
            </div>

            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border">
                <Table v-if="produtosList.length > 0">
                    <!-- <Table v-if="vendedoresList.length > 0"> -->
                    <TableHeader>
                        <TableRow>
                            <TableHead> Nome </TableHead>
                            <TableHead> Ações </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>

                        <TableRow v-for="produto in produtosList" :key="produto.id">
                            <TableCell> {{ produto.nome }} </TableCell>
                            <TableCell>
                                <div class="flex items-center gap-2">

                                    <!-- EDITAR -->
                                    <ButtonCriarProduto :produto="produto" />
                                    <!-- Remover-->
                                    <AcoesProduto :produto="produto" />

                                </div>
                            </TableCell>

                        </TableRow>

                    </TableBody>
                </Table>

                <!-- PAGINAÇÃO -->
                <div v-if="produtosList.length > 0" class="flex gap-2 mt-4">

                    <Link v-for="link in page.props.produtos.links" :key="link.label" :href="link.url ?? ''"
                        v-html="link.label.replace('Previous', 'Anterior').replace('Next', 'Próxima')" class="px-3 py-1 text-sm border rounded transition
                        hover:bg-gray-100 dark:hover:bg-gray-700" :class="{
                            'bg-yellow-400 text-black border-yellow-400': link.active,
                            'opacity-40 pointer-events-none': !link.url
                        }" />

                </div>

                <div class="flex h-full w-full flex-col items-center justify-center gap-4" v-else>
                    <Icon name="users" class="h-16 w-16 text-muted-foreground" />
                    <p class="text-center text-muted-foreground">
                        Nenhum produto encontrado.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
