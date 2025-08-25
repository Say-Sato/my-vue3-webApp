<template>
  <div class="container">
    <div class="form-card">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>
.container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 40px 20px;
  box-sizing: border-box;
}

.form-card {
  background: white;
  padding: 30px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  max-width: 600px; /* 少し幅を広げました */
  width: 100%;
}
</style>
