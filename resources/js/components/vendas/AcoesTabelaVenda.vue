<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Pencil, Trash2Icon } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import Icon from '@/components/Icon.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog';
import vendas from '@/routes/vendas';

const props = defineProps<{
    venda: Record<string, any>;
}>();

const venda = props.venda;

function removeVenda(id_venda: number) {
    router.delete(vendas.remover(id_venda).url, {
        onSuccess: () => {
            toast.success('Venda removida com sucesso!');
        },
        onError: (errors) => {
            toast.error('Erro ao remover a venda.');
            console.error(errors);
        },
    });
}

import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import { ref } from 'vue';

const loading = ref(false)

function abrirLoop() {
    loading.value = true
}


// function gerarExcel(idVenda) {
//     router.get(vendas.export(idVenda));
// }

// function gerarPdf(idVenda) {
//     router.get(vendas.exportPdf(idVenda));
// }

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();


</script>

<template>
    <LoadingOverlay :show="loading" />
    <div class="flex gap-2">
        <Link :href="venda.edit_url" @click="abrirLoop">
            <Button class="bg-yellow-400 text-black hover:bg-yellow-500">
                <Pencil class="w-4 h-4 mr-2" />
                Editar Venda
            </Button>
        </Link>
        <Dialog>
            <DialogTrigger>
                <Button class="bg-red-600 text-white hover:bg-red-700 border-none px-4">
                    <Trash2Icon class="w-4 h-4 mr-2" />
                    <span>Remover</span>
                </Button>
            </DialogTrigger>
            <DialogContent>
                <span>
                    Confirma a exclusão da venda?
                    <br />
                    <!-- <strong>{{ venda.id }}</strong> -->
                </span>
                <div class="flex justify-end gap-2">
                    <!-- Eu havia colocado assim @click.prevent="removeVendedor" @click="abrirLoop" funciona mais fica melhor se unificar -->
                    <Button class="bg-red-600 text-white hover:bg-red-700 border-none px-4"
                        @click.prevent="() => { abrirLoop(); removeVenda(venda.id) }">
                        <Trash2Icon class="w-4 h-4 mr-2" />
                        <span>Remover</span>
                    </Button>
                    <DialogClose>
                        <Button>
                            Cancelar
                        </Button>
                    </DialogClose>
                </div>
            </DialogContent>
        </Dialog>

        <a :href="vendas.export(venda.id).url" target="_blank" rel="noopener noreferrer">
            <Button class="bg-green-800 hover:bg-green-800/90">
                <Icon name="sheet" />
                Exportar em Excel
            </Button>
        </a>
        <a :href="vendas.exportPdf(venda.id).url" target="_blank" rel="noopener noreferrer">
            <Button class="bg-red-400 hover:bg-red-400/90">
                <Icon name="fileText" />
                Exportar em PDF
            </Button>
        </a>
    </div>
</template>

<style scoped></style>
