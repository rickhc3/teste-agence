<template>
  <div>
    <div class="flex justify-center mt-8">
      <div class="w-1/2 pr-4">
        <h2 class="text-lg font-semibold mb-4">Período</h2>
        <div class="flex">
          <div class="w-1/2 pr-2">
            <label class="block mb-2" for="start-date">Data Inicial</label>
            <input type="date" class="border p-2 w-full" v-model="start_at" />
          </div>
          <div class="w-1/2 pl-2">
            <label class="block mb-2" for="end-date">Data Final</label>
            <input type="date" class="border p-2 w-full" v-model="end_at" />
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-center mt-8">
      <div class="w-1/2 pr-4">
        <h2 class="text-lg font-semibold mb-4">Consultores Disponíveis</h2>
        <ul class="border p-2 h-64 overflow-y-scroll">
          <li
            v-for="consultant in availableConsultants"
            :key="consultant.co_usuario"
            class="cursor-pointer hover:bg-gray-100 p-2"
            @click="selectConsultant(consultant)"
          >
            {{ consultant.no_usuario }}
          </li>
        </ul>
      </div>
      <div class="w-1/2 pl-4">
        <h2 class="text-lg font-semibold mb-4">Consultores Selecionados</h2>
        <ul class="border p-2 h-64 overflow-y-scroll">
          <li
            v-for="consultant in selectedConsultants"
            :key="consultant.co_usuario"
            class="cursor-pointer hover:bg-gray-100 p-2"
            @click="deselectConsultant(consultant)"
          >
            {{ consultant.no_usuario }}
          </li>
        </ul>
      </div>
    </div>
    <div class="flex justify-center mt-8">
      <button
        class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded"
        @click="fetchDataConsultant"
      >
        Buscar
      </button>
    </div>

    <consultant-tables :api-data="responseData" />


  </div>
</template>

<script>
export default {
  data() {
    return {
      availableConsultants: [],
      selectedConsultants: [],
      start_at: "",
      end_at: "",
      responseData: {},
    };
  },
  mounted() {
    this.fetchApi();
  },
  methods: {
    fetchApi() {
      this.$axios.get("consultants").then((res) => {
        this.availableConsultants = res.data.data;
      });
    },
    selectConsultant(consultant) {
      this.selectedConsultants.push(consultant);
      this.selectedConsultants.sort((a, b) =>
        a.no_usuario.localeCompare(b.no_usuario)
      );

      const index = this.availableConsultants.findIndex(
        (c) => c.co_usuario === consultant.co_usuario
      );
      if (index !== -1) {
        this.availableConsultants.splice(index, 1);
      }
    },
    deselectConsultant(consultant) {
      this.availableConsultants.push(consultant);
      this.availableConsultants.sort((a, b) =>
        a.no_usuario.localeCompare(b.no_usuario)
      );

      const index = this.selectedConsultants.findIndex(
        (c) => c.co_usuario === consultant.co_usuario
      );
      if (index !== -1) {
        this.selectedConsultants.splice(index, 1);
      }
    },

    fetchDataConsultant() {
      const consultants = this.selectedConsultants.map(
        (consultant) => consultant.co_usuario
      );

      this.$axios
        .get("consultants/net-revenue", {
          params: {
            start_at: this.start_at,
            end_at: this.end_at,
            users: consultants,
          },
        })
        .then((res) => {
          console.log(res.data);
          this.responseData = res.data;
        });
    },
  },
};
</script>

<style>
/* Pode adicionar estilos personalizados aqui, se necessário */
</style>
