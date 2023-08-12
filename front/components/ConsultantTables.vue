<template>
  <div>
    <div v-for="(consultant, index) in apiData" :key="index" class="mb-8">
      <h2 class="text-xl font-semibold mb-4">{{ consultant.no_usuario }}</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full border rounded-lg overflow-hidden">
          <thead class="bg-gray-200">
            <tr>
              <th class="py-2 px-4 text-left">Período</th>
              <th class="py-2 px-4 text-left">Receita Líquida</th>
              <th class="py-2 px-4 text-left">Custo Fixo</th>
              <th class="py-2 px-4 text-left">Comissão</th>
              <th class="py-2 px-4 text-left">Lucro</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(data, period) in consultant.months"
              :key="period"
              class="bg-white"
            >
              <td class="py-2 px-4 whitespace-nowrap">{{ formatDate(period) }}</td>
              <td class="py-2 px-4 whitespace-nowrap">{{ formatCurrency(data.net_revenue) }}</td>
              <td class="py-2 px-4 whitespace-nowrap">-{{ formatCurrency(data.brut_salario) }}</td>
              <td class="py-2 px-4 whitespace-nowrap">-{{ formatCurrency(data.comission) }}</td>
              <td
                class="py-2 px-4 whitespace-nowrap"
                :class="{
                  'text-red-500':
                    parseFloat(data.profit.replace('.', '').replace(',', '.')) <
                    0,
                  'text-blue-500':
                    parseFloat(
                      data.profit.replace('.', '').replace(',', '.')
                    ) >= 0,
                }"
              >
                {{ formatCurrency(data.profit) }}
              </td>
            </tr>
            <tr class="bg-gray-100">
              <td class="py-2 px-4 whitespace-nowrap"><strong>Total</strong></td>
              <td class="py-2 px-4 whitespace-nowrap">
                {{
                  formatCurrency(
                    calculateTotal(consultant.months, "net_revenue")
                  )
                }}
              </td>
              <td class="py-2 px-4 whitespace-nowrap">
                {{
                  formatCurrency(
                    calculateTotal(consultant.months, "brut_salario")
                  )
                }}
              </td>
              <td class="py-2 px-4 whitespace-nowrap">
                {{
                  formatCurrency(calculateTotal(consultant.months, "comission"))
                }}
              </td>
              <td
                class="py-2 px-4 whitespace-nowrap"
                :class="{
                  'text-red-500':
                    parseFloat(calculateTotal(consultant.months, 'profit')) < 0,
                  'text-blue-500':
                    parseFloat(calculateTotal(consultant.months, 'profit')) >=
                    0,
                }"
              >
                {{
                  formatCurrency(calculateTotal(consultant.months, "profit"))
                }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    apiData: Array,
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
