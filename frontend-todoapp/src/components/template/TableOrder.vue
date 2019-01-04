<template>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr v-if="tableHeaderFilter!=null">
          <th scope="col" v-for="(item, index) in tableHeaderFilter" :key="index">
            <a @click="setOrder(index)">
              {{ item}}
              <span v-if="order.column==index">
                <i class="fas fa-sort-up" v-if="order.order=='asc'"></i>
                <i class="fas fa-sort-down" v-else></i>
              </span>
            </a>
          </th>
        </tr>
        <tr v-else>
          <th scope="col" v-for="(item, index) in tableHeader" :key="index">{{ item }}</th>
        </tr>        
      </thead>
      <tbody>
        <slot name="list"></slot>  
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "TableOrder",
  props:{
    tableHeader:{
      type:Array    
    },
    tableHeaderFilter:{
      type:Object,
      default:null     
    }   
  },  
  data:()=>({
    order:{
      column:'created_at',
      order:'asc'
    }
  }),  
  methods:{
    setOrder(order){      
      if(order!='option'){        
        this.order.order = this.order.column!=order?'desc':this.order.order=='desc'?'asc':'desc';  
        this.order.column = order      
        this.$emit("setOrderTable", { order:this.order, pageId: "?page=1" });
      }
    }
  }  
};
</script>

<style>
.options{
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.options .option-btn{
    margin: 0 5px 0 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    /* background-color: #e18e00; */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color:#191919;
    border: 1.5px solid #191919;
    transition: all 0.3s ease;
}
.options .option-button{
    margin: 0 5px 0 5px;  
    padding: 5px;  
    border-radius: 15px;    
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color:#191919;
    border: 1.5px solid #191919;
    transition: all 0.3s ease;
}
.options .option-btn:hover,.options .option-button:hover{
    background-color: #191919;
    color:#fff!important;
}
</style>