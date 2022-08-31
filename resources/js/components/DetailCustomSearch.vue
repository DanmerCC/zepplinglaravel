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
        <data-table :inload="inload" :columns="columns" :items="data"></data-table>
        <div class="row" v-if="page_info !=null">
            <div class="col-12">
                <button class="btn btn-sm btn-primary"> {{page_info.current_page}}</button>
                ...
                <button class="btn btn-sm btn-secondary"> {{page_info.last_page}}</button>
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
                { name: "SourceIP", value: "SourceIP" },
                { name: "DestinationIP", value: "DestinationIP" },
                { name: "SourceNatIP", value: "SourceNatIP" },
            ],
            data: [],
        };
    },
    methods: {
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
