<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">Filtrar Minuto</div>
                <div v-if="filter_range == null" class="col-3">
                    Desde
                    <minute-selector
                        v-model="filter_start_minute"
                    ></minute-selector>
                </div>
                <div v-if="filter_range == null" class="col-3">
                    Hasta
                    <minute-selector
                        v-model="filter_end_minute"
                    ></minute-selector>
                </div>
                <div class="col-3" v-if="filter_range == null">
                    <button
                        :disabled="
                            filter_start_minute == null ||
                            filter_end_minute == null
                        "
                        class="btn btn-secondary"
                        @click="
                            filter_range = {
                                start: filter_start_minute,
                                end: filter_end_minute,
                            }
                        "
                    >
                        Filtrar
                    </button>
                </div>
                <div v-else class="col-3">
                    <div class="btn">
                        {{ filter_range.start }} -
                        {{ filter_range.end }}
                        <button
                            class="btn btn-sm btn-danger"
                            @click.stop="filter_range = null"
                        >
                            X
                        </button>
                    </div>
                </div>
                <!--<div class="col-6">
                    <input
                        type="text"
                        placeholder="Buscar ..."
                        class="form-control"
                        name=""
                        id=""
                        v-model="search"
                    />
                </div>-->
                <div class="col-3"></div>
            </div>
        </div>
        <data-table
            :inload="inload || int_inload"
            :columns="columns"
            :items="data"
        >
            <template #opciones="{ row }">
                <button class="btn btn-info" @click="getOpenMap(row)">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </button>
            </template>
        </data-table>
        <div class="row" v-if="page_info != null">
            <div class="col-12">
                <button class="btn btn-sm btn-primary">
                    {{ page_info.current_page }}
                </button>
                <button
                    @click="
                        page_info.current_page++;
                        getDetails();
                    "
                    v-if="page_info.current_page != page_info.last_page - 1"
                    class="btn btn-sm btn-secondary"
                >
                    {{ page_info.current_page + 1 }}
                </button>
                ...
                <button
                    @click="
                        page_info.current_page = page_info.last_page;
                        getDetails();
                    "
                    class="btn btn-sm btn-secondary"
                >
                    {{ page_info.last_page }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        process_id: {
            type: Number,
            default: null,
        },
        inload: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            filter_range: null,
            int_inload: false,
            page_info: {
                current_page: 1,
            },
            search: null,
            new_ip: null,
            minute: null,
            filter_start_minute: null,
            filter_end_minute: null,
            columns: [
                { name: " ", value: "opciones" },
                { name: "SourceIP", value: "SourceIP" },
                { name: "DestinationIP", value: "DestinationIP" },
                { name: "imsi", value: "imsi" },
                { name: "sequence", value: "sequence" },
                /*{ name: "start_time", value: "start_time" },
                { name: "end_time", value: "end_time" },*/
                { name: "msisdn", value: "msisdn" },
                { name: "imei", value: "imei" },
                { name: "ip_address_assigned", value: "ip_address_assigned" },
                { name: "country_code", value: "country_code" },
                { name: "msisdn1", value: "msisdn1" },
                { name: "nombre_cliente", value: "nombre_cliente" },
                { name: "Tipo_de_cliente", value: "Tipo_de_cliente" },
                { name: "Tipo_de_Servicio", value: "Tipo_de_Servicio" },
                { name: "Dotacion", value: "Dotacion" },
                { name: "Plan", value: "Plan" },
                { name: "Ciudad_cliente", value: "Ciudad_cliente" },
                { name: "Genero", value: "Genero" },
                { name: "Ending", value: "Ending" },
                { name: "Rango_de_edad", value: "Rango_de_edad" },
            ],
            data: [],
        };
    },
    methods: {
        getOpenMap(map) {
            console.log(map);
            axios
                .get(`/custom/mapurl`, {
                    params: { ac: map.lac_tac, cell: map.sac_eci },
                })
                .then((result) => {
                    window
                        .open(
                            result.data.data,
                            "_blank",
                            "menubar=1,resizable=1,width=650,height=650"
                        )
                        .focus();
                })
                .catch((err) => {
                    console.error(err);
                });
        },
        getDetails() {
            this.int_inload = true;
            let filters = {};
            if (this.filter_range != null) {
                filters = {
                    filter_minute: {
                        start: this.filter_range.start,
                        end: this.filter_range.end,
                    },
                };
            }
            axios
                .get(`/custom/detail/index`, {
                    params: {
                        page: this.page_info.current_page,
                        Minute: this.minute,
                        ...filters,
                    },
                })
                .then(({ data }) => {
                    this.int_inload = false;
                    this.data = data.data.data;
                    this.page_info = {
                        current_page: data.data.current_page,
                        last_page: data.data.last_page,
                    };
                })
                .catch(console.error);
        },
    },
    watch: {
        minute(newValue, oldValue) {
            this.getDetails();
        },
        filter_range(newValue, oldValue) {
            console.log("filter_range: " + newValue);
            this.getDetails();
        },
    },
    mounted() {
        this.getDetails();
    },
};
</script>

<style scoped></style>
