<template>
  <div>
    <Navbar />
    <div class="container-fluid mt-3 px-6">
      <Nuxt :key="componentKey"/>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      componentKey: 0
    };
  },

  methods: {
  },
};
</script>

<style>
</style>
