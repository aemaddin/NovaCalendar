<template>
  <input class="w-full form-control form-input form-input-bordered" type="text" :value="value"
         @input="$emit('input', $event.target.value)"
         ref="input">
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: '',
      required: true
    }
  }
}
</script>

<style scoped>

</style>