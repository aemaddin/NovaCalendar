<template>
  <label :for="label" class="inline-block text-80 pt-2 leading-tight">{{ label }}</label>
</template>

<script>
export default {
  props: ['label'],
  computed: {
    titleize() {
      return this.name.replace(/(?:^|\s|-)\S/g, x => x.toUpperCase());
    }
  }
}
</script>

<style scoped>

</style>