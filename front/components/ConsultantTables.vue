<template>
  <div>
    <div
      v-for="(consultantData, consultantName) in apiData"
      :key="consultantName"
      class="mb-8"
    >
      <h2 class="text-xl font-semibold mb-4">{{ consultantName }}</h2>
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
              v-for="data in consultantData"
              :key="data.co_usuario"
              class="bg-white"
            >
              <td class="py-2 px-4">{{ data.no_usuario }}</td>
              <td class="py-2 px-4">{{ formatCurrency(data.net_revenue) }}</td>
              <td class="py-2 px-4">{{ formatCurrency(data.brut_salario) }}</td>
              <td class="py-2 px-4">{{ formatCurrency(data.comission) }}</td>
              <td
                class="py-2 px-4"
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
              <td class="py-2 px-4">Total</td>
              <td class="py-2 px-4">
                {{
                  formatCurrency(calculateTotal(consultantData, "net_revenue"))
                }}
              </td>
              <td class="py-2 px-4">
                {{
                  formatCurrency(calculateTotal(consultantData, "brut_salario"))
                }}
              </td>
              <td class="py-2 px-4">
                {{
                  formatCurrency(calculateTotal(consultantData, "comission"))
                }}
              </td>
              <td
                class="py-2 px-4"
                :class="{
                  'text-red-500':
                    parseFloat(calculateTotal(consultantData, 'profit')) < 0,
                  'text-blue-500':
                    parseFloat(calculateTotal(consultantData, 'profit')) >= 0,
                }"
              >
                {{ formatCurrency(calculateTotal(consultantData, "profit")) }}
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
    apiData: Object,
  },
  methods: {
    calculateTotal(dataArray, field) {
      return dataArray
        .reduce(
          (acc, curr) =>
            acc + parseFloat(curr[field].replace(".", "").replace(",", ".")),
          0
        )
        .toFixed(2);
    },
    formatCurrency(value) {
      return parseFloat(
        value.replace(".", "").replace(",", ".")
      ).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
    },

  },
  mounted() {
    console.log(this.apiData);
  },
};
</script>

