<template>
  <div>
    <div class="flex justify-center">
      <div class="w-1/2 pr-4">
        <h2 class="text-lg font-semibold mb-4">Período</h2>
        <div class="flex">
          <div class="w-1/2 pr-2">
            <label class="block mb-2" for="start-date">Data Inicial</label>
            <select class="border p-2 w-full" v-model="start_at">
              <option v-for="date in dates" :key="date" :value="date">
                {{ formatDate(date) }}
              </option>
            </select>
          </div>
          <div class="w-1/2 pl-2">
            <label class="block mb-2" for="end-date">Data Final</label>
            <select class="border p-2 w-full" v-model="end_at">
              <option v-for="date in dates" :key="date" :value="date">
                {{ formatDate(date) }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-center mt-8">
      <div class="w-1/2 pr-4">
        <h2 class="text-md font-semibold mb-4">Consultores Disponíveis</h2>
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
        <small class="text-gray-500">*Clique no nome do consultor para selecionar</small>
      </div>
      <div class="w-1/2 pl-4">
        <h2 class="text-md font-semibold mb-4">Consultores Selecionados</h2>
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
        <small class="text-gray-500">*Clique no nome do consultor para remover</small>
      </div>
    </div>

    <div class="flex justify-center mt-8 my-5">
      <button
        class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded mr-5"
        @click="fetchDataConsultant"
        :disabled="isLoading"
      >
        <span v-if="isLoading"> Carregando... </span>
        <span v-else> Buscar </span>
      </button>

      <button
        class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded"
        @click="toggleGraphs()"
        v-if="fetchedApi"
      >
        {{ isGraphsOpen ? "Fechar Gráficos" : "Ver Gráficos" }}
      </button>

      <div
        v-if="toastVisible"
        class="fixed top-12 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-800 text-white p-2 rounded shadow"
      >
        {{ mgsToast }}
      </div>
    </div>

    <div class="w-full justify-center mt-8 my-5" v-if="isGraphsOpen">
      <charts-modal
        :show="isGraphsOpen"
        :bar-chart-data="preparedBarChartData"
        :pie-chart-data="preparedPieChartData"
        @close-modal="closeModal"
      />
    </div>

    <consultant-tables :api-data="responseData" v-if="!isLoading" />
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
      responseData: [],
      dates: [],
      toastVisible: false,
      mgsToast: "",
      isLoading: false,
      isModalVisible: false,
      isGraphsOpen: false,
      fetchedApi: false,
    };
  },
  mounted() {
    this.fetchConsultants();
    this.fetchDates();
  },

  methods: {
    fetchConsultants() {
      this.$axios.get("consultants").then((res) => {
        this.availableConsultants = res.data.data;
      });
    },
    fetchDates() {
      this.$axios.get("invoices/order-dates").then((res) => {
        this.dates = res.data;
        this.start_at = this.dates[0];
        this.end_at = this.dates[this.dates.length - 1];
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
      this.isLoading = true;
      const consultants = this.selectedConsultants.map(
        (consultant) => consultant.co_usuario
      );

      if (consultants.length === 0) {
        this.isLoading = false;
        this.closeModal();
        this.mgsToast = "Selecione ao menos um consultor";
        this.toastVisible = true;
        setTimeout(() => {
          this.toastVisible = false;
        }, 3000);
        this.responseData = [];
        return;
      }

      this.$axios
        .get("consultants/net-revenue", {
          params: {
            start_at: this.start_at,
            end_at: this.end_at,
            users: consultants,
          },
        })
        .then((res) => {
          this.responseData = res.data;
          this.isLoading = false;
          this.fetchedApi = true;
        });
    },

    formatDate(date) {
      const options = {
        year: "numeric",
        month: "2-digit",
        timeZone: "America/Sao_Paulo",
      };
      const [year, month] = date.split("-");
      const formattedDate = new Date(year, month - 1);
      return formattedDate.toLocaleDateString("en-US", options);
    },

    openModal() {
      const consultants = this.selectedConsultants.map(
        (consultant) => consultant.co_usuario
      );

      if (consultants.length === 0) {
        this.isLoading = false;
        this.mgsToast = "Selecione ao menos um consultor";
        this.toastVisible = true;
        this.closeModal();
        setTimeout(() => {
          this.toastVisible = false;
        }, 3000);
        this.responseData = [];
        return;
      }

      this.isGraphsOpen = true;
    },
    closeModal() {
      this.isGraphsOpen = false;
    },
    getRandomColor() {
      return "#" + Math.floor(Math.random() * 16777215).toString(16);
    },

    toggleGraphs() {
      this.isGraphsOpen = !this.isGraphsOpen;
    },
  },

  computed: {
    preparedBarChartData() {
      const consultantsData = this.responseData;
      const months = this.dates.map((date) => this.formatDate(date));
      const totalFixedSalaries = consultantsData.reduce((total, consultant) => {
        const [monthNumber, year] = months[0].split("/");
        const monthDate = `${year}-${monthNumber}`;
        return (
          total +
          parseFloat(
            consultant.months[monthDate]?.brut_salario?.replace?.(/,/g, "") ||
              "0"
          )
        );
      }, 0);
      const averageFixedCost = totalFixedSalaries / consultantsData.length;

      const barChartData = {
        labels: months,
        datasets: consultantsData.map((consultant) => {
          const data = months.map((month) => {
            const [monthNumber, year] = month.split("/");
            const monthDate = `${year}-${monthNumber}`;
            const monthData = consultant.months[monthDate] || {};
            return parseFloat(
              monthData.net_revenue?.replace?.(/,/g, "") || "0"
            );
          });

          return {
            label: consultant.no_usuario,
            backgroundColor: this.getRandomColor(),
            data,
          };
        }),
      };

      barChartData.datasets.unshift({
        type: "line",
        label: "Custo Fixo Médio",
        borderColor: "#cecece",
        backgroundColor: "#cecece",
        borderWidth: 3,
        data: Array(months.length).fill(averageFixedCost),
        fill: false,
      });

      return barChartData;
    },

    preparedPieChartData() {
      const totalRevenue = this.responseData.reduce((total, consultant) => {
        const consultantTotal = Object.values(consultant.months).reduce(
          (consultantTotal, monthData) =>
            consultantTotal +
            parseFloat(monthData?.net_revenue?.replace?.(/,/g, "") || "0"),
          0
        );
        return total + consultantTotal;
      }, 0);

      const pieChartData = {
        labels: this.responseData.map((consultant) => consultant.no_usuario),
        datasets: [
          {
            data: this.responseData.map((consultant) =>
              Object.values(consultant.months).reduce(
                (consultantTotal, monthData) =>
                  consultantTotal +
                  parseFloat(
                    monthData?.net_revenue?.replace?.(/,/g, "") || "0"
                  ),
                0
              )
            ),
            order: 2,
            backgroundColor: this.responseData.map(() => this.getRandomColor()),
          },
        ],
      };

      return pieChartData;
    },
  },
};
</script>
