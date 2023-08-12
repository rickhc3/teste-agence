<template>
  <div>
    <div class="overflow-x-auto">
      <table class="min-w-full border rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-2 px-4 text-left">Período</th>
            <th
              v-for="(customer, index) in apiData"
              :key="index"
              class="py-2 px-4 text-left"
            >
              {{ customer.customer_name }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(data, period) in combinedData"
            :key="period"
            class="bg-white"
          >
            <td class="py-2 px-4 whitespace-nowrap">
              {{ formatDate(period) }}
            </td>
            <td
              v-for="(customer, index) in apiData"
              :key="index"
              class="py-2 px-4 whitespace-nowrap"
            >
              {{
                formatCurrency(
                  data[customer.customer_id]?.net_revenue || "0.00"
                )
              }}
            </td>
          </tr>
          <tr class="bg-gray-100">
            <td class="py-2 px-4 font-semibold">Total</td>
            <td
              v-for="(customer, index) in apiData"
              :key="index"
              class="py-2 px-4 font-semibold"
            >
              {{
                formatCurrency(calculateTotal(customer.months, "net_revenue"))
              }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    apiData: Array,
  },
  computed: {
    combinedData() {
      const combined = {};
      for (const customer of this.apiData) {
        for (const period in customer.months) {
          if (!combined[period]) {
            combined[period] = {};
          }
          combined[period][customer.customer_id] = customer.months[period];
        }
      }
      return combined;
    },
  },
  methods: {
    calculateTotal(months, field) {
      let total = 0;
      for (const period in months) {
        total += parseFloat(
          months[period][field].replace(".", "").replace(",", "")
        );
      }
      let totalTest = total / 10000;
      return totalTest.toFixed(2);
    },
    formatCurrency(value) {
      return parseFloat(
        value.replace(".", "").replace(",", ".")
      ).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
      });
    },
    formatDate(date) {
      const options = {
        year: "numeric",
        month: "2-digit",
        timeZone: "America/Sao_Paulo",
      };
      const [year, month] = date.split("-");
      const formattedDate = new Date(year, month - 1); // Subtrai 1 do mês para compensar a indexação baseada em zero
      return formattedDate.toLocaleDateString("en-US", options);
    },
  },
};
</script>
