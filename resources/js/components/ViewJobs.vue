<template>
  <div class="container">
    <div class="row">
      <button class="btn btn-primary" @click="newModal = true">Nuevo</button>
    </div>
    <div class="row">
      <data-table :columns="columns" :items="process">
        <template #status="{ item }">
          <div v-if="item == 'STARTED'">Iniciado</div>
          <div v-if="item == 'FALLO'">TERMINADO</div>
          <div v-if="item == 'ENDED'">FALLO</div>
        </template>
        <template #out_descompress="{ item }">
          <textarea :disabled="true" :value="item" name="" id="" cols="80" rows="5"></textarea>
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
  </div>
</template>

<script>
export default {
  data() {
    return {
      new_process_date: null,
      newModal: false,
      columns: [
        { name: "Estado", value: "status" },
        { name: "salida", value: "out_descompress" },
        { name: "Mostrar data", value: "mostrar_data" },
      ],
      process: [],
      worker:null,
    };
  },
  methods: {
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
    this.loadProcess();
    this.worker = setInterval(this.loadProcess, 60000);
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
