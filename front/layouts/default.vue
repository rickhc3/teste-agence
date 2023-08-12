<template>
  <div class="flex flex-col min-h-screen">
    <Navbar />
    <div class="container-fluid mt-3 px-6 flex-grow pb-16">
      <Nuxt :key="componentKey"/>
    </div>

    <footer class="bg-gray-800 text-white text-center py-4">
      <p>&copy; 2023</p>
    </footer>
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
