<template>
  <date-time-picker class="w-full form-control form-input form-input-bordered"
                    :value="modelValue"
                    @input="$emit('update:modelValue', $event.target.value)"
                    @change="$emit('update:modelValue', $event.target.value)"
                    autocomplete="off"/>
</template>

<script>
export default {
  props: ['modelValue']
}
</script>

<style scoped>

</style>