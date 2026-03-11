<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import DialogClose from '@/components/ui/dialog/DialogClose.vue';
import { Trash2 } from 'lucide-vue-next';
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import { ref } from 'vue';
import produtos from '@/routes/produtos';
import { toast } from 'vue-sonner';

const loading = ref(false)

const props = defineProps<{
    produto: {
        id: number
        nome: string
        preco: number
    }
}>()

const produto = props.produto

function removerProduto() {
    router.delete(produtos.remove(produto.id), {
        onSuccess: () => {
            toast.success('Produto removido com sucesso')
        },
        onError: (errors) => {
            console.error(errors);
            toast.error('Erro ao remover produto')
        },
        onFinish: () => {
            loading.value = false
        },
    });
}


function abrirLoop() {
    loading.value = true
}
</script>

<template>
    <LoadingOverlay :show="loading" />

    <div class="flex gap-2">
        <!-- EDITAR -->
        <ButtonCriarProduto :produto="produto" />

        <!-- REMOVER -->
        <Dialog>
            <DialogTrigger>
                <Button class="bg-red-600 text-white hover:bg-red-700 border-none px-4">
                    <Trash2 class="w-4 h-4 mr-2" />
                    Remover
                </Button>
            </DialogTrigger>

            <DialogContent>
                <span>
                    Tem certeza que deseja remover o produto:
                    <br />
                    <strong>{{ produto.nome }}</strong>
                </span>

                <div class="flex justify-end gap-2 mt-4">
                    <Button
                        class="bg-red-600 text-white hover:bg-red-700 border-none px-4"
                        @click.prevent="() => { abrirLoop(); removerProduto() }"
                    >
                        <Trash2 class="w-4 h-4 mr-2" />
                        Confirmar
                    </Button>

                    <DialogClose>
                        <Button>Cancelar</Button>
                    </DialogClose>
                </div>
            </DialogContent>
        </Dialog>
    </div>
</template>