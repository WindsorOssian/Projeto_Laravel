<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { dashboard } from '@/routes'
import { ShoppingCart, UserPlus, Package, UserCog } from 'lucide-vue-next'
import { type BreadcrumbItem } from '@/types'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Menu Principal',
        href: dashboard().url,
    }
]

const page = usePage()
// Verifica se é vendedor
const isVendedor = page.props.auth.user.parent_user_id !== null

const props = defineProps({
    vendasHoje: Number,
    vendasMes: Number,
    clientes: Number,
    produtos: Number,
    vendedores: Number,
    ultimasVendas: Array
})
</script>

<template>

    <Head title="Bem-vindo" />

    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6 space-y-6">

            <div class="text-2xl font-semibold">
                Bem-vindo, {{ page.props.auth.user.name }}
            </div>

            <!-- <pre>{{ page.props.auth.user }}</pre> -->

            <!-- CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-1 md:grid-cols-5 gap-4">

                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow">
                    <div class="text-sm text-gray-500">Vendas hoje</div>
                    <div class="text-2xl font-bold">
                        {{ vendasHoje }}
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow">
                    <div class="text-sm text-gray-500">Vendas no mês</div>
                    <div class="text-2xl font-bold">
                        {{ vendasMes }}
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow">
                    <div class="text-sm text-gray-500">Clientes</div>
                    <div class="text-2xl font-bold">
                        {{ clientes }}
                    </div>
                </div>

                <div v-if="!isVendedor" class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow">
                    <div class="text-sm text-gray-500">Produtos</div>
                    <div class="text-2xl font-bold">
                        {{ produtos }}
                    </div>
                </div>

                <div v-if="!isVendedor" class="bg-white dark:bg-zinc-900 p-4 rounded-xl shadow">
                    <div class="text-sm text-gray-500">Vendedores</div>
                    <div class="text-2xl font-bold">
                        {{ vendedores }}
                    </div>
                </div>

            </div>

            <!-- AÇÕES RÁPIDAS -->
            <div class="flex gap-4 flex-wrap">

                <Link href="/vendas/persistir"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 shadow-sm hover:-translate-y-0.5 transition flex items-center gap-2">
                    <ShoppingCart class="w-4 h-4 inline mr-2" />
                    Nova venda
                </Link>

                <Link v-if="!isVendedor" href="/clientes/persistir"
                    class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 shadow-sm hover:-translate-y-0.5 transition flex items-center gap-2">
                    <UserPlus class="w-4 h-4 inline mr-2" />
                    Novo cliente
                </Link>

                <Link v-if="!isVendedor" href="/produtos"
                    class="bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700 shadow-sm hover:-translate-y-0.5 transition flex items-center gap-2">
                    <Package class="w-4 h-4 inline mr-2" />
                    Produtos
                </Link>

                <Link v-if="!isVendedor" href="/vendedores/persistir"
                    class="bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 shadow-sm hover:-translate-y-0.5 transition flex items-center gap-2">
                    <UserCog class="w-4 h-4 inline mr-2" />
                    Novo vendedor
                </Link>

            </div>

            <!-- ÚLTIMAS VENDAS -->

            <!-- <pre>{{ ultimasVendas }}</pre> -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow">

                <div class="p-4 border-b font-semibold">
                    Últimas vendas
                </div>

                <table class="w-full text-sm">

                    <thead class="bg-gray-100 dark:bg-zinc-800">
                        <tr>
                            <th class="p-3 text-left">Pedido</th>
                            <th class="p-3 text-left">Cliente</th>
                            <th class="p-3 text-left">Vendedor</th>
                            <th class="p-3 text-left">Data</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr v-for="venda in ultimasVendas" :key="venda.id" class="border-t">

                            <td class="p-3">
                                Venda #{{ venda.id }}
                            </td>

                            <td class="p-3">
                                {{ venda.cliente?.nome }}
                            </td>

                            <td class="p-3">
                                <span v-if="venda.vendedor">
                                    {{ venda.vendedor.user?.name }}
                                </span>

                                <span v-else-if="venda.admin">
                                    {{ venda.admin.name }}
                                </span>

                                <span v-else>
                                    —
                                </span>
                            </td>

                            <td class="p-3">
                                {{ new Date(venda.created_at).toLocaleString() }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </AppLayout>
</template>