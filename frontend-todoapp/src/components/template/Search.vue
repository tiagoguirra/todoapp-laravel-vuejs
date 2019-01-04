<template>
  <form class="content-form content-search" @submit.prevent="onSubmit">
    <div class="nav-search">
      <div class="nav-search-group">
        <input
        :placeholder="placeholder"
        v-model="searchBy"
        maxlength="120"
        autofocus
        autocomplete="on"
        class="nav-search-input"
        @keydown.esc="escKey"
        @keydown.ctrl.enter="saveIt">
      </div>
      <div class="nav-search-buttons">
        <button
          type="button"
          class="nav-search-clear-btn"
          @click="clearSearch"
          v-if="searchBy" :style="{'right':clearStyle}"><i class="fas fa-times"></i></button>
        <button type="submit" class="nav-search-btn" :style="{'right':searchStyle}" v-if="search">
          <i class="fas fa-search"></i>
        </button>
        <button type="button" class="nav-search-add-btn" :style="{'right':addStyle}" v-if="add">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>    
  </form>
</template>

<script>
export default {
  props:{
    search:{
      type:Boolean,
      default:true
    },
    add:{
      type:Boolean,
      default:false
    }
  },
  data: () => ({
    searchBy: "",
    addStyle:0,
    searchStyle:0,
    clearStyle:0,
    placeholder:'Search'
  }),
  methods: {
    onSubmit() {      
      if(this.search){
        this.$emit("searchBy", this.searchBy);
      }else{
        this.$emit("addIt", this.searchBy);  
      }
    },
    saveIt(){            
      this.$emit("addIt", this.searchBy);
      this.searchBy = "";
    },
    clearSearch() {      
      this.searchBy = "";
      this.$emit("clear");
    },
    escKey(){
      this.clearSearch()
    }
  },
  mounted(){
    if(this.search){
      this.clearStyle = '43px'
      this.addStyle ='43px'      
    }    
    if(this.add){      
      if(this.search){
        this.placeholder = 'Search or add'
        this.clearStyle = '83px'
      }else{
        this.placeholder = 'Add'
        this.clearStyle = '43px'
      }
    }
  }
};
</script>

<style>
.content-form.content-search{
  margin: 0px;  
}
.nav-search {
  position: relative;
  will-change: left;
  -webkit-transition: left 0.15s ease-out;
  transition: left 0.15s ease-out;
}
.nav-search-input {
  height: 50px;
  padding: 7px 80px 9px 15px;
  -webkit-border-radius: 2px;
  border-radius: 2px;
  background-color: #fff;
  border: 1px solid #d2d2d2;
  position: relative;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  color: #333;
  font-size: 16px;
  width: 100%;
  margin: 0;
  font-family: inherit;
  border-radius:15px;
}
.nav-search-input:focus{
  outline: none;
}

.nav-search-add-btn,
.nav-search-clear-btn,
.nav-search-btn {
  background-image: none;
  height: 39px;
  padding-top: 2px;
  cursor: pointer;
  outline: 0;
  left: auto;
  border-radius: 0 2px 2px 0;
  width: 46px;
  border: 0 rgba(0, 0, 0, 0.2);
  background-color: #fff;
  top: 8px;
  right: 1px;
  z-index: 9;
  position: absolute;
  padding: 0;
  background: 0 0;
  font-size: 22px;
  color: #666;
  line-height: 1em;
}

button.nav-search-add-btn:before,
button.nav-search-add-btn:focus:before,
button.nav-search-btn:before,
button.nav-search-btn:focus:before {
  content: "";
  display: block;
  height: 26px;
  border-left: 1px solid #e6e6e6;
  position: absolute;
  top: 6.5px;
}
</style>
