<template>
  <select class="w-full form-control form-select" :value="value" @input="$emit('input', $event.target.value)"
          @change="$emit('change', $event.target.value)">
    <option value="" selected="selected" disabled="disabled">
      Choose an option
    </option>
    <option v-for="option in options" :value="option[valueName]">{{ option[titleName] }}</option>
  </select>
</template>

<script>
export default {
  props: ['options', 'valueName', 'titleName', 'value'],
}
</script>

<style scoped>

</style>