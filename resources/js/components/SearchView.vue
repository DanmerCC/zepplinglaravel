<template>
    <div class="container">
        <div class="row">
            <button class="btn btn-womprimary" @click="newQuery = true">
                Nuevo
            </button>
        </div>
        <div class="row">
            <div class="col-12">
                <div v-if="last!=null" class="card" style="width: 100%;">
                    <!--<img src="#" class="card-img-top" alt="...">-->
                    <div class="card-body">
                        <h5 class="card-title"> {{ moment(last.day).format("YYYY-MM-DD") }}</h5>
                        <detail-custom-search
                        :inload="loading"
                                :process_id="last.id"
                            >
                        </detail-custom-search>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <data-table
                data-table-id="table-id"
                v-if="false"
                    class="principal_datatable"
                    :columns="columns"
                    :items="process"
                >
                    <template #day="{row}">
                        {{ moment(row.day).format("YYYY-MM-DD") }}
                    </template>
                    <template #textarea="{row}">
                        <input
                            class="form-control"
                            type="text"
                            name=""
                            id=""
                            placeholder="Ip publica"
                            v-model="row.ip_publica"
                        />
                        <detail-custom-search
                            :process_id="row.id"
                        >
                    </detail-custom-search>
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
                        <div class="col-12">
                            <input
                                class="form-control"
                                type="text"
                                name=""
                                id=""
                                v-model="new_ip_publica"
                            />
                        </div>
                    </div>
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
                                    :key="index"
                                >
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for=""
                                >Enviarme un correo al terminar proceso</label
                            >
                            <input
                                type="checkbox"
                                v-model="new_email_notify"
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
import moment from "moment";
export default {
    data() {
        return {
            new_email_notify:true,
            last:null,
            new_ip_publica: null,
            search: null,
            new_ip: null,
            moment,
            columns: [
                { name: "Fecha", value: "day" },
                { name: "Horas", value: "hour" },
                { name: "Horas", value: "textarea" },
            ],
            process: [],
            new_date: new Date(),
            newQuery: null,
            new_cancel: true,
            new_hora: "13",
            lastinfo:null,
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
    computed: {
        loading() {
            if(this.lastinfo == null)return true
            return this.lastinfo.state == "STARTED"
        }
    },
    methods: {
        getLastInfo(){
            axios.get(`/custom/infolast`).then((result) => {
                this.lastinfo = result.data.data;
            }).catch((err) => {
                console.error(err);
            });
        },
        getLastSearch(){
            axios.get(`custom/last`).then((result) => {
                this.last = result.data.data.data;
            }).catch((err) => {
                console.error(err);
            });
        },
        getSearchView() {
            axios
                .get(`/custom/index`)
                .then((response) => {
                    console.log(response.data.data.data);
                    this.process = response.data.data.data;
                    if(this.process.length > 0) {
                        this.last = this.process[this.process.length - 1];
                    }
                })
                .catch(console.error);
        },
        prepareData() {
            axios
                .post(`/custom/create`, {
                    ip_publica: this.new_ip_publica,
                    day: this.new_date,
                    hour: this.new_hora,
                    email_notify:this.new_email_notify
                })
                .then((response) => {
                    console.log(response.data.data);
                    if (response.data.success) {
                        this.newQuery = null;
                        this.getSearchView();
                    }
                })
                .catch((error) => {
                    console.error(error.response);
                    if (error.response.status == 503) {
                        alert("Error: " + error.response.data.message);
                    }
                    console.error(error);
                });
        },
    },
    mounted() {
        setInterval(this.getLastInfo,5000)
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

::v-deep td:nth-child(-n + 2),
th:nth-child(-n + 2) {
    width: 10% !important;
}

::v-deep tbody.table.table-responsive-sm.table-sm tr {
    height: 1000px;
}
</style>
