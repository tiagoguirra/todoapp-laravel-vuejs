<template>
   <div class="container container-base">
    <router-view></router-view>
  </div>
</template>

<script>
    export default {
        name:'Content'
    }
</script>

<style>
body{
    background-color: #EBEBE8;    
}
.container-base{    
    padding-top: 60px; 
}
</style>