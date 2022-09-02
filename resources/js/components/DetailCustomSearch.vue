<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    Filtrar Minuto
                </div>
                <div class="col-3">
                    <minute-selector v-model="minute"></minute-selector>
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
        <data-table :inload="inload" :columns="columns" :items="data">
            <template #opciones="{row}">
                <button class="btn btn-info" @click="getOpenMap(row)">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </button>
            </template>
        </data-table>
        <div class="row" v-if="page_info !=null">
            <div class="col-12">
                <button class="btn btn-sm btn-primary"> {{page_info.current_page}}</button>
                <button @click="page_info.current_page++;getDetails()" v-if="page_info.current_page != page_info.last_page - 1" class="btn btn-sm btn-secondary"> {{(page_info.current_page + 1)}}</button>
                ...
                <button @click="page_info.current_page = page_info.last_page;getDetails()" class="btn btn-sm btn-secondary"> {{page_info.last_page}}</button>
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
    },
    data() {
        return {
            inload:false,
            page_info:{
                current_page: 1,
            },
            search: null,
            new_ip: null,
            minute: null,
            columns: [
                { name: " ", value: "opciones" },
                { name: "SourceIP", value: "SourceIP" },
                { name: "DestinationIP", value: "DestinationIP" },
                { name: "imsi", value: "imsi" },
                { name: "sequence", value: "sequence" },
                { name: "start_time", value: "start_time" },
                { name: "end_time", value: "end_time" },
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
        getOpenMap(map){
            console.log(map)
            axios.get(`/custom/mapurl`,{params:{ac:map.lac_tac,cell:map.sac_eci}}).then((result) => {
                window.open(result.data.data, '_blank',"menubar=1,resizable=1,width=650,height=650").focus();
            }).catch((err) => {
                console.error(err);
            });
        },
        getDetails() {
            this.inload = true;
            axios
                .get(`/custom/detail/index`,{params:{
                    page:this.page_info.current_page,
                    Minute:this.minute
                }})
                .then(({ data }) => {
                    this.inload = false;
                    this.data = data.data.data;
                    this.page_info = {
                        current_page:data.data.current_page,
                        last_page:data.data.last_page,
                    };
                })
                .catch(console.error);
        },
    },
    watch: {
        minute(newValue, oldValue) {
            this.getDetails()
        }
    },
    mounted() {
        this.getDetails();
    },
};
</script>

<style scoped>

</style>
