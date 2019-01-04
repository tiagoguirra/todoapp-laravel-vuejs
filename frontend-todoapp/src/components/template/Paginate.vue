<template>
    <section class="container paginate-container">
        <div class="paginate" v-if="this.pageItem.pages!=null">
            <div class="paginate-buttons">
                <a class="button arrow" v-if="pageItem.previous" @click="clickPage(pageItem.previous)"><i class="fas fa-chevron-left"></i></a>
                <a class="button page" v-for="(item,key) in pageItem.pages.first" :key="key" v-bind:class="[pageItem.current==key?'active':'']"  @click="clickPage(item)">{{ key }}</a>                
                <span v-if="pageItem.pages.slider">...</span>
                <a class="button page" v-for="(item,key) in pageItem.pages.slider" :key="key" v-bind:class="[pageItem.current==key?'active':'']" @click="clickPage(item)">{{ key }}</a>                
                <span v-if="pageItem.pages.last">...</span>
                <a class="button page" v-for="(item,key) in pageItem.pages.last" :key="key" v-bind:class="[pageItem.current==key?'active':'']" @click="clickPage(item)">{{ key }}</a>                
                <a class="button arrow" v-if="pageItem.next" @click="clickPage(pageItem.next)"><i class="fas fa-chevron-right"></i></a>
            </div>            
        </div>          
    </section>
</template>
<script>
export default {
  name: "Paginate",
  props:["pageItem"],
  methods:{
      clickPage(pageId){
        //   this.$parent.setPage(pageId);
          this.$emit('setPage',{pageId})
      }
  }
};
</script>
<style>
.paginate-buttons span{
    color:#7e7e7e;
}
.paginate-container .paginate{
    width: 100%;
    text-align: center;
}
.paginate-buttons .button{
    width: 30px;
    height: 30px;    
    display: inline-flex;
    align-items: center;
    justify-content: center;       
    transition: all 0.25s ease;
    color:#7e7e7e;
    border-radius: 50%;
}
.paginate-buttons .button:hover,.paginate-buttons .button.active{
    background-color: #191919;
    color:#fff
}
</style>
