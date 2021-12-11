<template>
  <div>
    <div v-for="(item, index) in options">
      <label :for="`${name}_${item.value}`" class="inline-flex items-center">
        <input :id="`${name}_${item.value}`" type="radio" class="form-radio"
               :name="name" @input="$emit('input', $event.target.value)"
               :value="item.value" :checked="index === 0"/>
        <span class="ml-2">{{ item.name }}</span>
      </label>
    </div>
  </div>
</template>

<script>
export default {
  props: ['options', 'name', 'value']
}
</script>

<style scoped>

</style>