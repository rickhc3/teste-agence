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
        <h2 class="text-md font-semibold mb-4">Clientes Disponíveis</h2>
        <ul class="border p-2 h-64 overflow-y-scroll">
          <li
            v-for="customer in availableCustomers"
            :key="customer.co_cliente"
            class="cursor-pointer hover:bg-gray-100 p-2"
            @click="selectConsultant(customer)"
          >
            {{ customer.no_fantasia }}
          </li>
        </ul>
        <small class="text-gray-500">*Clique no nome do cliente para selecionar</small>
      </div>
      <div class="w-1/2 pl-4">
        <h2 class="text-md font-semibold mb-4">Clientes Selecionados</h2>
        <ul class="border p-2 h-64 overflow-y-scroll">
          <li
            v-for="customer in selectedCustomers"
            :key="customer.co_cliente"
            class="cursor-pointer hover:bg-gray-100 p-2"
            @click="deselectConsultant(customer)"
          >
            {{ customer.no_fantasia }}
          </li>
        </ul>
        <small class="text-gray-500">*Clique no nome do cliente para remover</small>
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
        :pie-chart-data="preparedPieChartData"
        @close-modal="closeModal"
      />
    </div>

    <customer-tables :api-data="responseData" v-if="!isLoading && fetchedApi" />
  </div>
</template>

<script>
export default {
  data() {
    return {
      availableCustomers: [],
      selectedCustomers: [],
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
    this.fetchCustomers();
    this.fetchDates();
  },

  methods: {
    fetchCustomers() {
      this.$axios.get("customers").then((res) => {
        this.availableCustomers = res.data.data;
      });
    },
    fetchDates() {
      this.$axios.get("invoices/order-dates").then((res) => {
        this.dates = res.data;
        this.start_at = this.dates[0];
        this.end_at = this.dates[this.dates.length - 1];
      });
    },
    selectConsultant(customer) {
      this.selectedCustomers.push(customer);
      this.selectedCustomers.sort((a, b) =>
        a.no_fantasia.localeCompare(b.no_fantasia)
      );

      const index = this.availableCustomers.findIndex(
        (c) => c.co_cliente === customer.co_cliente
      );
      if (index !== -1) {
        this.availableCustomers.splice(index, 1);
      }
    },
    deselectConsultant(customer) {
      this.availableCustomers.push(customer);
      this.availableCustomers.sort((a, b) =>
        a.no_fantasia.localeCompare(b.no_fantasia)
      );

      const index = this.selectedCustomers.findIndex(
        (c) => c.co_cliente === customer.co_cliente
      );
      if (index !== -1) {
        this.selectedCustomers.splice(index, 1);
      }
    },

    fetchDataConsultant() {
      this.isLoading = true;
      const customers = this.selectedCustomers.map(
        (customer) => customer.co_cliente
      );

      if (customers.length === 0) {
        this.isLoading = false;
        this.closeModal();
        this.mgsToast = "Selecione ao menos um cliente";
        this.toastVisible = true;
        setTimeout(() => {
          this.toastVisible = false;
        }, 3000);
        this.responseData = [];
        return;
      }

      this.$axios
        .get("customers/net-revenue", {
          params: {
            start_at: this.start_at,
            end_at: this.end_at,
            customers,
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
      const Customers = this.selectedCustomers.map(
        (customer) => customer.co_usuario
      );

      if (Customers.length === 0) {
        this.isLoading = false;
        this.mgsToast = "Selecione ao menos um cliente";
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
    preparedPieChartData() {
      const totalRevenue = this.responseData.reduce((total, customer) => {
        const customerTotal = Object.values(customer.months).reduce(
          (customerTotal, monthData) =>
            customerTotal +
            parseFloat(monthData?.net_revenue?.replace?.(/,/g, "") || "0"),
          0
        );
        return total + customerTotal;
      }, 0);

      const pieChartData = {
        labels: this.responseData.map((customer) => customer.customer_name),
        datasets: [
          {
            data: this.responseData.map((customer) =>
              Object.values(customer.months).reduce(
                (customerTotal, monthData) =>
                  customerTotal +
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
