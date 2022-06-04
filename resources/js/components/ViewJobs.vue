<template>
  <div class="container">
    <div class="row">
      <button class="btn btn-primary" @click="newModal = true">Nuevo</button>
    </div>
    <div class="row">
      <data-table :columns="columns" :items="process">
        <template #descompresion="{row}">
            <div v-if="row.status == 'STARTED'">
            <button class="btn btn-secundary">
                ...CARGANDO
            </button>

            </div>
            <div v-else>
                <div v-if="row.status == 'FALLO'">FALLADO</div>
                <div v-if="row.status == 'ENDED'">TERMINADO</div>
                <textarea :disabled="true" :value="row.out_descompress" name="" id="" cols="60" rows="5"></textarea>
            </div>
        </template>
        <template #parrafo2>

            <!--<button class="btn btn-primary" @click="parrafo2()">Parrafo 2</button>
            <button class="btn btn-primary">Parrafo 3</button>
            <button class="btn btn-primary">Parrafo 4</button>
            <button class="btn btn-primary">Parrafo 5</button>
            <button class="btn btn-primary">Parrafo 6</button>
            <button class="btn btn-primary">Parrafo 7</button>
            <button class="btn btn-primary">Parrafo 8</button>
            <button class="btn btn-primary">Parrafo 9</button>
            <button class="btn btn-primary">Parrafo 10</button>
            <button class="btn btn-primary">Parrafo 11</button>
            <button class="btn btn-primary">Parrafo 12</button>!-->
            <button v-for="(parrafo,index) in parrafos" class="btn btn-primary" @click="showModal(parrafo)"> {{ parrafo.title ?parrafo.title :('Parrafo '+index) }}</button>
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
            type="date"
            name="new_process_date"
            id="new_process_date"
            placeholder="Fecha"
          />
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
    <modal-component v-if="modalParrafo!=null" @close="modalParrafo = null" :title="modalParrafo.title">
        <template #body>
            <div v-for="(param,index) in modalParrafo.settings.params">
                <label for="">
                    {{ index }}
                </label>
                <input type="text" v-model="modalParrafo.settings.params[index]">
            </div>
            <div class="container" v-if="resultadoParagrafoStandar!='' && resultadoParagrafoStandar!=null">
                <textarea readonly :disabled="false" v-model="resultadoParagrafoStandar" cols="60" rows="5"></textarea>
            </div>
        </template>
        <template #footer>
            <div>
            <button class="btn btn-primary" @click="viewParrafoResult(modalParrafo,modalParrafo.settings.params)">Enviar</button>
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
        { name: "Parrafo 2", value: "parrafo2" },/*
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
      process: [],
      worker:null,
      parrafos:[],
      modalParrafo:null,
      resultadoParagrafoStandar:''
    };
  },
  methods: {
    viewParrafoResult(parrafo,params){
        let data = {}
        console.log(params)
        axios.post(`/pararafosSinParametro/${parrafo.id}`,data)
        .then((response)=>{
            console.log(response);
            this.resultadoParagrafoStandar = response.data.body.msg.map(x=>x.data)
        })
        .catch(error=>console.error(error))
    },
    showModal(parrafo){
        this.modalParrafo = parrafo
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

<style></style>
