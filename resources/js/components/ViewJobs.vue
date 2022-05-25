<template>
<div class="container">
<div class="row">
    <button class="btn btn-primary" @click="newModal = true">Nuevo</button>
</div>
    <div class="row">
        <data-table :columns="columns" :items="items"></data-table>
    </div>
    <modal-component v-if="newModal" @close="newModal = false;new_process_date = null" title="Nuevo Proceso">
        <template #body>
            <div>
            <label for="new_process_date">Fecha</label>
                 <input v-model="new_process_date" type="date" name="new_process_date" id="new_process_date" placeholder="Fecha">
            </div>
        </template>
        <template #footer>
            <button class="btn btn-primary" @click="iniciarDescompresion()">
                Iniciar
            </button>
        </template>
    </modal-component>
</div>
</template>

<script>
export default {
    data() {
        return {
            new_process_date:null,
            newModal:false,
            columns: [{name:'',value:''}],
            items:[]
        }
    },
    methods: {
        iniciarDescompresion() {
            let data = {}
            axios.post(`/descomprimir`,data)
            .then((response)=>{
                console.log(response)
            })
            .catch(error=>console.error(error))
        }
    },
}
</script>

<style>

</style>
