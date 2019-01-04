<template>
  <div id="app">
    <toast-container :toastConfig="customConfig"></toast-container>  
    <Menu />
    <Progress class="progress-menu" type="indeterminate" v-if="isLoading"/>    
    <Content />      
  </div>
</template>

<script>
import Menu from '@/components/template/Menu'
import Content from '@/components/template/Content'
import Progress from "@/components/template/Progress";
import {mapGetters} from 'vuex'
import {USER_REQUEST} from '@/store/actions/user'

export default {
  name: 'App',
  components:{Menu,Content,Progress},
  computed:{
     ...mapGetters([      
      "isLoading",
      "isAuthenticated"   
    ]),
    customConfig: function() {
      return {
        toastContainerId: 1,
        animation: "ease-out-right",
        preventDuplicates: true
      };
    }
  },
  methods:{
    getData(){
      if(this.isAuthenticated){
        this.$store.dispatch(USER_REQUEST)
      }
    }
  },
  mounted(){
    this.getData()
  }
}
</script>

<style>
@import "./assets/css/global.css";
@import "vue-on-toast/dist/vue-on-toast.css";
.progress-menu{
  position: fixed;
  top:0;
}
</style>
