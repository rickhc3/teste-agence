<template>
  <div>
    <h1>Customers</h1>
    <ul>
      <li v-for="customer in customers" :key="customer.co_cliente">
        {{ customer.no_fantasia }}
      </li>
    </ul>
  </div>
</template>

<script>

export default {
  name: 'IndexPage',
  data() {
    return {
      customers: [],
    };
  },
  mounted() {
    this.fetchApi();
  },
  methods: {
    fetchApi() {
      this.$axios.get('customers').then((res) => {
        this.customers = res.data.data;
      });
    },
  },
}
</script>
