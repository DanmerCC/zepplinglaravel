<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <input
                        class="form-control"
                        type="text"
                        name=""
                        id=""
                        placeholder="Ip publica"
                        v-model="new_ip"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-3">
                    <hour-selector v-model="hour"></hour-selector>
                </div>
                <div class="col-6">
                    <input
                        type="text"
                        placeholder="Buscar ..."
                        class="form-control"
                        name=""
                        id=""
                        v-model="search"
                    />
                </div>
                <div class="col-3"></div>
            </div>
        </div>
        <data-table :columns="columns" :items="data"></data-table>
    </div>
</template>

<script>
export default {
    props: {
        process_id: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            hour: null,
            columns: [
                { name: "CLiente", value: "nombre_cliente" },
                { name: "Hora", value: "Hora" },
                { name: "Imsi", value: "imsi" },
                { name: "Ip privada", value: "SourceIP" },
            ],
            data: [],
        };
    },
    methods: {
        getDetails() {
            axios
                .get(`/custom/detail/index/${this.process_id}`)
                .then(({ data }) => {
                    this.data = data.data;
                })
                .catch(console.error);
        },
    },
    mounted() {
        this.getDetails();
    },
};
</script>

<style scoped>
::v-deep tr:first-child,
th:first-child {
    width: 10% !important;
    border: black 1px solid;
}
</style>
