<template>
  <div class="action">
    <div class="flex border-b border-40">
      <div class="px-8 py-6 w-1/5">
        <slot name="label"></slot>
      </div>
      <div class="py-6 px-8 w-1/2">
        <slot name="content"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FormSection"
}
</script>

<style scoped>

</style>