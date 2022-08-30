<template>
    <div class="container">
        <div class="row">
            <button class="btn btn-womprimary" @click="newQuery = true">
                Nuevo
            </button>
        </div>
        <div class="row">
            <div class="col-12">
                <data-table :columns="columns" :items="process">
                    <template #textarea>
                        <detail-custom-search></detail-custom-search>
                    </template>
                </data-table>
            </div>
        </div>
        <modal-component
            v-if="newQuery != null"
            @close="newQuery = null"
            title="Nuevo"
        >
            <template #body>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <input
                                class="form-control"
                                type="date"
                                name=""
                                id=""
                                v-model="new_date"
                            />
                        </div>
                        <div class="col-md-6">
                            <select
                                class="form-select"
                                v-model="new_hora"
                                name=""
                                id=""
                            >
                                <option
                                    v-for="(option, index) in option_hora"
                                    :value="option"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for=""
                                >Terminar proceso anterior</label
                            >
                            <input
                                type="checkbox"
                                v-model="new_cancel"
                                name=""
                                id=""
                            />
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button class="btn btn-primary" @click="prepareData(newQuery)">
                    Preparar data
                </button>
            </template>
        </modal-component>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            columns: [
                { name: "Fecha", value: "day" },
                { name: "Horas", value: "hour" },
                { name: "Horas", value: "textarea" },
            ],
            process: [],
            new_date: null,
            newQuery: null,
            new_cancel: true,
            new_hora: "13",
            /** 24 hours */
            option_hora: [
                "01",
                "02",
                "03",
                "04",
                "05",
                "06",
                "07",
                "08",
                "09",
                "10",
                "11",
                "12",
                "13",
                "14",
                "15",
                "16",
                "17",
                "18",
                "19",
                "20",
                "21",
                "22",
                "23",
                "24",
            ],
        };
    },
    methods: {
        getSearchView() {
            axios
                .get(`/custom/index`)
                .then((response) => {
                    console.log(response.data.data.data);
                    this.process = response.data.data.data;
                })
                .catch(console.error);
        },
        prepareData() {
            axios
                .post(`/custom/create`, {
                    day: this.new_date,
                    hour: this.new_hora,
                })
                .then((response) => {
                    console.log(response.data.data);
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
    mounted() {
        this.getSearchView();
    },
};
</script>

<style scoped>
::v-deep .btn-womprimary,
.btn.btn-primary {
    background-color: #612d8a !important;
    color: white !important;
    border-color: #9d6fc1 !important;
}
</style>
