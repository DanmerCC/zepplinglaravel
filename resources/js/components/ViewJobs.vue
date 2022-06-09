<template>
  <div class="container">
    <div class="row">
      <button class="btn btn-womprimary" @click="newModal = true">Nuevo</button>
    </div>
    <div class="row">
      <data-table :columns="columns" :items="process">
        <template #descompresion="{row}">
            <div v-if="row.status == 'STARTED'">
            <button class="btn btn-secundary" @click="showOptionsInprocess = row">
                ...CARGANDO
            </button>

            </div>
            <div v-else>
                <div v-if="row.status == 'FALLO'">FALLADO</div>
                <div v-if="row.status == 'ENDED'">TERMINADO</div>
                <textarea :disabled="true" :value="row.out_descompress" name="" id="" cols="60" rows="5"></textarea>

                <!--<div class="text plainTextContainer">
                    {{ row.out_descompress }}
                </div>-->
            </div>
        </template>
        <template #parrafo2="{row}">
            <button v-for="(parrafo,index) in parrafos" class="btn btn-primary" @click="showModal(parrafo,row.id,index)"> {{ parrafo.title ?parrafo.title :('Parrafo '+index) }}</button>

        </template>
        <template #mostrar_data="{ item ,row}">
          <div class="container" v-if="row.status == 'ENDED'">
            <button class="btn btn-primary">Visualizar</button>
          </div>
          <div v-else></div>
        </template>
      </data-table>
    </div>
    <modal-component
      v-if="newModal"
      @close="
        newModal = false;
        new_process_date = null;
      "
      title="Nuevo Proceso"
    >
      <template #body>
        <div>
          <label for="new_process_date">Fecha</label>
          <input
            v-model="new_process_date"
            class="form-control"
            type="date"
            name="new_process_date"
            id="new_process_date"
            placeholder="Fecha"
          />
          <small id="emailHelp" class="form-text text-muted">La Descompresión de los archivos puede tardar un tiempo
cuando finalice se le enviara un correo electronico.</small>
        </div>
      </template>
      <template #footer>
        <button
          :disabled="!dateFormated"
          class="btn btn-primary"
          @click="iniciarDescompresion()"
        >
          Iniciar
        </button>
      </template>
    </modal-component>
    <modal-component :isExtraLarge="true" v-if="modalParrafo!=null" @close="modalParrafo = null;  resultadoParagrafoStandar='';paragraphLowInfo=''" :title="modalParrafo.title">
        <template #body>
        <div class="container">
            <div class="row">
                <div class="col-6" v-for="(param,index) in modalParrafo.settings.params">
                    <label  :for="'input_'+index">
                        {{ index }}
                    </label>
                    <input :name="'input_'+index" :id="'input_'+index" class="form-control" type="text" v-model="modalParrafo.settings.params[index]">
                     <small id="emailHelp" class="form-text text-muted">{{paragraphLowInfo}}</small>
                </div>
                <div class="col-12" v-if="Object.keys(modalParrafo.settings.params) == 0">
                    <h4>No son necesarios datos de entrada</h4>
                </div>
            </div>
            <div class="row " v-if="verHistorial">
                <div class="container" >
                    <div class="text-bordered text plainTextContainer" v-for="result in getHistoryByParagraph(modalParrafo.id,modalParrafo.process_id)" v-html="result"></div>
                </div>
            </div>
            <div v-else>
                <button class="btn btn-default btn-sm" @click="verHistorial = true"><small>ver historial</small></button>
            </div>
            <button v-if="verHistorial" class="btn btn-sm btn-default" @click="verHistorial = false">
                <small>
                    cerrar historial
                </small>
            </button>
        </div>
            <div class="container" v-if="resultadoParagrafoStandar!='' && resultadoParagrafoStandar!=null">
                <div class="text plainTextContainer">{{ resultadoParagrafoStandar }}</div>
            </div>
        </template>
        <template #footer>
            <div>
            <button class="btn btn-womprimary" @click="viewParrafoResult(modalParrafo,modalParrafo.settings.params,modalParrafo.process_id)">Enviar</button>
            </div>
        </template>
    </modal-component>
    <modal-component v-if="showOptionsInprocess!=null" @close="showOptionsInprocess = null">
        <template #body>
            <div>
                <button class="btn btn-primary" @click="deCompress(showOptionsInprocess)">Interrumpir proceso</button>
            </div>
        </template>
    </modal-component>
  </div>
</template>

<script>
export default {
  data() {
    return {
      new_process_date: null,
      newModal: false,
      columns: [
        { name: "Descompresion", value: "descompresion" },
        { name: "Analisis", value: "parrafo2" },/*
        { name: "Parrafo 3", value: "parrafo3" },
        { name: "Parrafo 4", value: "parrafo4" },
        { name: "Parrafo 5", value: "parrafo5" },
        { name: "Parrafo 6", value: "parrafo6" },
        { name: "Parrafo 7", value: "parrafo7" },
        { name: "Parrafo 8", value: "parrafo8" },
        { name: "Parrafo 9", value: "parrafo9" },
        { name: "Parrafo 10", value: "parrafo10" },
        { name: "Parrafo 11", value: "parrafo11" },
        { name: "Parrafo 12", value: "parrafo12" },*/
      ],
      showOptionsInprocess:null,
      process: [],
      worker:null,
      parrafos:[],
      modalParrafo:null,
      resultadoParagrafoStandar:'',
      paragraphLowInfo:'',
      verHistorial:false,
      example:` +-------------------+-------------------+------------+---------------+---------------+-------+-------+-------------------+
|         start_time|           end_time|      msisdn|           imsi|           imei|lac_tac|sac_eci|ip_address_assigned|
+-------------------+-------------------+------------+---------------+---------------+-------+-------+-------------------+
|2022_05_30 16:29:42|2022_05_30 16:29:42|573028491759|732360024222496|861880055883816|    193|   3641|    100.104.209.203|
|2022_05_30 16:30:00|2022_05_30 16:30:01|573026724613|732360022318858|354142101165928|    e97|    4af|       10.73.210.17|
|2022_05_30 16:30:33|2022_05_30 16:30:33|573026724613|732360022318858|354142101165928|    e97|    4af|       100.97.81.80|
|2022_05_30 16:29:25|2022_05_30 16:30:30|573027594996|732360027929104|359458086883425|   2ee1|   4bfb|       100.98.47.72|
|2022_05_30 16:28:32|2022_05_30 16:30:41|573014289449|732360027351348|355620084350103|     25|   5155|      10.71.199.137|
|2022_05_30 16:27:42|2022_05_30 16:30:45|573236255817|732360126467835|355567114941924|     74|   7bc3|       10.65.95.194|
|2022_05_30 16:28:31|2022_05_30 16:30:42|573219427507|732360022818693|351622118022439|   1dba|   303d|    100.100.139.202|
|2022_05_30 16:27:28|2022_05_30 16:29:57|573238046214|732360021279975|352017077971776|    d49|   65c8|               null|
|2022_05_30 16:27:48|2022_05_30 16:30:10|573026998343|732360023706282|864987042848807|     7f|   73fa|        10.70.48.38|
|2022_05_30 16:25:54|2022_05_30 16:30:17|573212003048|732360028542158|865842033050914|   17b8|    b27|      10.67.251.225|
|2022_05_30 16:29:21|2022_05_30 16:30:23|573142327686|732360021249299|863697043717395|    e7e|   2d5a|               null|
|2022_05_30 16:30:04|2022_05_30 16:30:23|573142327686|732360021249299|863697043717395|    e7e|   2d47|               null|
|2022_05_30 16:30:04|2022_05_30 16:30:04|573142327686|732360021249299|863697043717395|    e7e|   2d47|               null|
|2022_05_30 16:16:20|2022_05_30 16:30:13|573146540500|732360028571014|860454047082407|    db4|   5b46|        10.73.8.125|
|2022_05_30 16:29:26|2022_05_30 16:30:28|573123069909|732360021994168|358742570324645|   1db7|   d4f5|       100.97.54.83|
|2022_05_30 16:27:22|2022_05_30 16:30:53|573028556031|732360022227873|354267098698423|    e8b|   2ca3|               null|
|2022_05_30 16:29:43|2022_05_30 16:30:46|573238088927|732360022892283|866175068057853|   3af2|   d023|               null|
|2022_05_30 16:27:16|2022_05_30 16:30:57|573027090489|732360023553013|354407089170061|    d4d|   9140|     100.103.83.152|
|2022_05_30 16:29:05|2022_05_30 16:30:18|573026013282|732360021741129|865032046219660|    d49|   64ea|     100.101.62.136|
|2022_05_30 16:21:44|2022_05_30 16:31:02|573238085648|732360028464729|860692058280614|   3332|   d9ba|      10.67.234.113|
+-------------------+-------------------+------------+---------------+---------------+-------+-------+-------------------+
only showing top 20 rows
`
    };
  },
  methods: {
    deCompress(procesInfo){
        console.log(procesInfo)
        let data = {
            id:procesInfo.id
        }
        axios.post(`/stop/decompres`,data)
        .then((response)=>{
            const res = response.data
            if(res.status == 'OK'){
                this.showOptionsInprocess = null
            }
        })
        .catch(error=>console.error(error))
    },
    viewParrafoResult(parrafo,params,process_id){
        let data = {...params,process_id}
        console.log(params)
        axios.post(`/parrafostandar/${parrafo.id}`,data)
        .then((response)=>{
            console.log(response);
            this.resultadoParagrafoStandar = response.data.body.msg.map(x=>x.data.replaceAll('\n',`
            `)).join("\n")
        })
        .catch(error=>console.error(error))
    },
    showModal(parrafo,process_id,index){

        this.paragraphLowInfo = ''
        if(index == 0){

            this.paragraphLowInfo = 'a consulta de IP Publica puede tardar un tiempo cuando finalice se le enviara un correo electronico.'
        }
        this.modalParrafo = {...parrafo,process_id:process_id}
    },
    parrafo2(){
        let data = {}
        axios.post(`/parrafo2`,data)
        .then((response)=>{
            this.loadProcess()
        })
        .catch(error=>console.error(error))
    },
    getParrafos(){
        let data = {}
        axios.get(`/paragraphs`,data)
        .then((response)=>{
            this.parrafos = response.data
        })
        .catch(error=>console.error(error))
    },
    getHistoryByParagraph(paragraph_id,process_id){
        let process = this.process.find(x=>x.id == process_id)
        let resultsFinded = []
        process.results.forEach(result => {
            if(result.paragraph_id == paragraph_id){
                resultsFinded.push(result)
            }
        });
        console.log(resultsFinded.map(z=>z.outout))
        return resultsFinded.map(z=>z.outout.replaceAll("\\\"","").split("\\n").join("\n"))
    },
    loadProcess() {
      let data = {};
      axios
        .get(`/process`, data)
        .then((response) => {
          this.process = response.data;
        })
        .catch((error) => console.error(error));
    },
    iniciarDescompresion() {
      let data = {
        fecha: this.dateFormated,
      };
      axios
        .post(`/descomprimir`, data)
        .then((response) => {
          this.newModal = false;
          this.loadProcess()
        })
        .catch((error) => console.error(error));
    },
  },
  mounted() {
    this.getParrafos()
    this.loadProcess();
    this.worker = setInterval(this.loadProcess, 4000);
  },
  computed: {
    dateFormated() {
      if (!this.new_process_date) return null;
      return this.new_process_date.replaceAll("-", "_");
    },
  },
};
</script>

<style scoped>
.text {
    display: block!important;
    font-family: Monaco,Menlo,"Ubuntu Mono",Consolas,source-code-pro,monospace;
    font-size: 12px!important;
    line-height: 1.42857143!important;
    margin: 0 0 5px!important;
    padding-top: 2px;
    unicode-bidi: embed;
    white-space: pre-wrap;
    word-break: break-all!important;
    word-wrap: break-word!important;
}
.plainTextContainer {
    font-family: Monaco,Menlo,"Ubuntu Mono",Consolas,source-code-pro,monospace;
    font-size: 12px!important;
}

::v-deep .btn-womprimary,.btn.btn-primary {
    background-color: #612D8A !important;
    color: white !important;
    border-color: #9d6fc1 !important;
}

.text-bordered {
    border: dotted gray;
}
</style>
