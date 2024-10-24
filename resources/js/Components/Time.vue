<template>
<script>
export default {
    props: {
      currentDate: String,
      currentTime: String,
      student: {
        type: Object,
        required: true,
      },
    },
  };
</script>
</template>