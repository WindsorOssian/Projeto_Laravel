<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import clientes from '@/routes/clientes';
import { computed } from 'vue';
import AcoesTabelaCliente from '@/components/clientes/AcoesTabelaCliente.vue';

type Cliente = Record<string, any>;

const page = usePage();
// Será atualizado automaticamente graças ao computed
const clientesList = computed<Cliente[]>(() => {
    return page.props.clientes?.data ?? [];
});

// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import { ref } from 'vue';
const loading = ref(false);
const abrirCriacaoLoop = () => {
    loading.value = true    
};
// Fim Load visual-------------
</script>

<template>

    <Head title="Cliente" />

    <AppLayout>
        <!-- Parte 2 Início Load visual------------- -->
        <LoadingOverlay :show="loading" />
        <!-- Parte 2 Fim Load visual------------- -->
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <Heading title="Clientes" description="Lista de clientes" />
            <div class="md:grid-cols 4 grid-cols-1">
                <!-- Parte 3 Início Load visual em específico @click="abrirCriacaoLoop"  :disabled="loading"  <span v-if="loading">⏳</span>  {{ loading ? 'Carregando...' : '+ Criar Novo Vendedor' }}------------- -->
                <Link :href="clientes.persistir()" @click="abrirCriacaoLoop">
                    <Button class="bg-yellow-400 text-black hover:bg-yellow-500" :disabled="loading">
                        <span v-if="loading">⏳</span>
                        {{ loading ? 'Carregando...' : '+ Criar Novo Cliente' }}
                    </Button>
                </Link>
                <!-- Parte 3 Fim Load visual em específico @click="abrirCriacaoLoop"  :disabled="loading"  <span v-if="loading">⏳</span>  {{ loading ? 'Carregando...' : '+ Criar Novo Vendedor' }}------------- -->
            </div>

            <div
                class="relative min-h-screen flex-1 rounded-xl border border-sidebar-border/70 p-4 md:min-h-min dark:border-sidebar-border">
                <Table v-if="clientesList.length > 0">
                    
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>

                        <TableRow v-for="clientes in clientesList" :key="clientes.id">
                            <TableCell> {{ clientes.user.name }} </TableCell>
                            <TableCell> {{ clientes.user.email }} </TableCell>
                            <TableCell>
                                <AcoesTabelaCliente :cliente="clientes"/>
                            </TableCell>
                        </TableRow>

                    </TableBody>
                </Table>

                 <!-- PAGINAÇÃO -->
                <div v-if="clientesList.length > 0" class="flex gap-2 mt-4">
                    <Link v-for="link in page.props.clientes.links" :key="link.label" :href="link.url ?? ''"
                        v-html="link.label.replace('Previous', 'Anterior').replace('Next', 'Próxima')" class="px-3 py-1 text-sm border rounded transition
                        hover:bg-gray-100 dark:hover:bg-gray-700" :class="{
                        'bg-yellow-400 text-black border-yellow-400': link.active,
                        'opacity-40 pointer-events-none': !link.url
                    }" />
                </div>

                <div class="flex h-full w-full flex-col items-center justify-center gap-4" v-else>
                    <Icon name="users" class="h-16 w-16 text-muted-foreground" />
                    <p class="text-center text-muted-foreground">
                        Nenhum cliente encontrado.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
