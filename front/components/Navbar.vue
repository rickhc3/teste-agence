<template>
  <section class="pt-8 px-6">
    <h2 class="mb-4 text-2xl font-bold">Teste Agence</h2>

    <h3 class="mb-4 text-md font-bold">Performance Comercial</h3>
    <div class="flex flex-wrap text-sm text-center">
      <a
        :class="{
          'inline-block w-full md:w-1/2 lg:w-auto mb-4 lg:mb-0 px-4 pb-2 border-b-2 border-indigo-500 text-indigo-500 cursor-pointer': activeTab === 'consultor',
          'inline-block w-full md:w-1/2 lg:w-auto mb-4 lg:mb-0 px-4 pb-2 text-gray-300 border-b-2 border-transparent hover:border-gray-300 cursor-pointer': activeTab !== 'consultor'
        }"
        @click="changeTab('consultor')"
      >
        Por Consultor
      </a>
      <a
        :class="{
          'inline-block w-full md:w-1/2 lg:w-auto mb-4 lg:mb-0 px-4 pb-2 border-b-2 border-indigo-500 text-indigo-500 cursor-pointer': activeTab === 'cliente',
          'inline-block w-full md:w-1/2 lg:w-auto mb-4 lg:mb-0 px-4 pb-2 text-gray-300 border-b-2 border-transparent hover:border-gray-300 cursor-pointer': activeTab !== 'cliente'
        }"
        @click="changeTab('cliente')"
      >
        Por Cliente
      </a>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      activeTab: 'consultor'
    };
  },
  methods: {
    changeTab(tab) {
      this.activeTab = tab;
    }
  }
};
</script>
