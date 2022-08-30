<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <input
                        type="text"
                        placeholder="Buscar ..."
                        class="form-control"
                        name=""
                        id=""
                        v-model="search"
                    />
                </div>
                <div class="col-12"></div>
            </div>
        </div>
        <data-table :columns="columns" :items="data"></data-table>
    </div>
</template>

<script>
export default {
    data() {
        return {
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
                .get(`/custom/detail/index`)
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

<style></style>
