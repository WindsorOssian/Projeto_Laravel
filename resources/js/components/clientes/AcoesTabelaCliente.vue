<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogTrigger } from '@/components/ui/dialog';
import clientes from '@/routes/clientes';
import { Pencil, Trash2Icon } from 'lucide-vue-next';
// import DialogContent from '../ui/dialog/DialogContent.vue';
import DialogClose from '../ui/dialog/DialogClose.vue';
// Início Load visual-------------
import LoadingOverlay from '@/components/ui/LoadingOverlay.vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const loading = ref(false)

function abrirLoop() {
    loading.value = true
}
// Fim Load visual-------------

const props = defineProps<{
    cliente: Record<string, any>;
}>();

const cliente = props.cliente;

function removeCliente(){
    router.delete(clientes.remove(cliente.id), {
        onSuccess: () => {
            toast.success('Cliente removido com sucesso')
        },
        onError: (errors) => {
            console.error(errors);
            toast.error('Erro ao remover cliente')
        },
        onFinish: () => {
            loading.value = false
        },
    });
}


</script>

<template>
    <!-- Parte 2 Início Load visual------------- -->
    <LoadingOverlay :show="loading" />
    <!-- Parte 2 Fim Load visual------------- -->
    <div class="flex gap-2">
        <!-- Parte 3 Início do Load visual no caso @click="abrirLoop"-->
        <Link :href="cliente.edit_url" @click="abrirLoop">
            <Button class="bg-yellow-400 text-black hover:bg-yellow-500">
                <Pencil class="w-4 h-4 mr-2" />
                <span>Editar</span>
            </Button>
        </Link>
        <!-- Parte 3 Fim do Load visual -->
        <Dialog>
            <DialogTrigger>
                <Button class="bg-red-600 text-white hover:bg-red-700 border-none px-4">
                    <Trash2Icon class="w-4 h-4 mr-2" />
                    <span>Remover</span>
                </Button>
            </DialogTrigger>
            <DialogContent>
                <span>
                    Tem certeza que deseja remover o cliente:
                    <br />
                    <strong>{{ cliente.user.name }}</strong>
                </span>
                <div class="flex justify-end gap-2">
                    <!-- Eu havia colocado assim @click.prevent="removeVendedor" @click="abrirLoop" funciona mais fica melhor se unificar -->
                    <Button class="bg-red-600 text-white hover:bg-red-700 border-none px-4" @click.prevent="() => { abrirLoop(); removeCliente(); }">
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
    </div>
</template>

<style scoped></style>