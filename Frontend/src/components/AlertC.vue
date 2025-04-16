<template>
  <transition name="fade">
    <div v-if="message" class="floating-alert" :class="alertClass">
      {{ message }}
    </div>
  </transition>
</template>

<script>
export default {
  name: 'AlertC',
  props: {
    message: {
      type: String,
      required: true,
    },
    color: {
      type: String,
      default: 'danger', // color por defecto es 'danger'
    },
  },
  computed: {
    alertClass() {
      return `alert alert-${this.color}`
    },
  },
}
</script>

<style scoped>
.floating-alert {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  min-width: 250px;
  max-width: 400px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
