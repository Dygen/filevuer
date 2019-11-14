<template>
  <div 
    v-show="visible" 
    id="loading-overlay"/>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            default: false
        }
    }
}
</script>